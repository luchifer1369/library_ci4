<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_lengkap'        => 'Admin M-Library',
                'email'               => 'admin@mlibrary.com',
                'password'            => password_hash('password123', PASSWORD_DEFAULT),
                'role'                => 'admin',
                'is_premium'          => false,
                'premium_expired_at'  => null,
                'free_trial_used'     => false,
                'poin'                => 0,
                'last_login_date'     => null,
                'auto_deduct_enabled' => false,
            ],
            [
                'nama_lengkap'        => 'User Normal',
                'email'               => 'user@mlibrary.com',
                'password'            => password_hash('password123', PASSWORD_DEFAULT),
                'role'                => 'user',
                'is_premium'          => false,
                'premium_expired_at'  => null,
                'free_trial_used'     => false,
                'poin'                => 100, // 100 points for testing
                'last_login_date'     => null,
                'auto_deduct_enabled' => false,
            ],
            [
                'nama_lengkap'        => 'User Premium',
                'email'               => 'premium@mlibrary.com',
                'password'            => password_hash('password123', PASSWORD_DEFAULT),
                'role'                => 'user',
                'is_premium'          => true,
                'premium_expired_at'  => date('Y-m-d H:i:s', strtotime('+3 days')), // 3 days free trial
                'free_trial_used'     => true,
                'poin'                => 0,
                'last_login_date'     => null,
                'auto_deduct_enabled' => false,
            ],
        ];

        // Insert batch to users table
        $this->db->table('users')->insertBatch($data);
    }
}
