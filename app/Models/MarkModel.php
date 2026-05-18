<?php

namespace App\Models;

use CodeIgniter\Model;

class MarkModel extends Model
{
    protected $table      = 'mark';
    protected $primaryKey = 'mark_id';

    protected $returnType     = 'array';
    protected $allowedFields = ['student_id', 'subject_id', 'class_id', 'section_id', 'exam_id', 'marks_json', 'mark_obtained', 'mark_coursework', 'mark_total', 'comment', 'year', 'school', 'approval'];

    protected $useTimestamps = false;
}
