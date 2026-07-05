<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nama_lengkap',
        'email',
        'password',
        'role',
        'is_premium',
        'premium_expired_at',
        'free_trial_used',
        'poin',
        'last_login_date',
        'auto_deduct_enabled'
    ];
}
