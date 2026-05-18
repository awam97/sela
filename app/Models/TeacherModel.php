<?php

namespace App\Models;

use CodeIgniter\Model;

class TeacherModel extends Model
{
    protected $table      = 'teacher';
    protected $primaryKey = 'teacher_id';
    protected $allowedFields = ['name', 'birthday', 'sex', 'religion', 'blood_group', 'address', 'phone', 'email', 'password', 'school', 'status'];
}
