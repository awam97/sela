<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Settings extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Default Index: Redirect to Academic Settings
     */
    public function index()
    {
        return redirect()->to(site_url('admin/settings/academic'));
    }

    /**
     * View Academic Settings (Weekends per School, etc.)
     */
    public function academic()
    {
        $schoolId = session()->get('school_id') ?? 1;
        
        // Fetch specific school configuration
        $school = $this->db->table('schools')->where('ID', $schoolId)->get()->getRowArray();
        
        $data = [
            'off_days' => $school['weekends'] ?? 'friday',
            'page_title' => 'الإعدادات الأكاديمية',
            'active_menu' => 'settings'
        ];

        return view('admin/settings/academic', $data);
    }

    /**
     * Update Academic Settings for the current school
     */
    public function updateAcademic()
    {
        $schoolId = session()->get('school_id') ?? 1;
        $days = $this->request->getPost('off_days') ?? [];
        $off_days_string = implode(',', $days);

        // Update the schools table directly
        $this->db->table('schools')
                 ->where('ID', $schoolId)
                 ->update(['weekends' => $off_days_string]);

        return redirect()->to(site_url('admin/settings/academic'))->with('success', 'تم تحديث إعدادات المدرسة بنجاح');
    }
}
