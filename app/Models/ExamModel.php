<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamModel extends Model
{
    protected $table      = 'exam';
    protected $primaryKey = 'exam_id';

    protected $returnType     = 'array';
    protected $allowedFields = ['name', 'year', 'school', 'type'];

    protected $useTimestamps = false;
}
