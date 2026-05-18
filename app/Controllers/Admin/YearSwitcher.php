<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class YearSwitcher extends BaseController
{
    /**
     * Handle the form submission for switching the academic year.
     */
    public function update()
    {
        $year = $this->request->getPost('year');

        if ($year) {
            $this->session->set('active_year', $year);
        }

        return redirect()->back();
    }
}
