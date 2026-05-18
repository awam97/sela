<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table      = 'student';
    protected $primaryKey = 'student_id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['name', 'phone', 'sex', 'username', 'password', 'parent_id', 'class_id', 'section_id', 'school', 'activate', 'activate_date', 'mother', 'nationalid', 'birthday', 'image'];

    protected $useTimestamps = false;
}
