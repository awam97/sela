<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth/login')->with('error', 'يجب تسجيل الدخول كمسؤول مدرسة أولاً.');
        }

        // Refresh recent activity cookie to start the 15-minute countdown from the last action
        $userId = session()->get('user_id');
        $db = \Config\Database::connect();
        $user = $db->table('admin')->where('admin_id', $userId)->get()->getRowArray();
        if ($user) {
            $timestamp = time();
            $role = 'admin';
            $signature = hash_hmac('sha256', "$userId:$role:$timestamp", $user['password']);
            $cookieValue = "$userId:$role:$timestamp:$signature";
            setcookie('sela_session_tracker', $cookieValue, time() + (86400 * 30), '/');
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
