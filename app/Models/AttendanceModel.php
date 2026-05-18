<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table            = 'attendance';
    protected $primaryKey       = 'attendance_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'school', 
        'timestamp', 
        'year', 
        'student_id', 
        'status', 
        'notes'
    ];

    /**
     * Get attendance for multiple students on a specific date
     */
    public function getAttendanceForStudents(array $studentIds, $timestamp, $schoolId)
    {
        if (empty($studentIds)) return [];
        
        return $this->whereIn('student_id', $studentIds)
                    ->where('timestamp', $timestamp)
                    ->where('school', $schoolId)
                    ->findAll();
    }
}
