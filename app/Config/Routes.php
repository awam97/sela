<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::landing');
$routes->get('about', 'Auth::about');
$routes->get('contact', 'Auth::contact');
$routes->get('school-registration', 'Auth::schoolRegistration');
$routes->post('school-registration', 'Auth::postSchoolRegistration');
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::postLogin');
$routes->get('superadmin/login', 'Auth::superLogin');
$routes->post('superadmin/login', 'Auth::postSuperLogin');
$routes->get('auth/select-otp-method', 'Auth::selectOtpMethod');
$routes->post('auth/send-selected-otp', 'Auth::sendSelectedOtp');
$routes->get('auth/verify-otp', 'Auth::verifyOtpPage');
$routes->post('auth/verify-otp', 'Auth::processOtpVerify');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('maintenance', 'Auth::maintenance');
$routes->get('auth/view-logs', 'Auth::viewLogs');
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->post('switch_year', 'YearSwitcher::update');

    // Students
    $routes->get('students', 'Students::index');
    $routes->post('students/selector', 'Students::selector');
    $routes->get('students/list/(:num)/(:any)', 'Students::list/$1/$2');
    $routes->get('students/pics/(:num)/(:any)', 'Students::pics/$1/$2');
    $routes->post('students/pics/(:num)/(:any)', 'Students::postPics/$1/$2');
    $routes->get('students/sections/(:num)/(:any)', 'Students::sections/$1/$2');
    $routes->get('students/entrance_cards/(:num)/(:any)', 'Students::entranceCards/$1/$2');
    $routes->get('students/access_cards/(:num)/(:any)', 'Students::accessCards/$1/$2');
    $routes->get('students/create', 'Students::create');
    $routes->post('students/create', 'Students::postCreate');
    $routes->get('students/get_sections/(:num)', 'Students::getSections/$1');
    $routes->get('students/get_subjects/(:num)', 'Students::getSubjects/$1');
    $routes->get('students/get_sections_grid/(:num)', 'Students::getSectionsGrid/$1');
    $routes->get('students/get_subjects_grid/(:num)', 'Students::getSubjectsGrid/$1');
    $routes->get('students/get_classes_all', 'Students::get_classes_all');
    $routes->post('students/edit/(:num)', 'Students::postEdit/$1');
    $routes->get('students/profile/(:num)', 'Students::profile/$1');
    $routes->get('students/delete/(:num)', 'Students::delete/$1');

    // Teachers
    $routes->get('teachers', 'Teachers::index');
    $routes->get('teachers/create', 'Teachers::create');
    $routes->post('teachers/create', 'Teachers::postCreate');
    $routes->get('teachers/edit/(:num)', 'Teachers::edit/$1');
    $routes->post('teachers/edit/(:num)', 'Teachers::postEdit/$1');
    $routes->get('teachers/delete/(:num)', 'Teachers::delete/$1');

    // Academic & Exams
    $routes->get('academic/exams', 'Academic::exams');
    $routes->get('academic/exams/create', 'Academic::exams_create');
    $routes->post('academic/exams/create', 'Academic::exams_create');
    $routes->get('academic/exams/edit/(:num)', 'Academic::exams_edit/$1');
    $routes->post('academic/exams/edit/(:num)', 'Academic::exams_edit/$1');
    $routes->get('academic/exams/delete/(:num)', 'Academic::exams_delete/$1');

    $routes->get('academic/marks', 'Academic::marks');
    $routes->post('academic/marks/save', 'Academic::marks_save');

    // Finance
    $routes->get('finance', 'Finance::index');
    $routes->get('finance/invoices', 'Finance::index');

    // Reports Hub
    $routes->get('reports', 'Reports::index');
    $routes->get('reports/marksheet/(:num)/(:any)', 'Reports::marksheet/$1/$2');
    $routes->get('reports/marksheet/(:num)/(:any)', 'Reports::marksheet/$1/$2');

    // Settings
    $routes->get('settings', 'Settings::index');
    $routes->get('settings/academic', 'Settings::academic');
    $routes->post('settings/update_academic', 'Settings::updateAcademic');

    // Routines (Timetables)
    $routes->get('routines/view/(:num)/(:num)', 'Routines::view/$1/$2');
    $routes->post('routines/save', 'Routines::save');
    $routes->get('routines/delete/(:num)/(:num)/(:num)', 'Routines::delete/$1/$2/$3');

    // Subjects
    $routes->get('subjects/index/(:num)', 'Subjects::index/$1');
    $routes->get('subjects/create/(:num)', 'Subjects::create/$1');
    $routes->post('subjects/create/(:num)', 'Subjects::create/$1');
    $routes->get('subjects/get_details/(:num)', 'Subjects::get_details/$1');
    $routes->post('subjects/edit/(:num)', 'Subjects::edit/$1');
    $routes->post('subjects/update_order', 'Subjects::update_order');
    $routes->get('subjects/delete/(:num)/(:num)', 'Subjects::delete/$1/$2');

    // Add more admin routes here
    // Homework Management
    $routes->get('homework/index/(:num)', 'Homework::index/$1');
    $routes->get('homework/create/(:num)', 'Homework::create/$1');
    $routes->post('homework/create/(:num)', 'Homework::create/$1');
    $routes->get('homework/edit/(:num)', 'Homework::edit/$1');
    $routes->post('homework/edit/(:num)', 'Homework::edit/$1');
    $routes->get('homework/delete/(:num)/(:num)', 'Homework::delete/$1/$2');

    // Classes & Sections
    $routes->get('classes', 'Classes::index');
    $routes->get('classes/create', 'Classes::create');
    $routes->post('classes/create', 'Classes::postCreate');
    $routes->get('classes/edit/(:num)', 'Classes::edit/$1');
    $routes->post('classes/edit/(:num)', 'Classes::postEdit/$1');
    $routes->get('classes/delete/(:num)', 'Classes::delete/$1');

    $routes->get('sections/create/(:num)', 'Classes::section_create/$1');
    $routes->post('sections/create/(:num)', 'Classes::section_postCreate/$1');
    $routes->get('sections/edit/(:num)', 'Classes::section_edit/$1');
    $routes->post('sections/edit/(:num)', 'Classes::section_postEdit/$1');
    $routes->get('sections/delete/(:num)', 'Classes::section_delete/$1');

    // Attendance
    $routes->get('attendance', 'Attendance::index');
    $routes->get('attendance/take', 'Attendance::take');
    $routes->post('attendance/save', 'Attendance::save');

    // Registration Management
    $routes->get('registrations', 'Registrations::index');
    $routes->post('registrations/approve/(:num)', 'Registrations::approve/$1');
    $routes->post('registrations/reject/(:num)', 'Registrations::reject/$1');
});

