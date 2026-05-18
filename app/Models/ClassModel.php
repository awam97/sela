<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassModel extends Model
{
    protected $table      = 'class';
    protected $primaryKey = 'class_id';
    protected $allowedFields = ['name', 'school', 'study_system', 'balance'];
}
