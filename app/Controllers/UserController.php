<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\CategoryModel;
use App\Models\DailyQuestModel;
use App\Models\FavoriteModel;
use App\Models\NotificationModel;
use App\Models\ReadingHistoryModel;
use App\Models\TransactionModel;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected $helpers = ['url', 'form'];

    private const TOPUP_PACKAGES = [
        'pemula'    => ['points' => 50,  'price' => 5000,  'label' => 'Paket Pemula',    'bonus' => 0],
        'reguler'   => ['points' => 160, 'price' => 15000, 'label' => 'Paket Reguler',   'bonus' => 10],
        'kutubuku'  => ['points' => 330, 'price' => 30000, 'label' => 'Paket Kutu Buku', 'bonus' => 30],
        'sultan'    => ['points' => 575, 'price' => 50000, 'label' => 'Paket Sultan',    'bonus' => 75],
    ];

    public function dashboard()
    {
        $userId        = session()->get('userId');
        $user          = $this->getFreshUser($userId);
        $bookModel     = new BookModel();
        $categoryModel = new CategoryModel();
        $search        = $this->request->getGet('search');
        $categoryId    = $this->request->getGet('category');

        $builder = $bookModel->select('books.*, categories.nama_kategori')
            ->join('categories', 'categories.id = books.category_id');

        if ($search) {
            $builder->like('books.title', $search);
        }
        if ($categoryId) {
            $builder->where('books.category_id', $categoryId);
        }

        $quest           = $this->getTodayQuest($userId);
        $favoriteModel   = new FavoriteModel();
        $favoritesToday  = $favoriteModel->where('user_id', $userId)
            ->where('created_at >=', date('Y-m-d') . ' 00:00:00')
            ->where('created_at <=', date('Y-m-d') . ' 23:59:59')
            ->countAllResults();

        $data = [
            'title'          => 'Dashboard',
            'user'           => $user,
            'books'          => $builder->orderBy('books.created_at', 'DESC')->findAll(),
            'categories'     => $categoryModel->findAll(),
            'search'         => $search,
            'categoryId'     => $categoryId,
            'quest'          => $quest,
            'favoritesToday' => $favoritesToday,
            'notifications'  => $this->getNotifications($userId),
            'unreadCount'    => $this->getUnreadCount($userId),
        ];

        return view('user/dashboard', $data);
    }

    public function topup()
    {
        $userId = session()->get('userId');

        return view('user/topup', [
            'title'    => 'Top Up Poin',
            'user'     => $this->getFreshUser($userId),
            'packages' => self::TOPUP_PACKAGES,
            'notifications' => $this->getNotifications($userId),
            'unreadCount'   => $this->getUnreadCount($userId),
        ]);
    }

    public function purchaseTopup()
    {
        $packageKey = $this->request->getPost('package');
        if (!isset(self::TOPUP_PACKAGES[$packageKey])) {
            return redirect()->back()->with('error', 'Paket tidak valid.');
        }

        $package       = self::TOPUP_PACKAGES[$packageKey];
        $userId        = session()->get('userId');
        $userModel     = new UserModel();
        $user          = $userModel->find($userId);
        $newPoints     = $user['poin'] + $package['points'];

        $userModel->update($userId, ['poin' => $newPoints]);

        $transactionModel = new TransactionModel();
        $transactionModel->insert([
            'user_id'     => $userId,
            'type'        => 'topup',
            'amount'      => $package['points'],
            'description' => $package['label'] . ' - Rp ' . number_format($package['price'], 0, ',', '.'),
        ]);

        $notificationModel = new NotificationModel();
        $notificationModel->insert([
            'user_id' => $userId,
            'title'   => 'Top Up Berhasil',
            'message' => 'Pembelian ' . $package['label'] . ' berhasil! +' . $package['points'] . ' poin telah ditambahkan ke akun Anda.',
            'is_read' => false,
        ]);

        return redirect()->to('dashboard')->with('success', 'Top up berhasil! +' . $package['points'] . ' poin.');
    }

    public function claimQuest($questNum)
    {
        $userId     = session()->get('userId');
        $quest      = $this->getTodayQuest($userId);
        $userModel  = new UserModel();
        $user       = $userModel->find($userId);
        $questModel = new DailyQuestModel();
        $reward     = 0;
        $field      = '';

        switch ((int) $questNum) {
            case 1:
                $favoriteModel = new FavoriteModel();
                $count = $favoriteModel->where('user_id', $userId)
                    ->where('created_at >=', date('Y-m-d') . ' 00:00:00')
                    ->where('created_at <=', date('Y-m-d') . ' 23:59:59')
                    ->countAllResults();
                if ($count < 1) {
                    return redirect()->back()->with('error', 'Quest belum selesai. Tambahkan 1 buku ke favorit.');
                }
                if ($quest['quest_1_claimed']) {
                    return redirect()->back()->with('error', 'Quest sudah diklaim.');
                }
                $reward = 10;
                $field  = 'quest_1_claimed';
                break;

            case 2:
                if ($quest['pages_read_today'] < 5) {
                    return redirect()->back()->with('error', 'Quest belum selesai. Baca 5 halaman terlebih dahulu.');
                }
                if ($quest['quest_2_claimed']) {
                    return redirect()->back()->with('error', 'Quest sudah diklaim.');
                }
                $reward = 10;
                $field  = 'quest_2_claimed';
                break;

            case 3:
                if ($quest['pages_read_today'] < 15) {
                    return redirect()->back()->with('error', 'Quest belum selesai. Baca 15 halaman terlebih dahulu.');
                }
                if ($quest['quest_3_claimed']) {
                    return redirect()->back()->with('error', 'Quest sudah diklaim.');
                }
                $reward = 20;
                $field  = 'quest_3_claimed';
                break;

            default:
                return redirect()->back()->with('error', 'Quest tidak valid.');
        }

        $questModel->update($quest['id'], [$field => true]);
        $userModel->update($userId, ['poin' => $user['poin'] + $reward]);

        $transactionModel = new TransactionModel();
        $transactionModel->insert([
            'user_id'     => $userId,
            'type'        => 'topup',
            'amount'      => $reward,
            'description' => 'Daily Quest Reward +' . $reward . ' Poin',
        ]);

        return redirect()->back()->with('success', 'Quest berhasil diklaim! +' . $reward . ' poin.');
    }

    public function favorites()
    {
        $userId = session()->get('userId');
        $favoriteModel = new FavoriteModel();

        $favorites = $favoriteModel->select('favorites.*, books.title, books.cover_image, books.description, categories.nama_kategori')
            ->join('books', 'books.id = favorites.book_id')
            ->join('categories', 'categories.id = books.category_id')
            ->where('favorites.user_id', $userId)
            ->orderBy('favorites.created_at', 'DESC')
            ->findAll();

        return view('user/favorites', [
            'title'         => 'Favorit',
            'user'          => $this->getFreshUser($userId),
            'favorites'     => $favorites,
            'notifications' => $this->getNotifications($userId),
            'unreadCount'   => $this->getUnreadCount($userId),
        ]);
    }

    public function history()
    {
        $userId = session()->get('userId');
        $historyModel = new ReadingHistoryModel();

        $histories = $historyModel->select('reading_history.*, books.title, books.cover_image, categories.nama_kategori')
            ->join('books', 'books.id = reading_history.book_id')
            ->join('categories', 'categories.id = books.category_id')
            ->where('reading_history.user_id', $userId)
            ->orderBy('reading_history.updated_at', 'DESC')
            ->findAll();

        return view('user/history', [
            'title'         => 'Riwayat Baca',
            'user'          => $this->getFreshUser($userId),
            'histories'     => $histories,
            'notifications' => $this->getNotifications($userId),
            'unreadCount'   => $this->getUnreadCount($userId),
        ]);
    }

    public function profile()
    {
        $userId = session()->get('userId');
        $transactionModel = new TransactionModel();

        return view('user/profile', [
            'title'         => 'Profil',
            'user'          => $this->getFreshUser($userId),
            'transactions'  => $transactionModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->limit(10)->findAll(),
            'notifications' => $this->getNotifications($userId),
            'unreadCount'   => $this->getUnreadCount($userId),
        ]);
    }

    public function claimPremiumTrial()
    {
        $userId    = session()->get('userId');
        $userModel = new UserModel();
        $user      = $userModel->find($userId);

        if ($user['free_trial_used']) {
            return redirect()->back()->with('error', 'Free trial sudah pernah digunakan.');
        }

        $userModel->update($userId, [
            'is_premium'         => true,
            'free_trial_used'    => true,
            'premium_expired_at' => date('Y-m-d H:i:s', strtotime('+3 days')),
        ]);

        session()->set([
            'is_premium' => true,
            'poin'       => $user['poin'],
        ]);

        $notificationModel = new NotificationModel();
        $notificationModel->insert([
            'user_id' => $userId,
            'title'   => 'Premium Free Trial Aktif',
            'message' => 'Selamat! Free Trial Premium 3 hari telah aktif. Nikmati akses penuh ke semua buku!',
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Free Trial Premium 3 hari berhasil diaktifkan!');
    }

    public function purchasePremium()
    {
        $userId    = session()->get('userId');
        $userModel = new UserModel();
        $user      = $userModel->find($userId);
        $cost      = 200;

        if ($user['poin'] < $cost) {
            return redirect()->back()->with('error', 'Poin tidak cukup. Dibutuhkan ' . $cost . ' poin untuk Premium 30 Hari.');
        }

        $expiredAt = date('Y-m-d H:i:s', strtotime('+30 days'));
        if ($user['is_premium'] && !empty($user['premium_expired_at']) && strtotime($user['premium_expired_at']) > time()) {
            $expiredAt = date('Y-m-d H:i:s', strtotime($user['premium_expired_at'] . ' +30 days'));
        }

        $userModel->update($userId, [
            'is_premium'         => true,
            'poin'               => $user['poin'] - $cost,
            'premium_expired_at' => $expiredAt,
        ]);

        session()->set([
            'is_premium' => true,
            'poin'       => $user['poin'] - $cost,
        ]);
        $transactionModel->insert([
            'user_id'     => $userId,
            'type'        => 'premium',
            'amount'      => $cost,
            'description' => 'Pembelian Premium 30 Hari',
        ]);

        $notificationModel = new NotificationModel();
        $notificationModel->insert([
            'user_id' => $userId,
            'title'   => 'Premium Berhasil Dibeli',
            'message' => 'Paket Premium 30 Hari berhasil diaktifkan. Selamat membaca tanpa batas!',
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Premium 30 Hari berhasil dibeli!');
    }

    public function markNotificationRead($id)
    {
        $notificationModel = new NotificationModel();
        $notification    = $notificationModel->where('id', $id)->where('user_id', session()->get('userId'))->first();

        if ($notification) {
            $notificationModel->update($id, ['is_read' => true]);
        }

        return redirect()->back();
    }

    public function markAllNotificationsRead()
    {
        $notificationModel = new NotificationModel();
        $notificationModel->where('user_id', session()->get('userId'))->set(['is_read' => true])->update();

        return redirect()->back();
    }

    private function getTodayQuest(int $userId): array
    {
        $questModel = new DailyQuestModel();
        $today      = date('Y-m-d');
        $quest      = $questModel->where('user_id', $userId)->where('quest_date', $today)->first();

        if (!$quest) {
            $questModel->insert([
                'user_id'          => $userId,
                'quest_date'       => $today,
                'quest_1_claimed'  => false,
                'pages_read_today' => 0,
                'quest_2_claimed'  => false,
                'quest_3_claimed'  => false,
            ]);
            $quest = $questModel->where('user_id', $userId)->where('quest_date', $today)->first();
        }

        return $quest;
    }
}
