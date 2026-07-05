<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\NotificationModel;
use App\Models\TransactionModel;

class AuthController extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return $this->redirectBasedOnRole(session()->get('role'));
        }
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel         = new UserModel();
        $notificationModel = new NotificationModel();

        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        // Proactive check: check if premium expired
        $isPremium = $user['is_premium'];
        if ($isPremium && !empty($user['premium_expired_at'])) {
            if (strtotime($user['premium_expired_at']) < time()) {
                $userModel->update($user['id'], ['is_premium' => false]);
                $isPremium = false;

                // Send premium expired notification
                $notificationModel->insert([
                    'user_id' => $user['id'],
                    'title'   => 'Masa Aktif Premium Habis',
                    'message' => 'Masa aktif akun premium Anda telah berakhir. Silakan beli paket premium kembali di halaman profil.',
                    'is_read' => false,
                ]);
            }
        }

        // Daily Login Reward Check
        $today = date('Y-m-d');
        $bonusClaimed = false;

        if (empty($user['last_login_date']) || $user['last_login_date'] !== $today) {
            // Give daily reward
            $newPoints = $user['poin'] + 10;
            $userModel->update($user['id'], [
                'last_login_date' => $today,
                'poin'            => $newPoints,
            ]);

            // Record transaction
            $transactionModel = new TransactionModel();
            $transactionModel->insert([
                'user_id'     => $user['id'],
                'type'        => 'topup',
                'amount'      => 10,
                'description' => 'Daily Login Reward +10 Poin',
            ]);

            // Record notification
            $notificationModel->insert([
                'user_id' => $user['id'],
                'title'   => 'Bonus Login Harian',
                'message' => 'Selamat! Anda mendapatkan bonus login harian sebesar 10 poin.',
                'is_read' => false,
            ]);

            $bonusClaimed = true;
        } else {
            // Just update last login date if needed (e.g. keep current date)
            $userModel->update($user['id'], [
                'last_login_date' => $today,
            ]);
        }

        // Get updated user data
        $updatedUser = $userModel->find($user['id']);

        // Set session
        session()->set([
            'userId'       => $updatedUser['id'],
            'nama_lengkap' => $updatedUser['nama_lengkap'],
            'email'        => $updatedUser['email'],
            'role'         => $updatedUser['role'],
            'is_premium'   => (bool)$updatedUser['is_premium'],
            'isLoggedIn'   => true,
        ]);

        if ($bonusClaimed) {
            session()->setFlashdata('success', 'Selamat Datang! Anda mendapatkan bonus login harian +10 Poin.');
        } else {
            session()->setFlashdata('success', 'Selamat datang kembali, ' . $updatedUser['nama_lengkap'] . '!');
        }

        return $this->redirectBasedOnRole($updatedUser['role']);
    }

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return $this->redirectBasedOnRole(session()->get('role'));
        }
        return view('auth/register');
    }

    public function attemptRegister()
    {
        $rules = [
            'nama_lengkap'     => 'required|min_length[3]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]|max_length[255]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();

        $data = [
            'nama_lengkap'        => $this->request->getPost('nama_lengkap'),
            'email'               => $this->request->getPost('email'),
            'password'            => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'                => 'user',
            'is_premium'          => true, // 3 days free trial premium on first register
            'free_trial_used'     => true,
            'premium_expired_at'  => date('Y-m-d H:i:s', strtotime('+3 days')),
            'poin'                => 0,
            'last_login_date'     => null,
            'auto_deduct_enabled' => false,
        ];

        $userModel->insert($data);

        // Send a welcome notification for the new user
        $newUserId = $userModel->getInsertID();
        $notificationModel = new NotificationModel();
        $notificationModel->insert([
            'user_id' => $newUserId,
            'title'   => 'Selamat Datang di M-Library!',
            'message' => 'Akun Anda telah berhasil dibuat. Anda mendapatkan Free Trial Premium selama 3 hari. Selamat membaca!',
            'is_read' => false,
        ]);

        return redirect()->to('login')->with('success', 'Pendaftaran berhasil! Silakan login untuk masuk.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login')->with('success', 'Anda telah berhasil logout.');
    }

    private function redirectBasedOnRole($role)
    {
        if ($role === 'admin') {
            return redirect()->to('admin');
        }
        return redirect()->to('dashboard');
    }
}
