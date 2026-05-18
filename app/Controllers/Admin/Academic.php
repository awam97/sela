<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ExamModel;
use App\Models\MarkModel;
use App\Models\SubjectDistributionModel;
use App\Models\SubjectMarkModel;

class Academic extends BaseController
{
    protected $examModel;
    protected $markModel;
    protected $distributionModel;
    protected $subjectMarkModel;

    public function __construct()
    {
        $this->examModel = new ExamModel();
        $this->markModel = new MarkModel();
        $this->distributionModel = new SubjectDistributionModel();
        $this->subjectMarkModel = new SubjectMarkModel();
    }

    public function index()
    {
        return redirect()->to('/admin/academic/exams');
    }

    /**
     * List all exams
     */
    public function exams()
    {
        $schoolId = $this->schoolId;
        $year     = $this->currentYear;

        $data = [
            'title' => 'إدارة الامتحانات',
            'page_title' => 'جداول ومواعيد الامتحانات',
            'active_menu' => 'academic',
            'exams' => $this->examModel->where('school', $schoolId)->where('year', $year)->findAll()
        ];

        return view('admin/academic/exams_list', $data);
    }

    /**
     * Create new exam
     */
    public function exams_create()
    {
        $schoolId = $this->schoolId;
        $year     = $this->currentYear;

        if ($this->request->getMethod() === 'post') {
            $data = [
                'name' => $this->request->getPost('name'),
                'date' => $this->request->getPost('date'),
                'category' => $this->request->getPost('category') ?? 's1',
                'status' => 1,
                'school' => $schoolId,
                'year' => $year
            ];

            if ($this->examModel->insert($data)) {
                return redirect()->to('/admin/academic/exams')->with('success', 'تم إضافة الامتحان بنجاح');
            }
            return redirect()->back()->with('error', 'فشل إضافة الامتحان');
        }

        $data = [
            'title' => 'إضافة امتحان',
            'page_title' => 'تحديد موعد امتحان جديد',
            'active_menu' => 'academic'
        ];

        return view('admin/academic/exams_create', $data);
    }

    /**
     * Edit exam
     */
    public function exams_edit($id)
    {
        $exam = $this->examModel->find($id);
        if (!$exam) return redirect()->back()->with('error', 'الامتحان غير موجود');

        if ($this->request->getMethod() === 'post') {
            $data = [
                'name' => $this->request->getPost('name'),
                'date' => $this->request->getPost('date'),
                'category' => $this->request->getPost('category') ?? 's1'
            ];

            if ($this->examModel->update($id, $data)) {
                return redirect()->to('/admin/academic/exams')->with('success', 'تم تحديث البيانات بنجاح');
            }
            return redirect()->back()->with('error', 'فشل التحديث');
        }

        $data = [
            'title' => 'تعديل امتحان',
            'page_title' => 'تحديث بيانات الامتحان',
            'active_menu' => 'academic',
            'exam' => $exam
        ];

        return view('admin/academic/exams_edit', $data);
    }

    /**
     * Delete exam
     */
    public function exams_delete($id)
    {
        if ($this->examModel->delete($id)) {
            return redirect()->to('/admin/academic/exams')->with('success', 'تم حذف الامتحان');
        }
        return redirect()->back()->with('error', 'فشل الحذف');
    }

