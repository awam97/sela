<?php

namespace App\Models;

use CodeIgniter\Model;

class SubjectDistributionModel extends Model
{
    protected $table      = 'subject_mark_distribution';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $allowedFields = ['subject_id', 'name', 'max_mark', 'year'];

    /**
     * Get distribution for a subject and year
     */
    public function getForSubject($subjectId, $year = null)
    {
        $builder = $this->where('subject_id', $subjectId);
        
        if ($year) {
            $builder->where('year', $year);
        }
        
        return $builder->orderBy('id', 'ASC')->findAll();
    }
}
