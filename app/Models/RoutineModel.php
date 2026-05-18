<?php

namespace App\Models;

use CodeIgniter\Model;

class RoutineModel extends Model
{
    protected $table      = 'class_routine';
    protected $primaryKey = 'class_routine_id';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = [
        'class_id',
        'section_id',
        'subject_id',
        'time_start',
        'time_end',
        'time_start_min',
        'time_end_min',
        'day',
        'school',
        'year',
        'am_pm'
    ];

    /**
     * Get full routine with subject and teacher details
     */
    public function getRoutineWithDetails($classId, $sectionId, $schoolId = 1, $year = '2026')
    {
        return $this->select('class_routine.*, subject.name as subject_name, teacher.name as teacher_name')
            ->join('subject', 'subject.subject_id = class_routine.subject_id')
            ->join('teacher', 'teacher.teacher_id = subject.teacher_id', 'left')
            ->where('class_routine.class_id', $classId)
            ->where('class_routine.section_id', $sectionId)
            ->where('class_routine.school', $schoolId)
            ->where('class_routine.year', $year)
            ->orderBy('day', 'ASC')
            ->orderBy('time_start', 'ASC')
            ->findAll();
    }
}
