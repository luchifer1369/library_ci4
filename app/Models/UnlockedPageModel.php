<?php

namespace App\Models;

use CodeIgniter\Model;

class UnlockedPageModel extends Model
{
    protected $table            = 'unlocked_pages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'book_id',
        'page_number'
    ];
}
