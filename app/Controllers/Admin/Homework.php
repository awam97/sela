<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HomeworkModel;
use App\Models\SubjectModel;

class Homework extends BaseController
{
    protected $homeworkModel;
    protected $subjectModel;
    protected $session;
    protected $currentYear;
    protected $schoolId;

    public function __construct()
    {
        $this->homeworkModel = new HomeworkModel();
        $this->subjectModel = new SubjectModel();
        $this->session = session();
        $this->currentYear = $this->session->get('running_year') ?? '2026';
        $this->schoolId = $this->session->get('school_id') ?? 1;
    }

    /**
     * List homework for a specific class
     */
    public function index($classId = null)
    {
        if (!$classId) {
            return redirect()->to('/admin/academic')->with('error', 'يرجى اختيار الصف الدراسي أولاً');
        }

        $data = [
            'homeworks' => $this->homeworkModel->getHomeworkByClass($classId, $this->schoolId, $this->currentYear),
            'class_id' => $classId,
            'title' => 'الواجبات المنزلية',
            'page_title' => 'إدارة الواجبات والمهام اليومية',
            'active_menu' => 'academic'
        ];

        return view('admin/homework/index', $data);
    }

    /**
     * Form to create new homework
     */
    public function create($classId)
    {
        if ($this->request->getMethod() === 'post') {
            $data = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'timestamp' => strtotime($this->request->getPost('date')),
                'subject_id' => $this->request->getPost('subject_id'),
                'class_id' => $classId,
                'school' => $this->schoolId,
                'year' => $this->currentYear
            ];

            if ($this->homeworkModel->insert($data)) {
                return redirect()->to('/admin/homework/index/' . $classId)->with('success', 'تم إضافة الواجب بنجاح');
            }
            return redirect()->back()->with('error', 'فشل إضافة الواجب')->withInput();
        }

        $data = [
            'subjects' => $this->subjectModel->where('class_id', $classId)->where('school', $this->schoolId)->findAll(),
            'class_id' => $classId,
            'title' => 'إضافة واجب جديد',
            'page_title' => 'تحديد مهمة منزلية جديدة',
            'active_menu' => 'academic'
        ];

        return view('admin/homework/create', $data);
    }

    /**
     * Form to edit existing homework
     */
    public function edit($id)
    {
        $homework = $this->homeworkModel->find($id);
        if (!$homework) {
            return redirect()->back()->with('error', 'الواجب غير موجود');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'timestamp' => strtotime($this->request->getPost('date')),
                'subject_id' => $this->request->getPost('subject_id')
            ];

            if ($this->homeworkModel->update($id, $data)) {
                return redirect()->to('/admin/homework/index/' . $homework['class_id'])->with('success', 'تم تحديث الواجب بنجاح');
            }
            return redirect()->back()->with('error', 'فشل التحديث')->withInput();
        }

        $data = [
            'homework' => $homework,
            'subjects' => $this->subjectModel->where('class_id', $homework['class_id'])->where('school', $this->schoolId)->findAll(),
            'class_id' => $homework['class_id'],
            'title' => 'تعديل الواجب',
            'page_title' => 'تحديث بيانات المهمة المنزلية',
            'active_menu' => 'academic'
        ];

        return view('admin/homework/edit', $data);
    }

    /**
     * Delete homework
     */
    public function delete($id, $classId)
    {
        if ($this->homeworkModel->delete($id)) {
            return redirect()->to('/admin/homework/index/' . $classId)->with('success', 'تم حذف الواجب بنجاح');
        }
        return redirect()->to('/admin/homework/index/' . $classId)->with('error', 'فشل حذف الواجب');
    }
}
