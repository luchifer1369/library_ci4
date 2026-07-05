<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nama_kategori' => 'Fiksi & Novel (Fiction)'],
            ['nama_kategori' => 'Pengembangan Diri (Self-Improvement)'],
            ['nama_kategori' => 'Bisnis & Keuangan (Business & Finance)'],
            ['nama_kategori' => 'Komik & Novel Grafis (Comics & Graphic Novels)'],
            ['nama_kategori' => 'Biografi & Autobiografi (Biography)'],
            ['nama_kategori' => 'Buku Anak & Edukasi (Children & Educational)'],
        ];

        // Simple insert batch to categories table
        $this->db->table('categories')->insertBatch($data);
    }
}
