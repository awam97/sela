<?php

namespace App\Models;

use CodeIgniter\Model;

class SuperUserModel extends Model
{
    protected $table = 'super';
    protected $primaryKey = 'super_id';

    protected $returnType = 'array';
    protected $allowedFields = ['name', 'email', 'password', 'phone', 'address', 'owner_status', 'username', 'status', 'class_id'];

    protected $useTimestamps = false;
}