// Super Admin Group
$routes->group('superadmin', ['namespace' => 'App\Controllers\SuperAdmin', 'filter' => 'superauth'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Cities
    $routes->get('cities', 'Cities::index');
    $routes->post('cities/create', 'Cities::create');
    $routes->post('cities/update/(:num)', 'Cities::update/$1');
    $routes->get('cities/delete/(:num)', 'Cities::delete/$1');

    // Schools
    $routes->get('schools', 'Schools::index');
    $routes->get('schools/create', 'Schools::create');
    $routes->post('schools/create', 'Schools::create');
    $routes->get('schools/edit/(:num)', 'Schools::edit/$1');
    $routes->post('schools/edit/(:num)', 'Schools::edit/$1');
    $routes->get('schools/delete/(:num)', 'Schools::delete/$1');

    // Admins
    $routes->get('admins', 'Admins::index');
    $routes->get('admins/create', 'Admins::create');
    $routes->post('admins/create', 'Admins::create');
    $routes->get('admins/edit/(:num)', 'Admins::edit/$1');
    $routes->post('admins/edit/(:num)', 'Admins::edit/$1');
    $routes->get('admins/delete/(:num)', 'Admins::delete/$1');

    // Global Settings
    $routes->get('settings', 'Settings::index');
    $routes->post('settings/update', 'Settings::update');
    $routes->get('settings/repair', 'Settings::repair');

    // Student Center Ordering
    $routes->get('student-center', 'StudentCenter::index');
    $routes->post('student-center/save-order', 'StudentCenter::saveOrder');

    // Registration Management (New)
    $routes->get('registrations', 'Registrations::index');
    $routes->post('registrations/approve/(:num)', 'Registrations::approve/$1');
    $routes->post('registrations/reject/(:num)', 'Registrations::reject/$1');
});

