<?php

namespace App\Models;

use CodeIgniter\Model;

class DailyQuestModel extends Model
{
    protected $table            = 'daily_quests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'quest_date',
        'quest_1_claimed',
        'pages_read_today',
        'quest_2_claimed',
        'quest_3_claimed'
    ];
}
