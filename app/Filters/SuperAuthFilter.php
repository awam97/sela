<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SuperAuthFilter implements FilterInterface
{
    /**
     * Ensure the user is logged in as 'super_admin'.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'super_admin') {
            return redirect()->to('/auth/login')->with('error', 'هذه الصلاحية للمدير العام فقط.');
        }

        // Refresh recent activity cookie to start the 15-minute countdown from the last action
        $userId = session()->get('user_id');
        $db = \Config\Database::connect();
        $user = $db->table('super')->where('super_id', $userId)->get()->getRowArray();
        if ($user) {
            $timestamp = time();
            $role = 'super_admin';
            $signature = hash_hmac('sha256', "$userId:$role:$timestamp", $user['password']);
            $cookieValue = "$userId:$role:$timestamp:$signature";
            setcookie('sela_session_tracker', $cookieValue, time() + (86400 * 30), '/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
