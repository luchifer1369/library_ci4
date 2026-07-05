<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDailyQuestsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'quest_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'quest_1_claimed' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'pages_read_today' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'quest_2_claimed' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'quest_3_claimed' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('daily_quests');
    }

    public function down()
    {
        $this->forge->dropTable('daily_quests');
    }
}