    /**
     * Marks Management
     */
    public function marks()
    {
        $db = \Config\Database::connect();
        $classId = $this->request->getGet('class_id') ? (int)$this->request->getGet('class_id') : null;
        $sectionId = $this->request->getGet('section_id') ? (int)$this->request->getGet('section_id') : null;
        $subjectId = $this->request->getGet('subject_id') ? (int)$this->request->getGet('subject_id') : null;
        
        $schoolId = (int)$this->schoolId;
        $year = $db->escapeString($this->currentYear);

        // Auto-migration: Create table if not exists using app context
        $db->query("CREATE TABLE IF NOT EXISTS subject_marks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            subject_id INT NOT NULL,
            class_id INT NOT NULL,
            section_id INT NOT NULL,
            school_id INT NOT NULL,
            year VARCHAR(20) NOT NULL,
            marks_json LONGTEXT,
            total_obtained FLOAT DEFAULT 0,
            total_possible FLOAT DEFAULT 100,
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX (student_id), INDEX (subject_id), INDEX (school_id), INDEX (year)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $data = [
            'title' => 'رصد الدرجات',
            'page_title' => 'إدارة نتائج الطلاب',
            'active_menu' => 'academic',
            'classes' => $db->table('class')->where('school', $schoolId)->get()->getResultArray(),
            'selected' => [
                'class_id' => $classId,
                'section_id' => $sectionId,
                'subject_id' => $subjectId
            ],
            'students' => [],
            'current_year' => $year,
            'exams' => [], // Safety fallback
            'current_exam' => [] // Safety fallback
        ];

        if ($classId && $sectionId && $subjectId) {
            // Load students from enroll table joining with NEW subject_marks table
            $data['students'] = $db->table('student s')
                ->select('s.student_id, s.name, m.total_obtained as mark_obtained, m.comment, m.id as mark_id, m.marks_json')
                ->join('enroll e', 'e.student_id = s.student_id')
                ->join('subject_marks m', "m.student_id = s.student_id AND m.subject_id = $subjectId AND m.year = '$year'", 'left')
                ->where('e.class_id', $classId)
                ->where('e.section_id', $sectionId)
                ->where('e.year', $year)
                ->get()->getResultArray();
            
            $data['distribution'] = $this->distributionModel->getForSubject($subjectId, $year);
            $data['current_subject'] = $db->table('subject')->where('subject_id', $subjectId)->get()->getRowArray();
            
            $data['current_class'] = $db->table('class')->where(['class_id' => $classId, 'school' => $schoolId])->get()->getRowArray();
            $data['subjects'] = $db->table('subject')->where(['class_id' => $classId, 'school' => $schoolId])->get()->getResultArray();
            $data['sections'] = $db->table('section')->where(['class_id' => $classId, 'school' => $schoolId])->get()->getResultArray();
        }

        return view('admin/academic/marks', $data);
    }

    /**
     * Save/Update student marks
     */
    public function marks_save()
    {
        $schoolId = $this->schoolId;
        $year = $this->currentYear;

        $studentIds = $this->request->getPost('student_id');
        $marks = $this->request->getPost('mark_obtained');
        $totals = $this->request->getPost('mark_total');
        
        $classId = $this->request->getPost('class_id');
        $sectionId = $this->request->getPost('section_id');
        $subjectId = $this->request->getPost('subject_id');

        // Fetch distribution components to map them
        $distribution = $this->distributionModel->getForSubject($subjectId);
        $dynamicMarks = $this->request->getPost('dynamic_mark'); // Array of [component_id => [marks]]

        foreach ($studentIds as $index => $studentId) {
            // Prepare JSON data for this student
            $studentJsonData = [];
            $totalMark = 0;
            if (!empty($distribution)) {
                foreach ($distribution as $dist) {
                    $val = $dynamicMarks[$dist['id']][$index] ?? 0;
                    $studentJsonData[$dist['name']] = $val;
                    $totalMark += (float)$val;
                }
            }

            $check = $this->subjectMarkModel->where([
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'year' => $year
            ])->first();

            $markData = [
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'class_id' => $classId,
                'section_id' => $sectionId,
                'school_id' => $schoolId,
                'marks_json' => json_encode($studentJsonData),
                'total_obtained' => $totalMark,
                'total_possible' => $totals[$index] ?? 100,
                'comment' => $this->request->getPost('comment')[$index] ?? '',
                'year' => $year
            ];

            if ($check) {
                $this->subjectMarkModel->update($check['id'], $markData);
            } else {
                $this->subjectMarkModel->insert($markData);
            }
        }

        return redirect()->to("/admin/academic/marks?class_id=$classId&section_id=$sectionId&subject_id=$subjectId")
            ->with('success', 'تم حفظ الدرجات بنجاح');
    }
}
