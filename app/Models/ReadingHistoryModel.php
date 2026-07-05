<?php

namespace App\Models;

use CodeIgniter\Model;

class ReadingHistoryModel extends Model
{
    protected $table            = 'reading_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'book_id',
        'last_read_page'
    ];
}
