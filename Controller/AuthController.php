<?php
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private User $user;
    public function __construct() { $this->user = new User(); }
    public function registerForm(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $errors = $_SESSION['errors'] ?? [];
        $old    = $_SESSION['old']    ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);
        $pageTitle = 'Create Account';
        include __DIR__ . '/../views/auth/register.php';
    }
    public function register(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        csrf_verify();
        $name     = trim($_POST['name']    ?? '');
        $email    = trim($_POST['email']   ?? '');
        $phone    = trim($_POST['phone']   ?? '');
        $password = $_POST['password']     ?? '';
        $confirm  = $_POST['confirm']      ?? '';

        $errors = [];
        if ($name === '')
            $errors['name'] = 'Full name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'Please enter a valid email address.';
        elseif ($this->user->findByEmail($email))
            $errors['email'] = 'This email address is already registered.';
        if (strlen($password) < 8)
            $errors['password'] = 'Password must be at least 8 characters.';
        if ($password !== $confirm)
            $errors['confirm'] = 'Passwords do not match.';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = compact('name', 'email', 'phone');
            redirect('/register');
        }

        $this->user->create($name, $email, $password, $phone);
        flash('success', 'Account created successfully! Please log in.');
        redirect('/login');
    }
    public function loginForm(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!empty($_SESSION['user_id']))
            redirect($_SESSION['role'] === 'admin' ? '/admin/products' : '/products');

        $errors = $_SESSION['errors'] ?? [];
        $old    = $_SESSION['old']    ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);
        $pageTitle = 'Sign In';
        include __DIR__ . '/../views/auth/login.php';
    }
    public function login(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        csrf_verify();

        $email    = trim($_POST['email']   ?? '');
        $password = $_POST['password']     ?? '';
        $remember = !empty($_POST['remember']);

        $record = $this->user->findByEmail($email);

        if (!$record || !password_verify($password, $record['password_hash'])) {
            $_SESSION['errors'] = ['auth' => 'Invalid email or password.'];
            $_SESSION['old']    = ['email' => $email];
            redirect('/login');
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $record['id'];
        $_SESSION['name']    = $record['name'];
        $_SESSION['role']    = $record['role'];

        if ($remember) {
            $rawToken    = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $rawToken);
            $this->user->update((int) $record['id'], ['remember_token' => $hashedToken]);
            setcookie('remember_token', $rawToken, [
                'expires'  => time() + 60 * 60 * 24 * 30,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        redirect($record['role'] === 'admin' ? '/admin/products' : '/products');
    }
    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!empty($_SESSION['user_id']))
            $this->user->clearRememberToken((int) $_SESSION['user_id']);

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

        $model   = new User();
        $hashed  = hash('sha256', $_COOKIE['remember_token']);
        $record  = $model->findByRememberToken($hashed);

        if ($record) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $record['id'];
            $_SESSION['name']    = $record['name'];
            $_SESSION['role']    = $record['role'];
        }
    }
}