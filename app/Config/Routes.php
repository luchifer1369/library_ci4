<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'AuthController::login');

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::attemptRegister');
$routes->get('logout', 'AuthController::logout');

// Admin routes
$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
    $routes->get('/', 'AdminController::index');
    $routes->get('books', 'AdminController::books');
    $routes->get('books/create', 'AdminController::createBook');
    $routes->post('books/store', 'AdminController::storeBook');
    $routes->get('books/edit/(:num)', 'AdminController::editBook/$1');
    $routes->post('books/update/(:num)', 'AdminController::updateBook/$1');
    $routes->get('books/delete/(:num)', 'AdminController::deleteBook/$1');
});

// User routes
$routes->group('', ['filter' => 'user'], static function ($routes) {
    $routes->get('dashboard', 'UserController::dashboard');
    $routes->get('topup', 'UserController::topup');
    $routes->post('topup/purchase', 'UserController::purchaseTopup');
    $routes->get('quest/claim/(:num)', 'UserController::claimQuest/$1');
    $routes->get('favorites', 'UserController::favorites');
    $routes->get('history', 'UserController::history');
    $routes->get('profile', 'UserController::profile');
    $routes->post('profile/premium-trial', 'UserController::claimPremiumTrial');
    $routes->post('profile/premium', 'UserController::purchasePremium');
    $routes->get('notifications/read/(:num)', 'UserController::markNotificationRead/$1');
    $routes->get('notifications/read-all', 'UserController::markAllNotificationsRead');

    // Reading routes
    $routes->get('book/(:num)', 'ReadingController::detail/$1');
    $routes->get('book/(:num)/pages', 'ReadingController::loadMorePages/$1');
    $routes->post('book/(:num)/favorite', 'ReadingController::toggleFavorite/$1');
    $routes->get('book/(:num)/read/(:num)', 'ReadingController::read/$1/$2');
    $routes->post('read/enable-auto-deduct', 'ReadingController::enableAutoDeduct');
    $routes->post('read/unlock-once', 'ReadingController::unlockOnce');
});
