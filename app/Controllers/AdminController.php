<?php

namespace App\Controllers;

use App\Libraries\PdfConverter;
use App\Models\BookModel;
use App\Models\BookPageModel;
use App\Models\CategoryModel;
use App\Models\UserModel;

class AdminController extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function index()
    {
        $bookModel = new BookModel();
        $userModel = new UserModel();

        $data = [
            'title'       => 'Dashboard Admin',
            'totalBooks'  => $bookModel->countAllResults(),
            'totalUsers'  => $userModel->where('role', 'user')->countAllResults(),
            'totalViews'  => (int) $bookModel->selectSum('views')->first()['views'],
            'popularBooks' => $bookModel->select('books.*, categories.nama_kategori')
                ->join('categories', 'categories.id = books.category_id')
                ->orderBy('views', 'DESC')
                ->limit(5)
                ->find(),
        ];

        return view('admin/dashboard', $data);
    }

    public function books()
    {
        $bookModel = new BookModel();

        $data = [
            'title' => 'Manajemen Buku',
            'books' => $bookModel->select('books.*, categories.nama_kategori')
                ->join('categories', 'categories.id = books.category_id')
                ->orderBy('books.created_at', 'DESC')
                ->find(),
        ];

        return view('admin/books/index', $data);
    }

    public function createBook()
    {
        $categoryModel = new CategoryModel();

        return view('admin/books/form', [
            'title'      => 'Tambah Buku',
            'book'       => null,
            'categories' => $categoryModel->findAll(),
        ]);
    }

    public function storeBook()
    {
        $rules = [
            'title'           => 'required|min_length[3]|max_length[255]',
            'description'     => 'required',
            'category_id'     => 'required|integer',
            'cover_image'     => 'uploaded[cover_image]|max_size[cover_image,5120]|is_image[cover_image]',
            'file_pdf'        => 'uploaded[file_pdf]|max_size[file_pdf,51200]|ext_in[file_pdf,pdf]',
            'free_page_start' => 'required|is_natural_no_zero',
            'free_page_end'   => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules, $this->bookValidationMessages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $freePageError = $this->validateFreePageRange();
        if ($freePageError !== null) {
            return redirect()->back()->withInput()->with('errors', ['free_page_end' => $freePageError]);
        }

        $coverFile = $this->request->getFile('cover_image');
        $pdfFile   = $this->request->getFile('file_pdf');

        $coverDir = FCPATH . 'uploads/covers';
        $pdfDir   = FCPATH . 'uploads/pdfs';

        if (!is_dir($coverDir)) {
            mkdir($coverDir, 0755, true);
        }
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0755, true);
        }

        $coverName = $coverFile->getRandomName();
        $pdfName   = $pdfFile->getRandomName();

        $coverFile->move($coverDir, $coverName);
        $pdfFile->move($pdfDir, $pdfName);

        $bookModel = new BookModel();
        $bookModel->insert([
            'title'           => $this->request->getPost('title'),
            'description'     => $this->request->getPost('description'),
            'category_id'     => $this->request->getPost('category_id'),
            'cover_image'     => 'uploads/covers/' . $coverName,
            'file_pdf'        => 'uploads/pdfs/' . $pdfName,
            'total_pages'     => 0,
            'free_page_start' => $this->request->getPost('free_page_start'),
            'free_page_end'   => $this->request->getPost('free_page_end'),
            'views'           => 0,
        ]);

        $bookId = $bookModel->getInsertID();

        try {
            $converter = new PdfConverter();
            $pagesDir  = FCPATH . 'uploads/pages';
            $pages     = $converter->convertToPng($pdfDir . DIRECTORY_SEPARATOR . $pdfName, $pagesDir, $bookId);

            $pageModel = new BookPageModel();
            foreach ($pages as $page) {
                $pageModel->insert([
                    'book_id'     => $bookId,
                    'page_number' => $page['page_number'],
                    'image_path'  => $page['image_path'],
                ]);
            }

            $bookModel->update($bookId, ['total_pages' => count($pages)]);
        } catch (\Throwable $e) {
            $bookModel->delete($bookId);
            return redirect()->back()->withInput()->with('error', 'Gagal konversi PDF: ' . $e->getMessage());
        }

        return redirect()->to('admin/books')->with('success', 'Buku berhasil ditambahkan dan PDF dikonversi ke gambar.');
    }

    public function editBook($id)
    {
        $bookModel     = new BookModel();
        $categoryModel = new CategoryModel();
        $book          = $bookModel->find($id);

        if (!$book) {
            return redirect()->to('admin/books')->with('error', 'Buku tidak ditemukan.');
        }

        return view('admin/books/form', [
            'title'      => 'Edit Buku',
            'book'       => $book,
            'categories' => $categoryModel->findAll(),
        ]);
    }

    public function updateBook($id)
    {
        $bookModel = new BookModel();
        $book      = $bookModel->find($id);

        if (!$book) {
            return redirect()->to('admin/books')->with('error', 'Buku tidak ditemukan.');
        }

        $rules = [
            'title'           => 'required|min_length[3]|max_length[255]',
            'description'     => 'required',
            'category_id'     => 'required|integer',
            'free_page_start' => 'required|is_natural_no_zero',
            'free_page_end'   => 'required|is_natural_no_zero',
        ];

        $coverFile = $this->request->getFile('cover_image');
        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            $rules['cover_image'] = 'max_size[cover_image,5120]|is_image[cover_image]';
        }

        $pdfFile = $this->request->getFile('file_pdf');
        if ($pdfFile && $pdfFile->isValid() && !$pdfFile->hasMoved()) {
            $rules['file_pdf'] = 'max_size[file_pdf,51200]|ext_in[file_pdf,pdf]';
        }

        if (!$this->validate($rules, $this->bookValidationMessages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $freePageError = $this->validateFreePageRange();
        if ($freePageError !== null) {
            return redirect()->back()->withInput()->with('errors', ['free_page_end' => $freePageError]);
        }

        $updateData = [
            'title'           => $this->request->getPost('title'),
            'description'     => $this->request->getPost('description'),
            'category_id'     => $this->request->getPost('category_id'),
            'free_page_start' => $this->request->getPost('free_page_start'),
            'free_page_end'   => $this->request->getPost('free_page_end'),
        ];

        $coverDir = FCPATH . 'uploads/covers';
        $pdfDir   = FCPATH . 'uploads/pdfs';

        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            if (!is_dir($coverDir)) {
                mkdir($coverDir, 0755, true);
            }
            if (!empty($book['cover_image']) && is_file(FCPATH . $book['cover_image'])) {
                unlink(FCPATH . $book['cover_image']);
            }
            $coverName = $coverFile->getRandomName();
            $coverFile->move($coverDir, $coverName);
            $updateData['cover_image'] = 'uploads/covers/' . $coverName;
        }

        $reconvertPdf = false;
        if ($pdfFile && $pdfFile->isValid() && !$pdfFile->hasMoved()) {
            if (!is_dir($pdfDir)) {
                mkdir($pdfDir, 0755, true);
            }
            if (!empty($book['file_pdf']) && is_file(FCPATH . $book['file_pdf'])) {
                unlink(FCPATH . $book['file_pdf']);
            }
            $pdfName = $pdfFile->getRandomName();
            $pdfFile->move($pdfDir, $pdfName);
            $updateData['file_pdf'] = 'uploads/pdfs/' . $pdfName;
            $reconvertPdf           = true;
        }

        $bookModel->update($id, $updateData);

        if ($reconvertPdf) {
            $pageModel = new BookPageModel();
            $oldPages  = $pageModel->where('book_id', $id)->findAll();

            foreach ($oldPages as $oldPage) {
                if (is_file(FCPATH . $oldPage['image_path'])) {
                    unlink(FCPATH . $oldPage['image_path']);
                }
            }
            $pageModel->where('book_id', $id)->delete();

            try {
                $converter = new PdfConverter();
                $pagesDir  = FCPATH . 'uploads/pages';
                $pages     = $converter->convertToPng($pdfDir . DIRECTORY_SEPARATOR . $pdfName, $pagesDir, (int) $id);

                foreach ($pages as $page) {
                    $pageModel->insert([
                        'book_id'     => $id,
                        'page_number' => $page['page_number'],
                        'image_path'  => $page['image_path'],
                    ]);
                }

                $bookModel->update($id, ['total_pages' => count($pages)]);
            } catch (\Throwable $e) {
                return redirect()->back()->withInput()->with('error', 'Gagal konversi PDF: ' . $e->getMessage());
            }
        }

        return redirect()->to('admin/books')->with('success', 'Buku berhasil diperbarui.');
    }

    public function deleteBook($id)
    {
        $bookModel = new BookModel();
        $book      = $bookModel->find($id);

        if (!$book) {
            return redirect()->to('admin/books')->with('error', 'Buku tidak ditemukan.');
        }

        $pageModel = new BookPageModel();
        $pages     = $pageModel->where('book_id', $id)->findAll();

        foreach ($pages as $page) {
            if (is_file(FCPATH . $page['image_path'])) {
                unlink(FCPATH . $page['image_path']);
            }
        }

        if (!empty($book['cover_image']) && is_file(FCPATH . $book['cover_image'])) {
            unlink(FCPATH . $book['cover_image']);
        }
        if (!empty($book['file_pdf']) && is_file(FCPATH . $book['file_pdf'])) {
            unlink(FCPATH . $book['file_pdf']);
        }

        $bookModel->delete($id);

        return redirect()->to('admin/books')->with('success', 'Buku berhasil dihapus.');
    }

    private function bookValidationMessages(): array
    {
        return [
            'free_page_start' => [
                'is_natural_no_zero' => 'Halaman gratis "Dari" harus angka bulat minimal 1.',
            ],
            'free_page_end' => [
                'is_natural_no_zero' => 'Halaman gratis "Sampai" harus angka bulat minimal 1.',
            ],
            'cover_image' => [
                'is_image' => 'Cover harus berupa file gambar (JPG, PNG, GIF, WebP, dll).',
            ],
        ];
    }

    private function validateFreePageRange(): ?string
    {
        $start = (int) trim((string) $this->request->getPost('free_page_start'));
        $end   = (int) trim((string) $this->request->getPost('free_page_end'));

        if ($end < $start) {
            return 'Halaman gratis "Sampai" (' . $end . ') harus sama atau lebih besar dari "Dari" (' . $start . '). Contoh: Dari 1, Sampai 10.';
        }

        return null;
    }
}
