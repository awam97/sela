<?php

namespace App\Models;

use CodeIgniter\Model;

class SchoolModel extends Model
{
    protected $table = 'schools';
    protected $primaryKey = 'ID';

    protected $returnType = 'array';
    protected $allowedFields = ['name', 'year', 'address', 'phone', 'email', 'manager', 'status'];

    protected $useTimestamps = false;
}
