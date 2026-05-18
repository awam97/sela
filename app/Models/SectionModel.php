<?php

namespace App\Models;

use CodeIgniter\Model;

class SectionModel extends Model
{
    protected $table      = 'section';
    protected $primaryKey = 'section_id';
    protected $allowedFields = ['name', 'class_id', 'school'];
}
