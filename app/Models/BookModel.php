<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table            = 'books';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'title',
        'description',
        'category_id',
        'cover_image',
        'file_pdf',
        'total_pages',
        'free_page_start',
        'free_page_end',
        'views'
    ];
}
