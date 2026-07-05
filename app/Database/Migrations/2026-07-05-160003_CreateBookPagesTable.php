<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookPagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'book_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'page_number' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'image_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('book_id', 'books', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('book_pages');
    }

    public function down()
    {
        $this->forge->dropTable('book_pages');
    }
}
