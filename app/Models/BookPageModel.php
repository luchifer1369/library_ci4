<?php

namespace App\Models;

use CodeIgniter\Model;

class BookPageModel extends Model
{
    protected $table            = 'book_pages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'book_id',
        'page_number',
        'image_path'
    ];
}
