<?php

namespace App\Models;

use CodeIgniter\Model;

class SubjectMarkModel extends Model
{
    protected $table      = 'subject_marks';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $allowedFields = [
        'student_id', 'subject_id', 'class_id', 'section_id', 
        'school_id', 'year', 'marks_json', 'total_obtained', 
        'total_possible', 'comment'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
