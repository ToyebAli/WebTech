<?php
if (!function_exists('e')) require_once __DIR__ . '/../../config/helpers.php';
include __DIR__ . '/../layouts/header.php';
?>
<div class="wrap page">
<div style="max-width:440px;margin:0 auto">
  <div class="card"><div class="cb">
    <div style="display:flex;justify-content:center;margin-bottom:var(--s6)">
      <div style="width:52px;height:52px;background:var(--primary);border-radius:var(--rlg);
        display:flex;align-items:center;justify-content:center;box-shadow:var(--sh-md)">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2">
          <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
      </div>
    </div>
    <h1 class="ct" style="text-align:center">Welcome back</h1>
    <p style="text-align:center;color:var(--muted);font-size:var(--sm);
      margin-top:calc(-1 * var(--s4));margin-bottom:var(--s6)">
      Sign in to your ShopHub account
    </p>

    <?php if ($msg = flash('success')): ?>
    <div class="alert alert-ok" role="alert">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      <?= e($msg) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($errors['auth'])): ?>
    <div class="alert alert-err" role="alert" aria-live="assertive">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      <?= e($errors['auth']) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/login" novalidate>
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <div class="fg">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email"
          class="fc <?= !empty($errors['auth']) ? 'err' : '' ?>"
          value="<?= e($old['email'] ?? '') ?>"
          placeholder="you@example.com"
          required autocomplete="email" autofocus>
      </div>

      <div class="fg">
        <label for="password">Password</label>
        <div style="position:relative">
          <input type="password" id="password" name="password"
            class="fc <?= !empty($errors['auth']) ? 'err' : '' ?>"
            placeholder="Your password" required autocomplete="current-password"
            style="padding-right:2.8rem">
          <button type="button" onclick="togPwd(this)" aria-label="Show password"
            style="position:absolute;right:var(--s3);top:50%;transform:translateY(-50%);color:var(--muted)">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="fg">
        <div class="crow">
          <input type="checkbox" id="remember" name="remember" value="1">
          <label for="remember">Remember me for 30 days</label>
        </div>
        <div class="fh" style="margin-top:var(--s1);margin-left:1.5rem">
          Secure HttpOnly cookie. Avoid on shared devices.
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:var(--s2)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
          <polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
        </svg>
        Sign In
      </button>
    </form>

    <p style="margin-top:var(--s6);text-align:center;font-size:var(--sm);color:var(--muted)">
      No account yet? <a href="/register" style="font-weight:600">Create one free</a>
    </p>
    <div class="alert alert-info" style="margin-top:var(--s6);font-size:var(--xs)">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <div><strong>Demo admin:</strong> admin@store.com / password</div>
    </div>
  </div></div>
</div></div>

<script>
function togPwd(btn){
  const el=document.getElementById('password');
  const show=el.type==='text';
  el.type=show?'password':'text';
  btn.setAttribute('aria-label',show?'Show password':'Hide password');
  btn.querySelector('svg').innerHTML=show
    ?'<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'
    :'<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
}
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>