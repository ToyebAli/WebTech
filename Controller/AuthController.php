<?php
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private User $model;
    public function __construct() { $this->model = new User(); }

    public function registerForm(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!empty($_SESSION['user_id'])) {
            redirect($_SESSION['role'] === 'admin' ? '/admin/products' : '/products');
        }
        $errors    = $_SESSION['errors'] ?? [];
        $old       = $_SESSION['old']    ?? [];
        $pageTitle = 'Create Account';
        unset($_SESSION['errors'], $_SESSION['old']);
        include __DIR__ . '/../views/auth/register.php';
    }

    public function register(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        csrf_verify();
        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $phone    = trim($_POST['phone']    ?? '');
        $password = $_POST['password']      ?? '';
        $confirm  = $_POST['confirm']       ?? '';
        $errors = [];
        if ($name === '') $errors['name'] = 'Full name is required.';
        elseif (strlen($name) > 100) $errors['name'] = 'Name must be 100 characters or less.';
        if ($email === '') $errors['email'] = 'Email address is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email address.';
        elseif ($this->model->findByEmail($email)) $errors['email'] = 'This email address is already registered.';
        if (strlen($password) < 8) $errors['password'] = 'Password must be at least 8 characters.';
        if ($password !== $confirm) $errors['confirm'] = 'Passwords do not match.';
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = compact('name', 'email', 'phone');
            redirect('/register');
        }
        $this->model->create($name, $email, $password, $phone);
        flash('success', 'Account created! Please sign in.');
        redirect('/login');
    }

    public function loginForm(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!empty($_SESSION['user_id'])) {
            redirect($_SESSION['role'] === 'admin' ? '/admin/products' : '/products');
        }
        $errors    = $_SESSION['errors'] ?? [];
        $old       = $_SESSION['old']    ?? [];
        $pageTitle = 'Sign In';
        unset($_SESSION['errors'], $_SESSION['old']);
        include __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        csrf_verify();
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';
        $remember = !empty($_POST['remember']);
        $user = $this->model->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['errors'] = ['auth' => 'Invalid email or password.'];
            $_SESSION['old']    = ['email' => $email];
            redirect('/login');
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        if ($remember) {
            $raw    = bin2hex(random_bytes(32));
            $hashed = hash('sha256', $raw);
            $this->model->saveToken((int) $user['id'], $hashed);
            setcookie('remember_token', $raw, [
                'expires'  => time() + 60 * 60 * 24 * 30,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
        redirect($user['role'] === 'admin' ? '/admin/products' : '/products');
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!empty($_SESSION['user_id'])) {
            $this->model->clearToken((int) $_SESSION['user_id']);
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/');
        redirect('/login');
    }

    public static function restoreFromCookie(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!empty($_SESSION['user_id'])) return;
        if (empty($_COOKIE['remember_token'])) return;
        $model  = new User();
        $hashed = hash('sha256', $_COOKIE['remember_token']);
        $user   = $model->findByToken($hashed);
        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];
        } else {
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }
}