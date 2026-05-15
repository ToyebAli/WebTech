<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$loggedIn  = !empty($_SESSION['user_id']);
$role      = $_SESSION['role'] ?? '';
$userName  = $_SESSION['name'] ?? '';
$cartCount = (!empty($_SESSION['cart'])) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'ShopHub') ?> — ShopHub</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
<style>
:root{
  --font-body:'Inter',sans-serif; --font-head:'Sora',sans-serif;
  --xs:.75rem;--sm:.875rem;--base:1rem;--lg:1.25rem;--xl:1.5rem;--2xl:1.875rem;
  --s1:.25rem;--s2:.5rem;--s3:.75rem;--s4:1rem;--s5:1.25rem;--s6:1.5rem;
  --s8:2rem;--s10:2.5rem;--s12:3rem;--s16:4rem;
  --bg:#f7f6f2;--surface:#fff;--surface2:#f9f8f5;
  --border:#e2e0db;--divider:#ece9e4;
  --text:#1a1814;--muted:#6b6966;--faint:#b0aeaa;
  --primary:#01696f;--primary-h:#0c4e54;--primary-light:#e8f4f4;
  --success:#437a22;--success-light:#eef5e9;
  --warning:#964219;--warning-light:#fdf2ec;
  --error:#b91c1c;--error-light:#fef2f2;
  --info:#1d4ed8;--info-light:#eff6ff;
  --rsm:.375rem;--rmd:.5rem;--rlg:.75rem;--rxl:1rem;
  --sh-sm:0 1px 3px rgba(0,0,0,.06);--sh-md:0 4px 12px rgba(0,0,0,.09);
  --sh-lg:0 12px 32px rgba(0,0,0,.12);--tr:160ms cubic-bezier(.16,1,.3,1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-font-smoothing:antialiased;scroll-behavior:smooth}
body{font-family:var(--font-body);font-size:var(--base);color:var(--text);
  background:var(--bg);min-height:100dvh;line-height:1.6}
img,svg{display:block;max-width:100%}
input,button,select,textarea{font:inherit;color:inherit}
button{cursor:pointer;background:none;border:none}
a{color:var(--primary);text-decoration:none}
a:hover{text-decoration:underline}
table{border-collapse:collapse;width:100%}
:focus-visible{outline:2px solid var(--primary);outline-offset:3px;border-radius:var(--rsm)}
.skip{position:absolute;top:-40px;left:0;z-index:9999;background:var(--primary);
  color:#fff;padding:var(--s2) var(--s4);border-radius:0 0 var(--rmd) 0}
.skip:focus{top:0}
.nav{position:sticky;top:0;z-index:100;background:var(--surface);
  border-bottom:1px solid var(--border);box-shadow:var(--sh-sm)}
.nav-in{max-width:1200px;margin:0 auto;padding:var(--s3) var(--s6);
  display:flex;align-items:center;gap:var(--s6)}
.nav-brand{display:flex;align-items:center;gap:var(--s2);
  font-family:var(--font-head);font-size:var(--lg);font-weight:700;color:var(--text)}
.nav-brand:hover{text-decoration:none}
.nav-brand svg{color:var(--primary)}
.nav-links{display:flex;align-items:center;gap:var(--s1);list-style:none;margin-left:auto}
.nav-a{display:flex;align-items:center;gap:var(--s1);font-size:var(--sm);
  font-weight:500;color:var(--muted);padding:var(--s2) var(--s3);
  border-radius:var(--rmd);transition:background var(--tr),color var(--tr)}
.nav-a:hover{background:var(--surface2);color:var(--text);text-decoration:none}
.nav-badge{background:var(--primary);color:#fff;font-size:.65rem;font-weight:700;
  padding:1px 6px;border-radius:999px}
.nav-sep{width:1px;height:22px;background:var(--divider);margin:0 var(--s1)}
.wrap{max-width:1200px;margin:0 auto;padding-inline:var(--s6)}
.page{padding-block:var(--s10)}
.ph{display:flex;align-items:center;gap:var(--s3);margin-bottom:var(--s8)}
.ph h1{font-family:var(--font-head);font-size:var(--2xl);font-weight:700}
.card{background:var(--surface);border:1px solid var(--border);
  border-radius:var(--rxl);box-shadow:var(--sh-sm)}
.cb{padding:var(--s8)}
.ct{font-family:var(--font-head);font-size:var(--xl);font-weight:700;margin-bottom:var(--s6)}
.fg{margin-bottom:var(--s5)}
.frow{display:grid;grid-template-columns:1fr 1fr;gap:var(--s4)}
.fg label{display:block;font-size:var(--sm);font-weight:600;margin-bottom:var(--s2);color:var(--text)}
.fc{display:block;width:100%;padding:var(--s3) var(--s4);background:var(--bg);color:var(--text);
  border:1.5px solid var(--border);border-radius:var(--rmd);font-size:var(--sm);line-height:1.5;
  transition:border-color var(--tr),box-shadow var(--tr)}
.fc::placeholder{color:var(--faint)}
.fc:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(1,105,111,.15)}
.fc.err{border-color:var(--error);box-shadow:0 0 0 3px rgba(185,28,28,.1)}
.fe{color:var(--error);font-size:var(--xs);margin-top:var(--s1);font-weight:500}
.fh{color:var(--muted);font-size:var(--xs);margin-top:var(--s1)}
.fdiv{border:none;border-top:1px solid var(--divider);margin-block:var(--s6)}
.crow{display:flex;align-items:center;gap:var(--s2)}
.crow input[type=checkbox]{width:16px;height:16px;cursor:pointer;accent-color:var(--primary)}
.crow label{margin-bottom:0;font-weight:400;cursor:pointer;font-size:var(--sm)}
.pbar{height:4px;background:var(--divider);border-radius:999px;margin-top:var(--s2);overflow:hidden}
.pbar-fill{height:100%;width:0;border-radius:999px;transition:width .35s ease,background .35s ease}
.btn{display:inline-flex;align-items:center;gap:var(--s2);padding:var(--s2) var(--s5);
  font-size:var(--sm);font-weight:600;border-radius:var(--rmd);border:1.5px solid transparent;
  cursor:pointer;transition:background var(--tr),color var(--tr),border-color var(--tr);white-space:nowrap}
.btn:active{transform:scale(.98)}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
.btn-primary:hover{background:var(--primary-h);border-color:var(--primary-h);text-decoration:none;color:#fff}
.btn-sec{background:var(--surface2);color:var(--text);border-color:var(--border)}
.btn-sec:hover{background:var(--divider)}
.btn-danger{background:var(--error);color:#fff;border-color:var(--error)}
.btn-danger:hover{background:#991b1b}
.btn-full{width:100%;justify-content:center}
.btn-lg{padding:var(--s3) var(--s6);font-size:var(--base)}
.btn-sm{padding:var(--s1) var(--s3);font-size:var(--xs)}
.alert{display:flex;align-items:flex-start;gap:var(--s3);padding:var(--s4);
  border-radius:var(--rmd);font-size:var(--sm);margin-bottom:var(--s5)}
.alert svg{flex-shrink:0;margin-top:1px}
.alert-ok{background:var(--success-light);color:#166534;border:1px solid #bbf7d0}
.alert-err{background:var(--error-light);color:#991b1b;border:1px solid #fecaca}
.alert-info{background:var(--info-light);color:var(--info);border:1px solid #bfdbfe}
.alert-warn{background:var(--warning-light);color:#92400e;border:1px solid #fde68a}
.badge{display:inline-flex;align-items:center;padding:2px var(--s3);
  font-size:var(--xs);font-weight:600;border-radius:999px}
.b-green{background:var(--success-light);color:var(--success)}
.b-blue{background:var(--info-light);color:var(--info)}
.b-red{background:var(--error-light);color:var(--error)}
.b-orange{background:var(--warning-light);color:var(--warning)}
.b-gray{background:var(--surface2);color:var(--muted)}
.b-yellow{background:#fef9c3;color:#854d0e}
.empty{display:flex;flex-direction:column;align-items:center;text-align:center;
  gap:var(--s4);padding:var(--s16) var(--s8);color:var(--muted)}
.empty h3{color:var(--text);font-size:var(--lg)}
.empty p{max-width:36ch}
@media(max-width:640px){
  .frow{grid-template-columns:1fr}
  .nav-in,.wrap,.cb{padding-inline:var(--s4)}
}
</style>
</head>
<body>
<a href="#main" class="skip">Skip to content</a>
<nav class="nav" aria-label="Main navigation">
  <div class="nav-in">
    <a href="<?= $loggedIn ? ($role==='admin'?'/admin/products':'/products') : '/login' ?>"
       class="nav-brand" aria-label="ShopHub home">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
      </svg>
      ShopHub
    </a>
    <ul class="nav-links" role="list">
      <?php if ($loggedIn): ?>
        <?php if ($role === 'customer'): ?>
          <li><a href="/products" class="nav-a">Shop</a></li>
          <li><a href="/cart" class="nav-a" aria-label="Cart">
            Cart <?php if ($cartCount > 0): ?>
              <span class="nav-badge"><?= $cartCount ?></span>
            <?php endif; ?>
          </a></li>
          <li><a href="/orders" class="nav-a">Orders</a></li>
        <?php endif; ?>
        <?php if ($role === 'admin'): ?>
          <li><a href="/admin/products" class="nav-a">Products</a></li>
          <li><a href="/admin/categories" class="nav-a">Categories</a></li>
          <li><a href="/admin/orders" class="nav-a">Orders</a></li>
        <?php endif; ?>
        <li><div class="nav-sep" aria-hidden="true"></div></li>
        <li><a href="/profile" class="nav-a">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
          <?= e($userName) ?>
        </a></li>
        <li><a href="/logout" class="btn btn-sec btn-sm">Sign Out</a></li>
      <?php else: ?>
        <li><a href="/login" class="nav-a">Sign In</a></li>
        <li><a href="/register" class="btn btn-primary btn-sm">Register</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>
<main id="main">