<?php
/* Copyright (C) 2024		MDW							<mdeweerd@users.noreply.github.com>
 */
// Protection to avoid direct call of template
if (empty($context) || !is_object($context)) {
	print "Error, template page can't be called as URL";
	exit(1);
}

'@phan-var-force Context $context';

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="/dolibarr/public/mybrand/img/favicon.ico?v=<?php echo time(); ?>">
</head>

<body class="body bodylogin">

<?php
print '
<link rel="icon" type="image/x-icon" href="' . DOL_URL_ROOT . '/dolibarr/public/mybrand/img/favicon.ico">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<style>
:root{
  --brand1:#0f8ea8; --brand2:#1178d1;
}

html,body{
  height:100%;
  background:var(--bg);
  color:var(--ink);
  font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;
  margin: 0;
  padding: 0;
}
*{box-sizing:border-box}

/* Page background image */
body.bodylogin {
  background:url("/public/mybrand/img/background icon.jpg")
              center center / cover no-repeat fixed !important;
  margin: 0;
  padding: 0;
  height: 100vh;
  overflow: auto;
}

/* ---------- Header logo (top-left) ---------- */
.brand-header{position:fixed;top:22px;left:32px;z-index:1000}
.brand-header img{height:70px;width:auto}

/* ---------- Two-column shell + divider ---------- */
.doli-login-shell{
  display:grid;
  grid-template-columns:1fr 1fr;
  min-height:100vh;
  position:relative;
}
.doli-login-shell::before{
  content:"";
  position:absolute;
  top:130px;bottom:130px;left:50%;
  width:1px;background:#ccbebe;
}

/* ---------- Left panel (AMLAK side) ---------- */
.doli-left{
  padding:clamp(24px,5vw,56px);
  display:grid;
  place-items:center;
  text-align:center;
  color:#fff;
  position: relative;
}
.doli-left-inner{
  max-width:560px;width:100%;
  display:flex;flex-direction:column;
  align-items:center;justify-content:center;
  gap:14px;
  min-height: 100%;
}
.doli-left{
  position:relative;
}
.doli-left .doli-logo{display:none}
.doli-brandmark{margin:8px 0 6px}
.doli-brandmark img{
  margin-top: 38px;
  height:350px;
  width:auto;max-width:440px;
}
.doli-tagline{
  font-size:clamp(14px,1.4vw,18px);
  opacity:.95; color:#171a1f;
}
.doli-social {
  position: absolute;
  left: 50%;
  bottom: -18px;
  transform: translateX(-50%);
  display: flex;
  justify-content: center;
  gap: 6px;
  margin: 0;
  padding: 0;
}
.doli-social a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  line-height: 0;
  margin: 0;
  padding: 0;
  border: 0;
}
.doli-social a img {
  display: block;
  width: 20px;
  height: 20px;
  transition: transform .25s, filter .25s;
}
.doli-social a img:hover {
  filter: none;
  transform: scale(1.1);
}

