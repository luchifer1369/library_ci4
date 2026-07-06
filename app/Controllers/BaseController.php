<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    protected function getFreshUser(int $userId): array
    {
        $userModel = model(\App\Models\UserModel::class);
        $user      = $userModel->find($userId);

        if ($user['is_premium'] && !empty($user['premium_expired_at']) && strtotime($user['premium_expired_at']) < time()) {
            $userModel->update($userId, ['is_premium' => false]);
            $user['is_premium'] = false;
        }

        $premiumActive = $this->isPremiumActive($user);
        $user['is_premium'] = $premiumActive;

        session()->set([
            'is_premium' => $premiumActive,
            'poin'       => $user['poin'],
        ]);

        return $user;
    }

    protected function isPremiumActive(?array $user): bool
    {
        if (!$user || empty($user['is_premium'])) {
            return false;
        }

        if (empty($user['premium_expired_at'])) {
            return true;
        }

        return strtotime($user['premium_expired_at']) >= time();
    }

    protected function getNotifications(int $userId): array
    {
        return model(\App\Models\NotificationModel::class)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();
    }

    protected function getUnreadCount(int $userId): int
    {
        return model(\App\Models\NotificationModel::class)
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->countAllResults();
    }
}
