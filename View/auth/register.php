<?php
if (!function_exists('e')) require_once __DIR__ . '/../../config/helpers.php';
include __DIR__ . '/../layouts/header.php';
?>
<div class="wrap page">
<div style="max-width:480px;margin:0 auto">
  <p style="font-size:var(--xs);color:var(--muted);margin-bottom:var(--s4)">
    <a href="/login">&larr; Back to Sign In</a>
  </p>
  <div class="card"><div class="cb">
    <h1 class="ct">Create Account</h1>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-err" role="alert" aria-live="polite">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Please fix the errors below before continuing.
    </div>
    <?php endif; ?>

    <form method="POST" action="/register" novalidate>
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <div class="frow">
        <div class="fg">
          <label for="name">Full Name <span style="color:var(--error)">*</span></label>
          <input type="text" id="name" name="name"
            class="fc <?= !empty($errors['name']) ? 'err' : '' ?>"
            value="<?= e($old['name'] ?? '') ?>"
            placeholder="Your full name" required autocomplete="name" autofocus>
          <?php if (!empty($errors['name'])): ?>
            <div class="fe" role="alert">&#9888; <?= e($errors['name']) ?></div>
          <?php endif; ?>
        </div>
        <div class="fg">
          <label for="phone">Phone <span style="color:var(--faint);font-weight:400">(optional)</span></label>
          <input type="tel" id="phone" name="phone"
            class="fc" value="<?= e($old['phone'] ?? '') ?>"
            placeholder="01712345678" autocomplete="tel">
        </div>
      </div>

      <div class="fg">
        <label for="email">Email Address <span style="color:var(--error)">*</span></label>
        <input type="email" id="email" name="email"
          class="fc <?= !empty($errors['email']) ? 'err' : '' ?>"
          value="<?= e($old['email'] ?? '') ?>"
          placeholder="you@example.com" required autocomplete="email">
        <?php if (!empty($errors['email'])): ?>
          <div class="fe" role="alert">&#9888; <?= e($errors['email']) ?></div>
        <?php endif; ?>
      </div>

      <div class="fg">
        <label for="password">Password <span style="color:var(--error)">*</span></label>
        <div style="position:relative">
          <input type="password" id="password" name="password"
            class="fc <?= !empty($errors['password']) ? 'err' : '' ?>"
            placeholder="Minimum 8 characters"
            required minlength="8" autocomplete="new-password"
            oninput="pwdStr(this.value)" style="padding-right:2.8rem">
          <button type="button" onclick="togPwd('password',this)" aria-label="Show password"
            style="position:absolute;right:var(--s3);top:50%;transform:translateY(-50%);color:var(--muted)">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
        <div class="pbar"><div class="pbar-fill" id="pb"></div></div>
        <?php if (!empty($errors['password'])): ?>
          <div class="fe" role="alert">&#9888; <?= e($errors['password']) ?></div>
        <?php else: ?>
          <div class="fh" id="ph">Use 8+ characters with letters, numbers &amp; symbols.</div>
        <?php endif; ?>
      </div>

      <div class="fg">
        <label for="confirm">Confirm Password <span style="color:var(--error)">*</span></label>
        <div style="position:relative">
          <input type="password" id="confirm" name="confirm"
            class="fc <?= !empty($errors['confirm']) ? 'err' : '' ?>"
            placeholder="Re-enter your password"
            required autocomplete="new-password"
            oninput="chkMatch()" style="padding-right:2.8rem">
          <button type="button" onclick="togPwd('confirm',this)" aria-label="Show confirm password"
            style="position:absolute;right:var(--s3);top:50%;transform:translateY(-50%);color:var(--muted)">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
        <?php if (!empty($errors['confirm'])): ?>
          <div class="fe" role="alert">&#9888; <?= e($errors['confirm']) ?></div>
        <?php endif; ?>
        <div id="mh" class="fh" style="display:none"></div>
      </div>

      <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:var(--s2)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="8.5" cy="7" r="4"/>
          <line x1="20" y1="8" x2="20" y2="14"/>
          <line x1="23" y1="11" x2="17" y2="11"/>
        </svg>
        Create Account
      </button>
    </form>

    <p style="margin-top:var(--s6);text-align:center;font-size:var(--sm);color:var(--muted)">
      Already have an account? <a href="/login" style="font-weight:600">Sign in</a>
    </p>
  </div></div>
</div></div>

<script>
const lvl=[
  {w:'0%',bg:'transparent',t:'Use 8+ characters with letters, numbers & symbols.'},
  {w:'25%',bg:'#b91c1c',t:'Weak — add uppercase letters and numbers.'},
  {w:'50%',bg:'#d97706',t:'Fair — add a symbol to strengthen it.'},
  {w:'75%',bg:'#ca8a04',t:'Good — almost there!'},
  {w:'100%',bg:'#437a22',t:'Strong password \u2713'},
];
function pwdStr(v){
  let s=0;
  if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;
  if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
  const b=document.getElementById('pb');
  const h=document.getElementById('ph');
  b.style.width=lvl[s].w;b.style.background=lvl[s].bg;
  if(h)h.textContent=lvl[s].t;
  chkMatch();
}
function chkMatch(){
  const p=document.getElementById('password').value;
  const c=document.getElementById('confirm').value;
  const h=document.getElementById('mh');
  if(!c){h.style.display='none';return;}
  h.style.display='block';
  if(p===c){h.textContent='\u2713 Passwords match';h.style.color='var(--success)';}
  else{h.textContent='\u2717 Passwords do not match';h.style.color='var(--error)';}
}
function togPwd(id,btn){
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