/**
 * Public Registration Routes
 */
$routes->get('register', 'Auth::register');
$routes->get('about', 'Auth::about');
$routes->get('get-classes/(:num)', 'Auth::getClasses/$1');
$routes->post('register/send-otp', 'Auth::sendOtp');
$routes->post('register/verify-otp', 'Auth::verifyOtp');
$routes->post('register/submit', 'Auth::submitRegistration');

/**
 * Native Flutter Mobile Application API Routes
 */
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->get('/', 'MobileApi::index');
    $routes->get('', 'MobileApi::index');
    $routes->post('login', 'MobileApi::login');
    $routes->post('send-otp', 'MobileApi::sendOtp');
    $routes->post('verify-otp', 'MobileApi::verifyOtp');
    $routes->get('dashboard', 'MobileApi::dashboard');
    $routes->get('students', 'MobileApi::students');
    $routes->get('subjects', 'MobileApi::subjects');
    $routes->post('students/create', 'MobileApi::createStudent');
    $routes->post('students/edit/(:num)', 'MobileApi::editStudent/$1');
    $routes->post('students/delete/(:num)', 'MobileApi::deleteStudent/$1');
    $routes->get('students/identify/(:num)', 'MobileApi::identifyStudent/$1');
    $routes->post('attendance/save', 'MobileApi::saveAttendance');
    $routes->get('registrations', 'MobileApi::registrations');
    $routes->post('registrations/approve/(:num)', 'MobileApi::approveRegistration/$1');
    $routes->post('registrations/reject/(:num)', 'MobileApi::rejectRegistration/$1');
    $routes->get('profile', 'MobileApi::profile');
    $routes->post('profile/update', 'MobileApi::updateProfile');
    $routes->get('finance/invoices', 'MobileApi::invoices');
    $routes->get('finance/invoice/print/(:num)', 'MobileApi::printInvoice/$1');
    $routes->get('academic/marks/options', 'MobileApi::marksOptions');
    $routes->get('academic/marks/list', 'MobileApi::marksList');
    $routes->post('academic/marks/save', 'MobileApi::saveMarks');
    $routes->post('students/upload_photo', 'MobileApi::uploadStudentPhoto');

    // Super Admin Cities Management
    $routes->get('superadmin/cities', 'MobileApi::getCities');
    $routes->post('superadmin/cities/create', 'MobileApi::createCity');
    $routes->post('superadmin/cities/edit/(:num)', 'MobileApi::updateCity/$1');
    $routes->post('superadmin/cities/delete/(:num)', 'MobileApi::deleteCity/$1');

    // Super Admin Schools Management
    $routes->get('superadmin/schools', 'MobileApi::getSchools');
    $routes->post('superadmin/schools/create', 'MobileApi::createSchool');
    $routes->post('superadmin/schools/edit/(:num)', 'MobileApi::updateSchool/$1');
    $routes->post('superadmin/schools/delete/(:num)', 'MobileApi::deleteSchool/$1');

    // Super Admin Admins Management
    $routes->get('superadmin/admins', 'MobileApi::getAdmins');
    $routes->post('superadmin/admins/create', 'MobileApi::createAdmin');
    $routes->post('superadmin/admins/edit/(:num)', 'MobileApi::updateAdmin/$1');
    $routes->post('superadmin/admins/delete/(:num)', 'MobileApi::deleteAdmin/$1');

    // Super Admin Settings Management
    $routes->get('superadmin/settings', 'MobileApi::getSettings');
    $routes->post('superadmin/settings/update', 'MobileApi::updateSettings');
});