/* Support + copyright under the form (right card) */
.doli-support{
  position:absolute;
  left:50%; transform:translateX(-50%);
  bottom:12px;
  display:flex; align-items:center; justify-content:center;
  gap:8px; color:var(--muted); font-size:14px
}
.doli-support a{color:#0e6ad4;text-decoration:none}
.doli-support a:hover{text-decoration:underline}
.doli-support .icon-16{width:16px;height:16px;opacity:.7;flex:0 0 16px}
.sr-only{
  position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;
  clip:rect(0,0,0,0);white-space:nowrap;border:0;
}
.doli-copy{
  position:absolute;
  left:50%;
  transform:translateX(-50%);
  bottom:-19px;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  flex-wrap:nowrap;
  color:var(--muted);
  font-size:14px;
  line-height:1.2;
}
.doli-copy a{color:#0e6ad4;text-decoration:none}
.doli-copy a:hover{text-decoration:none}
.doli-copy-logo {
  height:16px;
  width:auto;
  display:inline-block;
  vertical-align:middle;
  margin:0;
  filter:none;
}
body.login-page{
  background:url("/dolibarr/public/mybrand/img/background icon.jpg")
              center no-repeat fixed !important;
}

/* ---------- Right panel (login card) ---------- */
.doli-right{
  padding-top: 100px;
  position: relative;
  display:grid;
  place-items:center;
}
.doli-card{
  width:70%;
  // padding:clamp(24px,4vw,40px);
}
.doli-hello{margin:0 0 4px;
font-size: 30px;
padding-left: 30px;
font-weight: bold;
}
.doli-sub{
  padding-left: 30px;
  color:var(--muted);
  font-weight:500
}

/* Form fields */
.doli-field{margin:14px 0 16px}
.doli-label{display:block;font-size:13px;color:var(--muted);margin-bottom:8px}
.doli-input-wrap{position:relative}
.doli-input{
  width:100%;border:1px solid #e5e7eb;border-radius:12px;
  padding:14px 44px 14px 14px;font-size:16px;outline:none;background:#fff;
  transition:box-shadow .15s,border-color .15s
}
.doli-input:focus{border-color:var(--brand2);box-shadow:0 0 0 6px var(--ring)}
.doli-row{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:8px}
.doli-remember{display:inline-flex;align-items:center;gap:8px;color:var(--muted);font-size:14px}
.doli-cta{margin-top:18px}

.doli-btn {
background: linear-gradient(135deg, #000D25 0%, #1178d1 100%);
    width: 100%;
    border: none;
    padding: clamp(14px, 3vw, 12px);
    border-radius: 12px;
    font-weight: 700;
    font-size: clamp(15px, 4vw, 17px);
    color: #ffffff;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(17, 120, 209, 0.35);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    letter-spacing: normal;
}

.doli-btn:hover {
    background: linear-gradient(135deg, #00163f 0%, #1a8ff2 100%);
    box-shadow: 0 8px 25px rgba(17, 120, 209, 0.55);
    transform: translateY(-2px);
}

.doli-btn:active {
    transform: translateY(0);
    box-shadow: 0 4px 12px rgba(17, 120, 209, 0.45);
}

/* Subtle shine effect on hover */
.doli-btn::after {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
    transition: left 0.6s;
}

.doli-btn:hover::after {
    left: 100%;
}

.doli-help{margin-top:14px;text-align:center;color:var(--muted);font-size:14px}
.doli-help a{color:#0e6ad4;text-decoration:none}
.doli-help a:hover{text-decoration:underline}

/* ---------- Responsive Optimizations ---------- */
@media (max-width: 980px) {
    .doli-login-shell {
        grid-template-columns: 1fr;
        min-height: 100vh;
    }
    .doli-login-shell::before {
        display: none;
    }
    .doli-left {
        min-height: 38vh;
        padding: 10vw 6vw 6vw;
        place-items: center;
        text-align: center;
    }
    .doli-left-inner {
        min-height: auto;
    }
    .doli-brandmark img {
        height: 140px;
    }
    .doli-social, .doli-support, .doli-copy {
        position: static;
        transform: none;
        margin: 10px 0 0;
        flex-wrap: wrap;
        text-align: center;
        width: 100%;
    }
    .doli-right {
        padding: 20px;
        padding-top: 0;
    }
    .doli-hello, .doli-sub {
        padding-left: 0;
        text-align: center;
    }
}

@media (max-width: 640px) {
    .brand-header{
        top: 12px;
        left: 14px;
    }
    .brand-header img{
        height: 42px;
    }
    .doli-left {
        padding: 16px 20px 26px;
    }
    .doli-brandmark img {
        height: 90px;
    }
    .doli-card{
        border-radius:14px;
        padding:24px 18px;
        width:100%;
    }
}

@media (max-width: 360px) {
    .doli-brandmark img{height: 70px}
    .doli-hello{font-size:28px}
    .doli-sub{font-size:15px}
    .doli-input{font-size:14px}
    .doli-support, .doli-copy{font-size:12px}
    .doli-copy-logo{height:16px}
}

/* Generic Styles that should apply at all breakpoints */
.doli-label,
.doli-remember span,
.doli-support span,
.doli-copy,
.doli-help,
.doli-sub {
  margin-left: 0.75rem;
  color: #000000 !important;
}
.doli-support a {

  color: black !important;
}
.doli-row a {
  font-size: 14px;
  color: #000000 !important;
  text-decoration: none;
}
.doli-input::placeholder {
  color: #666 !important;
}
.login {
  padding-top: 20px;
}
.doli-copy span {
  background: linear-gradient(90deg, #00A8B5, blue);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  -moz-background-clip: text;
  -moz-text-fill-color: transparent;
  display: inline-block;
  font-size: 15px;
}
input:not([type=checkbox], [type=radio]), select, textarea {
  color: black;
  height: 35px;
  margin-bottom: var(--spacing);
}
.doli-support .fa-phone {
  color: #000000 !important;
}
.password-toggle {
  position: absolute;
  right: 14px;
  top: 38%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #000000;
  font-size: 16px;
  padding: 5px;
  transition: color 0.15s;
}
.password-toggle:hover {
  color: #666;
}
.doli-input-wrap {
  position: relative;
}
.doli-input {
  padding-right: 44px;
}
.gradient-text {
  background: linear-gradient(90deg, #00A8B5, blue);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  -moz-background-clip: text;
  -moz-text-fill-color: transparent;
  display: inline-block;
  font-size: 15px;
  text-decoration: none;
}
.gradient-text:hover {
  text-decoration: underline;
}

h2 {
    display: block;
    font-size: 1.5em;
    margin-block-start: 0.83em;
    margin-block-end: 0.83em;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    font-weight: bold;
    unicode-bidi: isolate;
    color: #000000 !important;
}

.fa-phone:before {
    content: "\f095";
    position: relative;
    top: 0px;
    left: 13px;
}

</style>

';
?>
<body class="body bodylogin">
<div class="doli-login-shell">
    
    <!-- HEADER LOGO (top-left) -->
	<div class="brand-header">
    <a href="https://realcoresolutions.com/" target="_blank" rel="noopener noreferrer">
        <h2><?php echo $langs->trans("Growth ERP"); ?></h2>
    </a>
	</div>

  <!-- LEFT BRAND PANEL -->
  <section class="doli-left">
    <div class="doli-left-inner">
      <div class="doli-logo">
        <img src="/dolibarr/public/mybrand/img/realcore.png" alt="Logo">
      </div>

      <div class="doli-brandmark">
        <!-- for local host -->
         <a href="https://realcoresolutions.com/" target="_blank" rel="noopener noreferrer">
        <img src="/dolibarr/public/mybrand/img/Realcore logo-02.svg" alt="">
        <!-- <img src="/public/mybrand/img/Realcore logo-02.svg" alt="Realcore Solutions"> -->
      </a>
      </div>

      <div class="doli-social">
        <a href="https://www.instagram.com/realcoresolutions/" target="_blank" aria-label="Instagram">
            <!-- for locat host -->
          <img src="/dolibarr/public/mybrand/img/instagram.svg" alt="">
          <!-- <img src="/public/mybrand/img/instagram-svgrepo-com.svg" alt=""> -->
        </a>
        <a href="https://www.linkedin.com/company/realcore-solutions/" target="_blank" aria-label="LinkedIn">
          <!-- for local host -->
        <img src="/dolibarr/public/mybrand/img/linkedin.svg" alt="">
          <!-- <img src="/public/mybrand/img/linkedin-svgrepo-com.svg" alt="Realcore Solutions"> -->
        </a>
        <a href="https://www.facebook.com/realcoresolution/" target="_blank" aria-label="Facebook">
          <!-- for local host -->
          <img src="/dolibarr/public/mybrand/img/facebooknew.svg" alt="">
          <!-- <img src="/public/mybrand/img/facebook-boxed-svgrepo-com (1).svg" alt="Realcore Solutions"> -->
        </a>
      </div>

    </div>
  </section>

  <!-- RIGHT FORM CARD -->
  <section class="doli-right">
    <div class="doli-card">
      <b><h1 class="doli-hello"><?php echo $langs->trans("Welcome to Client Portal"); ?></h1></b> 
      <p class="doli-sub"><?php echo $langs->trans("Please Login To Your Account"); ?></p>

      <form class="login" method="POST">
        <?php echo $context->getFormToken(); ?>
        <input type="hidden" name="action_login" value="login">

        <div class="doli-field">
          <label class="doli-label" for="username"><?php echo $langs->trans("loginWebportalUserName"); ?></label>
          <div class="doli-input-wrap">
            <!-- <i class="login__icon fas fa-user"></i> -->
            <input class="doli-input" type="text" id="username" name="login" placeholder="<?php print dol_escape_htmltag($langs->trans('loginWebportalUserName')); ?>">
          </div>
        </div>
        <div class="doli-field">
          <label class="doli-label" for="password"><?php echo $langs->trans("Password"); ?></label>
          <div class="doli-input-wrap">
            <input class="doli-input" type="password" id="password" name="password" placeholder="<?php print dol_escape_htmltag($langs->trans('Password')) ?>">
            <span class="password-toggle" id="togglepassword">
              <i class="fa-solid fa-eye"></i>
            </span>
          </div>
        </div>
        <div class="doli-row">
          <label class="doli-remember">
            <input type="checkbox" name="rememberme" value="1">
            <span><?php echo $langs->trans("RememberMe"); ?></span>
          </label>
          <a href="#"><?php echo $langs->trans("Forgot Password?"); ?></a>
        </div>

        <div class="doli-cta">
          <button class="doli-btn" type="submit"><?php print dol_escape_htmltag($langs->trans('Connection')) ?></button>
        </div>

        <p class="doli-help">
          ️ <i class="fa-solid fa-lock"></i>
          <?php echo $langs->trans("Your Connection Is Secure And Encrypted"); ?>
        </p>

        <div class="doli-support">
          <i class="fa-solid fa-phone" style="color: #000000;"></i>
          <span>Need help? <a href="/dolibarr/public/ticket/index.php?entity=1" target="_blank" rel="noopener">Contact Support</a></span>
        </div>

<div class="doli-copy">
  &copy; 2025 by <a href="https://realcoresolutions.com/" target="_blank" rel="noopener"><img src="<?php echo DOL_URL_ROOT; ?>/public/mybrand/img/Realcore logo-02.svg" alt="Realcore Solutions" class="doli-copy-logo"></a>. All Rights Reserved.
</div>
      </form>
    </div>
  <!-- Add the script here -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const togglePassword = document.querySelector('#togglepassword');
  const passwordInput = document.querySelector('#password');

  togglePassword.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    const icon = this.querySelector('i');
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
  });
});
</script>
  </section>
</div>
</body>