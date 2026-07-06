<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\BookPageModel;
use App\Models\DailyQuestModel;
use App\Models\FavoriteModel;
use App\Models\NotificationModel;
use App\Models\ReadingHistoryModel;
use App\Models\TransactionModel;
use App\Models\UnlockedPageModel;
use App\Models\UserModel;

class ReadingController extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function detail($bookId)
    {
        $userId    = session()->get('userId');
        $bookModel = new BookModel();
        $book      = $bookModel->select('books.*, categories.nama_kategori')
            ->join('categories', 'categories.id = books.category_id')
            ->where('books.id', $bookId)
            ->first();

        if (!$book) {
            return redirect()->to('dashboard')->with('error', 'Buku tidak ditemukan.');
        }

        $bookModel->update($bookId, ['views' => $book['views'] + 1]);

        $favoriteModel = new FavoriteModel();
        $isFavorite    = $favoriteModel->where('user_id', $userId)->where('book_id', $bookId)->first() !== null;

        $pageModel = new BookPageModel();
        $pages     = $pageModel->where('book_id', $bookId)->orderBy('page_number', 'ASC')->limit(12)->findAll();

        return view('reading/detail', [
            'title'         => $book['title'],
            'book'          => $book,
            'isFavorite'    => $isFavorite,
            'pages'         => $pages,
            'totalPages'    => $book['total_pages'],
            'user'          => $this->getFreshUser($userId),
            'notifications' => $this->getNotifications($userId),
            'unreadCount'   => $this->getUnreadCount($userId),
        ]);
    }

    public function loadMorePages($bookId)
    {
        $offset    = (int) $this->request->getGet('offset');
        $pageModel = new BookPageModel();
        $pages     = $pageModel->where('book_id', $bookId)
            ->orderBy('page_number', 'ASC')
            ->limit(12, $offset)
            ->findAll();

        return $this->response->setJSON(['pages' => $pages]);
    }

    public function toggleFavorite($bookId)
    {
        $userId        = session()->get('userId');
        $favoriteModel = new FavoriteModel();
        $existing      = $favoriteModel->where('user_id', $userId)->where('book_id', $bookId)->first();

        if ($existing) {
            $favoriteModel->delete($existing['id']);
            return redirect()->back()->with('success', 'Buku dihapus dari favorit.');
        }

        $favoriteModel->insert(['user_id' => $userId, 'book_id' => $bookId]);
        return redirect()->back()->with('success', 'Buku ditambahkan ke favorit.');
    }

    public function read($bookId, $pageNum = 1)
    {
        $userId    = session()->get('userId');
        $pageNum   = (int) $pageNum;
        $bookModel = new BookModel();
        $book      = $bookModel->find($bookId);

        if (!$book) {
            return redirect()->to('dashboard')->with('error', 'Buku tidak ditemukan.');
        }

        $pageModel = new BookPageModel();
        $page      = $pageModel->where('book_id', $bookId)->where('page_number', $pageNum)->first();

        if (!$page) {
            return redirect()->to('book/' . $bookId)->with('error', 'Halaman tidak ditemukan.');
        }

        $user       = $this->getFreshUser($userId);
        $access     = $this->checkPageAccess($user, $book, $pageNum);
        $fromNext   = $this->request->getGet('from') === 'next';

        if (!$access['allowed']) {
            if ($access['needs_modal']) {
                return view('reading/read', [
                    'title'       => $book['title'],
                    'book'        => $book,
                    'page'        => $page,
                    'pageNum'     => $pageNum,
                    'totalPages'  => $book['total_pages'],
                    'user'        => $user,
                    'showModal'   => true,
                    'blocked'     => true,
                    'prevPage'    => max(1, $pageNum - 1),
                    'nextPage'    => min($book['total_pages'], $pageNum + 1),
                ]);
            }
            return redirect()->to('book/' . $bookId)->with('error', $access['message']);
        }

        $this->updateReadingHistory($userId, $bookId, $pageNum);

        if ($fromNext) {
            $this->incrementPagesReadToday($userId);
        }

        return view('reading/read', [
            'title'      => $book['title'] . ' - Halaman ' . $pageNum,
            'book'       => $book,
            'page'       => $page,
            'pageNum'    => $pageNum,
            'totalPages' => $book['total_pages'],
            'user'       => $user,
            'showModal'  => false,
            'blocked'    => false,
            'prevPage'   => max(1, $pageNum - 1),
            'nextPage'   => min($book['total_pages'], $pageNum + 1),
        ]);
    }

    public function enableAutoDeduct()
    {
        $userId    = session()->get('userId');
        $userModel = new UserModel();
        $userModel->update($userId, ['auto_deduct_enabled' => true]);

        $bookId  = (int) $this->request->getPost('book_id');
        $pageNum = (int) $this->request->getPost('page_number');

        return $this->unlockAndRedirect($bookId, $pageNum, true);
    }

    public function unlockOnce()
    {
        $bookId  = (int) $this->request->getPost('book_id');
        $pageNum = (int) $this->request->getPost('page_number');

        return $this->unlockAndRedirect($bookId, $pageNum, false);
    }

    private function unlockAndRedirect(int $bookId, int $pageNum, bool $enableAutoDeduct)
    {
        $userId    = session()->get('userId');
        $userModel = new UserModel();
        $bookModel = new BookModel();
        $user      = $userModel->find($userId);
        $book      = $bookModel->find($bookId);

        if ($enableAutoDeduct) {
            $userModel->update($userId, ['auto_deduct_enabled' => true]);
            $user['auto_deduct_enabled'] = true;
        }

        if ($user['poin'] < 1) {
            return redirect()->to('book/' . $bookId . '/read/' . $pageNum)->with('error', 'Poin tidak cukup untuk membuka halaman ini.');
        }

        $this->deductAndUnlock($userId, $bookId, $pageNum, $user);

        return redirect()->to('book/' . $bookId . '/read/' . $pageNum . '?from=next');
    }

    /**
     * @return array{allowed: bool, needs_modal: bool, message: string}
     */
    private function checkPageAccess(array $user, array $book, int $pageNum): array
    {
        if ($this->isPremiumActive($user)) {
            return ['allowed' => true, 'needs_modal' => false, 'message' => ''];
        }

        if ($pageNum >= $book['free_page_start'] && $pageNum <= $book['free_page_end']) {
            return ['allowed' => true, 'needs_modal' => false, 'message' => ''];
        }

        $unlockedModel = new UnlockedPageModel();
        $unlocked      = $unlockedModel->where('user_id', $user['id'])
            ->where('book_id', $book['id'])
            ->where('page_number', $pageNum)
            ->first();

        if ($unlocked) {
            return ['allowed' => true, 'needs_modal' => false, 'message' => ''];
        }

        if ($user['auto_deduct_enabled']) {
            if ($user['poin'] < 1) {
                return ['allowed' => false, 'needs_modal' => false, 'message' => 'Poin habis. Silakan top up untuk melanjutkan membaca.'];
            }
            $this->deductAndUnlock($user['id'], $book['id'], $pageNum, $user);
            return ['allowed' => true, 'needs_modal' => false, 'message' => ''];
        }

        return ['allowed' => false, 'needs_modal' => true, 'message' => 'Halaman berbayar. Aktifkan auto-deduct atau bayar 1 poin.'];
    }

    private function deductAndUnlock(int $userId, int $bookId, int $pageNum, array $user): void
    {
        $userModel       = new UserModel();
        $unlockedModel   = new UnlockedPageModel();
        $transactionModel = new TransactionModel();

        $existing = $unlockedModel->where('user_id', $userId)
            ->where('book_id', $bookId)
            ->where('page_number', $pageNum)
            ->first();

        if ($existing) {
            return;
        }

        $userModel->update($userId, ['poin' => $user['poin'] - 1]);

        $unlockedModel->insert([
            'user_id'     => $userId,
            'book_id'     => $bookId,
            'page_number' => $pageNum,
        ]);

        $transactionModel->insert([
            'user_id'     => $userId,
            'type'        => 'spend',
            'amount'      => 1,
            'description' => 'Buka halaman ' . $pageNum . ' buku #' . $bookId,
        ]);
    }

    private function updateReadingHistory(int $userId, int $bookId, int $pageNum): void
    {
        $historyModel = new ReadingHistoryModel();
        $existing     = $historyModel->where('user_id', $userId)->where('book_id', $bookId)->first();

        if ($existing) {
            $historyModel->update($existing['id'], ['last_read_page' => $pageNum]);
        } else {
            $historyModel->insert([
                'user_id'        => $userId,
                'book_id'        => $bookId,
                'last_read_page' => $pageNum,
            ]);
        }
    }

    private function incrementPagesReadToday(int $userId): void
    {
        $questModel = new DailyQuestModel();
        $today      = date('Y-m-d');
        $quest      = $questModel->where('user_id', $userId)->where('quest_date', $today)->first();

        if (!$quest) {
            $questModel->insert([
                'user_id'          => $userId,
                'quest_date'       => $today,
                'quest_1_claimed'  => false,
                'pages_read_today' => 1,
                'quest_2_claimed'  => false,
                'quest_3_claimed'  => false,
            ]);
        } else {
            $questModel->update($quest['id'], ['pages_read_today' => $quest['pages_read_today'] + 1]);
        }
    }

}
