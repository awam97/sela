<?php

namespace App\Models;

use CodeIgniter\Model;

class CityModel extends Model
{
    protected $table = 'cities';
    protected $primaryKey = 'ID';

    protected $returnType = 'array';
    protected $allowedFields = ['name'];

    protected $useTimestamps = false;
}
