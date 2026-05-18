<?php

namespace App\Models;

use CodeIgniter\Model;

class HomeworkModel extends Model
{
    protected $table      = 'homeworks';
    protected $primaryKey = 'ID';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'title', 
        'description', 
        'timestamp', 
        'subject_id', 
        'class_id', 
        'school', 
        'year'
    ];

    protected $useTimestamps = false;

    /**
     * Get homework for a specific class, school, and year
     */
    public function getHomeworkByClass($classId, $schoolId = 1, $year = '2026')
    {
        return $this->select('homeworks.*, subject.name as subject_name')
            ->join('subject', 'subject.subject_id = homeworks.subject_id', 'left')
            ->where('homeworks.class_id', $classId)
            ->where('homeworks.school', $schoolId)
            ->where('homeworks.year', $year)
            ->orderBy('homeworks.timestamp', 'DESC')
            ->findAll();
    }
}
