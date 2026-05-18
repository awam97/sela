<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SubjectModel;
use App\Models\ClassModel;
use App\Models\TeacherModel;

class Subjects extends BaseController
{
    protected $subjectModel;
    protected $classModel;
    protected $teacherModel;
    protected $subjectDistributionModel;

    public function __construct()
    {
        $this->subjectModel = new SubjectModel();
        $this->classModel = new \App\Models\ClassModel();
        $this->teacherModel = new \App\Models\TeacherModel();
        $this->subjectDistributionModel = new \App\Models\SubjectDistributionModel();

        // Auto-migration: Ensure distribution table exists
        $db = \Config\Database::connect();
        $db->query("CREATE TABLE IF NOT EXISTS subject_mark_distribution (
            id INT AUTO_INCREMENT PRIMARY KEY,
            subject_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            max_mark FLOAT NOT NULL,
            year VARCHAR(20) DEFAULT NULL,
            INDEX (subject_id),
            INDEX (year)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // Migration: Add year column if it doesn't exist
        try {
            $db->query("ALTER TABLE subject_mark_distribution ADD COLUMN year VARCHAR(20) DEFAULT NULL AFTER max_mark");
        } catch (\Exception $e) {}
    }

    /**
     * List subjects for a specific class
     */
    public function index($classId = null)
    {
        if (!$classId) {
            return redirect()->to(site_url('admin/academic'))->with('error', 'يرجى اختيار الصف أولاً');
        }

        $session = session();
        $schoolId = $session->get('school_id') ?? 1;
        $currentYear = $session->get('running_year') ?? '2025-2026';

        $data['subjects'] = $this->subjectModel->getSubjectsWithTeachers($classId, $schoolId, $currentYear);
        $db = \Config\Database::connect();
        $data['teachers'] = $db->table('teacher')->get()->getResultArray();
        $data['class_id'] = $classId;
        $data['active_menu'] = 'academic';
        $data['page_title'] = 'إدارة المواد الدراسية';

        return view('admin/subjects/list', $data);
    }

    /**
     * Create a new subject
     */
    public function create($classId)
    {
        $session = session();
        
        if ($this->request->is('post')) {
            $data = [
                'name'       => $this->request->getPost('name'),
                'class_id'   => $classId,
                'teacher_id' => $this->request->getPost('teacher_id') ?: null,
                'school'     => $session->get('school_id') ?? 1,
                'year'       => $session->get('running_year') ?? '2025-2026',
                'total_mark' => $this->request->getPost('total_mark') ?? 100,
                'pass_mark'  => $this->request->getPost('pass_mark') ?? 50,
                'sort'       => $this->request->getPost('sort') ?? 1,
            ];

            if ($this->subjectModel->insert($data)) {
                $subjectId = $this->subjectModel->insertID();
                
                // Handle Mark Distribution
                $distributionNames = (array)$this->request->getPost('dist_name');
                $distributionMarks = (array)$this->request->getPost('dist_mark');
                
                if (!empty($distributionNames)) {
                    foreach ($distributionNames as $key => $name) {
                        if (!empty($name) && isset($distributionMarks[$key])) {
                            $this->subjectDistributionModel->insert([
                                'subject_id' => $subjectId,
                                'name'       => $name,
                                'max_mark'   => $distributionMarks[$key],
                                'year'       => $data['year']
                            ]);
                        }
                    }
                }

                return redirect()->to(site_url('admin/subjects/index/' . $classId))->with('success', 'تم إضافة المادة وتوزيع الدرجات بنجاح');
            }
            return redirect()->back()->with('error', 'فشل في إضافة المادة')->withInput();
        }

        // Fallback for GET request: Redirect to list since we now use modals
        return redirect()->to(site_url('admin/subjects/index/' . $classId));
    }

    /**
     * Edit a subject
     */
    public function edit($subjectId)
    {
        $subject = $this->subjectModel->find($subjectId);
        if (!$subject) {
            return redirect()->back()->with('error', 'المادة غير موجودة');
        }

        if ($this->request->is('post')) {
            $data = [
                'name'       => $this->request->getPost('name'),
                'teacher_id' => $this->request->getPost('teacher_id') ?: null,
                'total_mark' => $this->request->getPost('total_mark'),
                'pass_mark'  => $this->request->getPost('pass_mark'),
                'sort'       => $this->request->getPost('sort'),
            ];

            if ($this->subjectModel->update($subjectId, $data)) {
                // Handle Mark Distribution
                $distributionNames = (array)$this->request->getPost('dist_name');
                $distributionMarks = (array)$this->request->getPost('dist_mark');
                
                $this->subjectDistributionModel->where('subject_id', $subjectId)->delete();
                
                if (!empty($distributionNames)) {
                    foreach ($distributionNames as $key => $name) {
                        if (!empty($name) && isset($distributionMarks[$key])) {
                            $this->subjectDistributionModel->insert([
                                'subject_id' => $subjectId,
                                'name'       => $name,
                                'max_mark'   => $distributionMarks[$key],
                                'year'       => $subject['year']
                            ]);
                        }
                    }
                }

                return redirect()->to(site_url('admin/subjects/index/' . $subject['class_id']))->with('success', 'تم تحديث المادة وتوزيع الدرجات بنجاح');
            }
            return redirect()->back()->with('error', 'فشل في تحديث المادة')->withInput();
        }

        // Fallback for GET request: Redirect to list since we now use modals
        return redirect()->to(site_url('admin/subjects/index/' . $subject['class_id']));
    }

    /**
     * Delete a subject
     */
    public function delete($subjectId, $classId)
    {
        if ($this->subjectModel->delete($subjectId)) {
            return redirect()->to(site_url('admin/subjects/index/' . $classId))->with('success', 'تم حذف المادة بنجاح');
        }
        return redirect()->to(site_url('admin/subjects/index/' . $classId))->with('error', 'فشل في حذف المادة');
    }

    /**
     * Get subject details for AJAX (Modal)
     */
    public function get_details($subjectId)
    {
        $subject = $this->subjectModel->find($subjectId);
        if (!$subject) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'المادة غير موجودة']);
        }

        $distributions = $this->subjectDistributionModel->getForSubject($subjectId, $subject['year']);

        return $this->response->setJSON([
            'status' => 'success',
            'subject' => $subject,
            'distributions' => $distributions
        ]);
    }

    /**
     * Update subjects order (Drag & Drop)
     */
    public function update_order()
    {
        if ($this->request->is('post')) {
            $orderArr = $this->request->getPost('order');
            
            if (!empty($orderArr) && is_array($orderArr)) {
                $db = \Config\Database::connect();
                foreach ($orderArr as $index => $id) {
                    if (!empty($id)) {
                        $db->table('subject')
                          ->where('subject_id', (int)$id)
                          ->update(['sort' => $index + 1]);
                    }
                }
                return $this->response->setJSON(['status' => 'success', 'message' => 'تم تحديث الترتيب بنجاح']);
            }
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'لم يتم استلام بيانات الترتيب']);
    }
}
