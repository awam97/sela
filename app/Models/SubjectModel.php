<?php

namespace App\Models;

use CodeIgniter\Model;

class SubjectModel extends Model
{
    protected $table      = 'subject';
    protected $primaryKey = 'subject_id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name',
        'class_id',
        'teacher_id',
        'school',
        'year',
        'total_mark',
        'pass_mark',
        'sort'
    ];

    protected $useTimestamps = false;

    /**
     * Get subjects with teacher names for a class
     */
    public function getSubjectsWithTeachers($classId, $schoolId = 1, $year = '2026')
    {
        return $this->select('subject.*, teacher.name as teacher_name')
            ->join('teacher', 'teacher.teacher_id = subject.teacher_id', 'left')
            ->where('subject.class_id', $classId)
            ->where('subject.school', $schoolId)
            ->where('subject.year', $year)
            ->orderBy('sort', 'ASC')
            ->findAll();
    }
}
