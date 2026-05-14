<?php
if (!function_exists('e')) require_once __DIR__ . '/../../config/helpers.php';
include __DIR__ . '/../layouts/header.php';
?>
<div class="wrap page">
  <div class="ph">
    <h1>My Profile</h1>
    <span class="badge <?= $_SESSION['role']==='admin' ? 'b-blue' : 'b-green' ?>">
      <?= e(ucfirst($_SESSION['role'] ?? 'customer')) ?>
    </span>
  </div>

  <?php if ($msg = flash('success')): ?>
  <div class="alert alert-ok" role="alert" style="max-width:860px">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    <?= e($msg) ?>
  </div>
  <?php endif; ?>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--s8);align-items:start;max-width:860px">

    <div class="card"><div class="cb">
      <h2 style="font-size:var(--lg);font-weight:700;margin-bottom:var(--s6);display:flex;align-items:center;gap:var(--s2)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>
        Personal Information
      </h2>

      <?php if (($tab ?? '') === 'profile' && !empty($errors)): ?>
      <div class="alert alert-err" role="alert">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        Please fix the errors below.
      </div>
      <?php endif; ?>

      <form method="POST" action="/profile/update" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="frow">
          <div class="fg">
            <label for="name">Full Name <span style="color:var(--error)">*</span></label>
            <input type="text" id="name" name="name"
              class="fc <?= !empty($errors['name']) ? 'err' : '' ?>"
              value="<?= e($profile['name']) ?>" required autocomplete="name">
            <?php if (!empty($errors['name'])): ?>
              <div class="fe" role="alert">&#9888; <?= e($errors['name']) ?></div>
            <?php endif; ?>
          </div>
          <div class="fg">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" class="fc"
              value="<?= e($profile['phone'] ?? '') ?>"
              placeholder="01712345678" autocomplete="tel">
          </div>
        </div>
        <div class="fg">
          <label for="email">Email Address <span style="color:var(--error)">*</span></label>
          <input type="email" id="email" name="email"
            class="fc <?= !empty($errors['email']) ? 'err' : '' ?>"
            value="<?= e($profile['email']) ?>" required autocomplete="email">
          <?php if (!empty($errors['email'])): ?>
            <div class="fe" role="alert">&#9888; <?= e($errors['email']) ?></div>
          <?php endif; ?>
        </div>
        <hr class="fdiv">
        <h3 style="font-size:var(--base);font-weight:600;margin-bottom:var(--s4);display:flex;align-items:center;gap:var(--s2)">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
          </svg>
          Saved Addresses
        </h3>
        <div class="fh" style="margin-bottom:var(--s4)">Shown at checkout as quick-select options.</div>
        <div class="fg">
          <label for="addr1">Address 1</label>
          <input type="text" id="addr1" name="addr1" class="fc"
            value="<?= e($profile['addr1']) ?>"
            placeholder="House, Road, Area, City"
            autocomplete="shipping address-line1">
        </div>
        <div class="fg">
          <label for="addr2">Address 2 <span style="color:var(--faint);font-weight:400">(optional)</span></label>
          <input type="text" id="addr2" name="addr2" class="fc"
            value="<?= e($profile['addr2']) ?>"
            placeholder="Office / alternative address"
            autocomplete="shipping address-line2">
        </div>
        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Save Changes
        </button>
      </form>
    </div></div>

    <div style="display:flex;flex-direction:column;gap:var(--s6)">
      <div class="card"><div class="cb">
        <h2 style="font-size:var(--lg);font-weight:700;margin-bottom:var(--s6);display:flex;align-items:center;gap:var(--s2)">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          Change Password
        </h2>

        <?php if (($tab ?? '') === 'password' && !empty($errors)): ?>
        <div class="alert alert-err" role="alert">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          Please fix the errors below.
        </div>
        <?php endif; ?>

        <form method="POST" action="/profile/password" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <div class="fg">
            <label for="cur_pwd">Current Password</label>
            <div style="position:relative">
              <input type="password" id="cur_pwd" name="current_password"
                class="fc <?= !empty($errors['current']) ? 'err' : '' ?>"
                placeholder="Your current password"
                required autocomplete="current-password" style="padding-right:2.8rem">
              <button type="button" onclick="togF('cur_pwd',this)" aria-label="Show current password"
                style="position:absolute;right:var(--s3);top:50%;transform:translateY(-50%);color:var(--muted)">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <?php if (!empty($errors['current'])): ?>
              <div class="fe" role="alert">&#9888; <?= e($errors['current']) ?></div>
            <?php endif; ?>
          </div>
          <div class="fg">
            <label for="new_pwd">New Password</label>
            <div style="position:relative">
              <input type="password" id="new_pwd" name="new_password"
                class="fc <?= !empty($errors['new']) ? 'err' : '' ?>"
                placeholder="Minimum 8 characters"
                required minlength="8" autocomplete="new-password"
                oninput="pwdStr2(this.value)" style="padding-right:2.8rem">
              <button type="button" onclick="togF('new_pwd',this)" aria-label="Show new password"
                style="position:absolute;right:var(--s3);top:50%;transform:translateY(-50%);color:var(--muted)">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <div class="pbar"><div class="pbar-fill" id="pb2"></div></div>
            <?php if (!empty($errors['new'])): ?>
              <div class="fe" role="alert">&#9888; <?= e($errors['new']) ?></div>
            <?php endif; ?>
          </div>
          <div class="fg">
            <label for="conf_pwd">Confirm New Password</label>
            <input type="password" id="conf_pwd" name="confirm_password"
              class="fc <?= !empty($errors['confirm']) ? 'err' : '' ?>"
              placeholder="Re-enter new password"
              required autocomplete="new-password">
            <?php if (!empty($errors['confirm'])): ?>
              <div class="fe" role="alert">&#9888; <?= e($errors['confirm']) ?></div>
            <?php endif; ?>
          </div>
          <button type="submit" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Update Password
          </button>
        </form>
      </div></div>

      <div class="card"><div class="cb" style="padding:var(--s5) var(--s6)">
        <h3 style="font-size:var(--sm);font-weight:600;color:var(--muted);margin-bottom:var(--s4)">Account Details</h3>
        <dl style="display:grid;grid-template-columns:auto 1fr;gap:var(--s2) var(--s4);font-size:var(--sm)">
          <dt style="color:var(--muted);font-weight:500">Role</dt>
          <dd><?= e(ucfirst($profile['role'])) ?></dd>
          <dt style="color:var(--muted);font-weight:500">Member since</dt>
          <dd><?= e(date('d M Y', strtotime($profile['created_at']))) ?></dd>
          <dt style="color:var(--muted);font-weight:500">User ID</dt>
          <dd style="font-family:monospace;color:var(--muted)">#<?= (int)$profile['id'] ?></dd>
        </dl>
        <hr class="fdiv" style="margin:var(--s4) 0">
        <a href="/logout" class="btn btn-danger btn-sm"
           style="width:100%;justify-content:center"
           onclick="return confirm('Sign out of your account?')">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Sign Out
        </a>
      </div></div>
    </div>
  </div>
</div>

<script>
function pwdStr2(v){
  let s=0;
  if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;
  if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
  const c=['transparent','#b91c1c','#d97706','#ca8a04','#437a22'];
  const b=document.getElementById('pb2');
  b.style.width=(s*25)+'%';b.style.background=c[s];
}
function togF(id,btn){
  const el=document.getElementById(id);
  const show=el.type==='text';
  el.type=show?'password':'text';
  btn.setAttribute('aria-label',show?'Show password':'Hide password');
  btn.querySelector('svg').innerHTML=show
    ?'<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'
    :'<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
}
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>