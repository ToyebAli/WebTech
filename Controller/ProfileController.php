<?php
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController {

    private User $user;

    public function __construct() { $this->user = new User(); }
    public function show(): void {
        session_guard();
        $profile = $this->user->findById((int) $_SESSION['user_id']);
        $profile['shipping_addresses'] =
            json_decode($profile['shipping_addresses'] ?? '[]', true) ?? [];

        if (session_status() === PHP_SESSION_NONE) session_start();
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $pageTitle = 'My Profile';
        include __DIR__ . '/../views/profile/show.php';
    }
    public function update(): void {
        session_guard();
        csrf_verify();

        $id    = (int) $_SESSION['user_id'];
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $addr1 = trim($_POST['addr1'] ?? '');
        $addr2 = trim($_POST['addr2'] ?? '');
        $addresses = array_values(array_filter([$addr1, $addr2]));

        $errors = [];
        if ($name === '')
            $errors['name'] = 'Full name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'Please enter a valid email address.';
        else {
            $existing = $this->user->findByEmail($email);
            if ($existing && (int) $existing['id'] !== $id)
                $errors['email'] = 'This email address is already in use.';
        }

        if (!empty($errors)) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['errors'] = $errors;
            redirect('/profile');
        }

        $this->user->update($id, [
            'name'               => $name,
            'email'              => $email,
            'phone'              => $phone,
            'shipping_addresses' => json_encode($addresses),
        ]);
        $_SESSION['name'] = $name;
        flash('success', 'Profile updated successfully.');
        redirect('/profile');
    }
    public function changePassword(): void {
        session_guard();
        csrf_verify();

        $id      = (int) $_SESSION['user_id'];
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $record = $this->user->findById($id);
        $errors = [];

        if (!password_verify($current, $record['password_hash']))
            $errors['current'] = 'Current password is incorrect.';
        if (strlen($new) < 8)
            $errors['new'] = 'New password must be at least 8 characters.';
        if ($new !== $confirm)
            $errors['confirm'] = 'Passwords do not match.';

        if (!empty($errors)) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['errors'] = $errors;
            redirect('/profile');
        }

        $this->user->updatePassword($id, $new);
        flash('success', 'Password changed successfully.');
        redirect('/profile');
    }
}