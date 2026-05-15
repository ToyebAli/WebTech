<?php
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController {
    private User $model;
    public function __construct() { $this->model = new User(); }

    
    public function show(): void {
        session_guard();
        $profile   = $this->model->findById((int) $_SESSION['user_id']);
        $addresses = json_decode($profile['shipping_addresses'] ?? '[]', true) ?? [];
        $profile['addr1'] = $addresses[0] ?? '';
        $profile['addr2'] = $addresses[1] ?? '';
        if (session_status() === PHP_SESSION_NONE) session_start();
        $errors    = $_SESSION['errors']     ?? [];
        $tab       = $_SESSION['active_tab'] ?? 'profile';
        unset($_SESSION['errors'], $_SESSION['active_tab']);
        $pageTitle = 'My Profile';
        include __DIR__ . '/../views/profile/show.php';
    }


    public function update(): void {
        session_guard();
        csrf_verify();
        $userId = (int) $_SESSION['user_id'];
        $name   = trim($_POST['name']  ?? '');
        $email  = trim($_POST['email'] ?? '');
        $phone  = trim($_POST['phone'] ?? '');
        $addr1  = trim($_POST['addr1'] ?? '');
        $addr2  = trim($_POST['addr2'] ?? '');
        $addresses = array_values(array_filter([$addr1, $addr2], fn($a) => $a !== ''));
        $errors = [];
        if ($name === '') $errors['name'] = 'Full name is required.';
        elseif (strlen($name) > 100) $errors['name'] = 'Name must be 100 characters or less.';
        if ($email === '') $errors['email'] = 'Email address is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email address.';
        else {
            $existing = $this->model->findByEmail($email);
            if ($existing && (int)$existing['id'] !== $userId)
                $errors['email'] = 'This email is already used by another account.';
        }
        if (!empty($errors)) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['errors']     = $errors;
            $_SESSION['active_tab'] = 'profile';
            redirect('/profile');
        }
        $this->model->updateProfile($userId, $name, $email, $phone, json_encode($addresses));
        $_SESSION['name'] = $name;
        flash('success', 'Profile updated successfully.');
        redirect('/profile');
    }

    
    public function changePassword(): void {
        session_guard();
        csrf_verify();
        $userId  = (int) $_SESSION['user_id'];
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $user    = $this->model->findById($userId);
        $errors  = [];
        if (!password_verify($current, $user['password_hash']))
            $errors['current'] = 'Your current password is incorrect.';
        if (strlen($new) < 8)
            $errors['new'] = 'New password must be at least 8 characters.';
        if ($new !== $confirm)
            $errors['confirm'] = 'Passwords do not match.';
        if (!empty($errors)) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['errors']     = $errors;
            $_SESSION['active_tab'] = 'password';
            redirect('/profile');
        }
        $this->model->updatePassword($userId, $new);
        flash('success', 'Password changed successfully.');
        redirect('/profile');
    }
}