<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MaintenanceFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $db = \Config\Database::connect();
        $maintenance = $db->table('settings')->where('type', 'maintenance_mode')->get()->getRow();
        
        $isMaintenance = ($maintenance && strtolower($maintenance->description) === 'true');
        $session = session();
        
        // Fetch custom maintenance bypass key from database settings
        $bypassSetting = $db->table('settings')->where('type', 'maintenance_bypass_key')->get()->getRow();
        $bypassKey = $bypassSetting ? trim($bypassSetting->description) : 'SelaAdminPasscode2026';
        
        // Allow Super Admin to set a bypass flag if they provide the exact matching security key on GET requests
        $userInputKey = $request->getGet('key');
        if (!empty($bypassKey) && !empty($userInputKey) && $userInputKey === $bypassKey) {
            $session->set('super_login_bypass', true);
        }
        
        if ($isMaintenance) {
            $currentPath = trim($request->getUri()->getPath(), '/');
            $isApi = (str_starts_with($currentPath, 'api') || str_contains($currentPath, '/api/'));
            
            // Check if it's an API request
            if ($isApi) {
                // Allow /api/login and /api/verify-otp and /api/send-otp to bypass maintenance check
                // so users can attempt to log in (if they are super_admin)
                $apiAllowedPaths = [
                    'api/login',
                    'api/send-otp',
                    'api/verify-otp'
                ];
                $isApiAllowed = false;
                foreach ($apiAllowedPaths as $allowed) {
                    if (str_contains($currentPath, $allowed)) {
                        $isApiAllowed = true;
                        break;
                    }
                }
                if ($isApiAllowed) {
                    return;
                }

                // For all other API requests, check if a valid Super Admin token is provided
                $authHeader = $request->getServer('HTTP_AUTHORIZATION') ?? $request->header('Authorization');
                $isSuperAdmin = false;
                if ($authHeader) {
                    $authHeaderString = is_object($authHeader) ? $authHeader->getValue() : $authHeader;
                    if (preg_match('/Bearer\s+(.*)$/i', $authHeaderString, $matches)) {
                        $token = $matches[1];
                        $parts = explode(':', $token);
                        if (count($parts) === 3) {
                            list($userId, $role, $signature) = $parts;
                            $tokenSecret = 'sela_secure_api_key_2026';
                            $expectedSignature = hash_hmac('sha256', $userId . ':' . $role, $tokenSecret);
                            if ($signature === $expectedSignature && $role === 'super_admin') {
                                $isSuperAdmin = true;
                            }
                        }
                    }
                }

                if ($isSuperAdmin) {
                    // Super Admin is authenticated and allowed to bypass maintenance mode in API
                    return;
                }

                // Otherwise, return a clean JSON response with 503 status code
                $response = service('response');
                return $response->setJSON([
                    'status' => 'error',
                    'message' => 'النظام في وضع الصيانة حالياً. يرجى المحاولة لاحقاً.'
                ])->setStatusCode(503);
            }

            // 1. If we are already on the maintenance page, let it load! This prevents infinite redirect loops.
            if ($currentPath === 'maintenance') {
                return;
            }
            
            // 2. If logged in but NOT super_admin, destroy session (log out completely) and redirect to maintenance
            if ($session->get('isLoggedIn') && $session->get('role') !== 'super_admin') {
                $session->remove(['isLoggedIn', 'role', 'user_id', 'username', 'name', 'temp_user', 'temp_role', 'super_login_bypass']);
                $session->destroy();
                return redirect()->to('/maintenance')->with('error', 'النظام في وضع الصيانة حالياً. غير مسموح بالدخول لغير المدير العام.');
            }
            
            // 3. If already logged in as Super Admin, fully bypass maintenance
            if ($session->get('role') === 'super_admin') {
                return;
            }
            
            // 4. Whitelisted paths allowed during maintenance to complete Super Admin login and OTP steps
            $allowedPaths = [
                'superadmin/login',
                'auth/select-otp-method',
                'auth/send-selected-otp',
                'auth/verify-otp',
                'auth/logout'
            ];
            
            // 5. Also allow general auth pages if the temporary bypass key is active (?key=...)
            if ($session->get('super_login_bypass') === true) {
                if (str_contains($currentPath, 'auth')) {
                    return;
                }
            }
            
            // 6. Check if current path is a whitelisted login route
            $isAllowed = false;
            foreach ($allowedPaths as $allowed) {
                if (str_contains($currentPath, $allowed)) {
                    $isAllowed = true;
                    break;
                }
            }
            
            if ($isAllowed) {
                return;
            }
            
            // 7. Otherwise, redirect everything else (including normal /auth/login) to /maintenance
            return redirect()->to('/maintenance');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
