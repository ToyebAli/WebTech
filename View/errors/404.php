<?php
if (!function_exists('e')) require_once __DIR__ . '/../../config/helpers.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = '404 Not Found';
include __DIR__ . '/../layouts/header.php';
$home = !empty($_SESSION['user_id'])
    ? ($_SESSION['role'] === 'admin' ? '/admin/products' : '/products')
    : '/login';
?>
<div class="wrap page" style="min-height:60vh;display:flex;align-items:center;justify-content:center">
  <div class="empty">
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="1.4" style="color:var(--faint)">
      <circle cx="12" cy="12" r="10"/>
      <path d="M16 16s-1.5-2-4-2-4 2-4 2"/>
      <line x1="9" y1="9" x2="9.01" y2="9"/>
      <line x1="15" y1="9" x2="15.01" y2="9"/>
    </svg>
    <h3>404 — Page Not Found</h3>
    <p>The page you are looking for does not exist or has been moved.</p>
    <a href="<?= $home ?>" class="btn btn-primary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        <polyline points="9 22 9 12 15 12 15 22"/>
      </svg>
      Back to Home
    </a>
  </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>