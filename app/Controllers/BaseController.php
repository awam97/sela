<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = ['form', 'url'];
    protected $session;
    protected $db;
    protected $currentYear;
    protected $schoolId = 18; // Defaulting to the school ID found in the dump

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();

        // 1. Manage Active Year Session logic
        if (!$this->session->get('active_year')) {
            $school = $this->db->table('schools')->where('ID', $this->schoolId)->get()->getRowArray();
            $defaultYear = $school ? $school['year'] : '2025-2026';
            $this->session->set('active_year', $defaultYear);
            $this->session->set('running_year', $defaultYear); // Complementary key
        }
        
        if (!$this->session->get('school_id')) {
            $this->session->set('school_id', $this->schoolId);
        }

        $this->currentYear = $this->session->get('active_year');

        // 2. Fetch all available academic years for the switcher
        $availableYears = $this->db->table('enroll')->select('year')->distinct()->orderBy('year', 'DESC')->get()->getResultArray();
        $yearsList = array_column($availableYears, 'year');
        if (!in_array($this->currentYear, $yearsList)) {
            $yearsList[] = $this->currentYear;
        }
        sort($yearsList);
        $yearsList = array_reverse($yearsList);

        // 3. Fetch System Settings
        $settings = $this->db->table('settings')->get()->getResultArray();
        $systemSettings = [];
        foreach ($settings as $row) {
            $systemSettings[$row['type']] = $row['description'];
        }

        // 4. Registration Requests Count (for Sidebar badge)
        $registrationCount = 0;
        if (session()->get('role') === 'admin') {
            $registrationCount = $this->db->table('registration_requests')
                ->where('school_id', session()->get('school_id'))
                ->where('status', 'pending')
                ->countAllResults();
        }

        // 5. Share data with all views globally
        $view = \Config\Services::renderer();
        $view->setData([
            'current_year' => $this->currentYear,
            'available_years' => $yearsList,
            'system_name' => $systemSettings['system_name'] ?? 'Sela Platform',
            'system_settings' => $systemSettings,
            'pending_registrations_count' => $registrationCount
        ], 'raw');
    }
}
