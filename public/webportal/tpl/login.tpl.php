<?php
/* Copyright (C) 2024		MDW							<mdeweerd@users.noreply.github.com>
 */
// Protection to avoid direct call of template
if (empty($context) || !is_object($context)) {
	print "Error, template page can't be called as URL";
	exit(1);
}
'@phan-var-force Context $context';
print '

<!-- Load Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<style>
:root{
  --brand1:#0f8ea8; --brand2:#1178d1;
  // --ink:#171a1f; --muted:#6b7280; --bg:#f7f8fb;
  // --card:#ffffff; --ring:rgba(17,120,209,.18);
  // --shadow:0 10px 30px rgba(0,0,0,.08); --radius:18px;
}

html,body{
  height:100%;
  background:var(--bg);
  color:var(--ink);
  font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;
  margin: 0;
  padding: 0;
  overflow: hidden; /* Prevent scrollbars that might cause white space */
}
*{box-sizing:border-box}

/* Page background image */
body.bodylogin {
  background:url("/public/mybrand/img/background icon.jpg")
             center center / cover no-repeat fixed !important;
  margin: 0;
  padding: 0;
  height: 100vh; /* Ensure full viewport height */
  overflow: auto; /* Allow scrolling if needed */
}

/* ---------- Header logo (top-left) ---------- */
.brand-header{position:fixed;top:22px;left:32px;z-index:1000}
.brand-header img{height:70px;width:auto}

/* ---------- Two-column shell + divider ---------- */
.doli-login-shell{
  display:grid;
  grid-template-columns:1fr 1fr;
  min-height:100vh; /* Changed from 100dvh to 100vh for better compatibility */
  position:relative;
}
.doli-login-shell::before{
  content:"";
  position:absolute;
  top:130px;bottom:130px;left:50%;
  width:2px;background:black;
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
  min-height: 100%; /* Changed from 60vh to 100% */
}

/* Make the left section a positioning context */
.doli-left{
  position:relative;
}

/* Hide legacy inline logo block above AMLAK text, if present */
.doli-left .doli-logo{display:none}

/* AMLAK mark */
.doli-brandmark{margin:8px 0 6px}
.doli-brandmark img{
 margin-top: 38px;
  height:500px;
  width:auto;max-width:100%;
}

/* Tagline beneath AMLAK */
.doli-tagline{
  font-size:clamp(14px,1.4vw,18px);
  opacity:.95; color:#171a1f;
}

/* Social icons footer */
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

/* On small screens place under logo normally */
@media (max-width:980px){
  .doli-social {
    position: static;
    transform: none;
    margin-top: 16px;
  }
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

@media (max-width:980px){
  .doli-copy{
    position:static;
    transform:none;
    margin-top:10px;
    flex-wrap:wrap;
    gap:6px;
  }
  .doli-copy-logo{height:20px}
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
  width:78%;
  padding:clamp(24px,4vw,40px);
}
.doli-hello{margin:0 0 4px;
font-size: 30px;
padding-left: 30px;
font-weight: bold;
}
.doli-sub{ 
 padding-left: 30px;
margin-bottom:-48px;color:var(--muted);
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
.doli-btn{
  width:100%;border:0;padding:11px 18px;border-radius:12px;font-weight:700;font-size:16px;color:#fff;
  background-image:linear-gradient(90deg,var(--brand1),var(--brand2));
  transition:transform .06s,filter .15s,box-shadow .15s
}
.doli-btn:hover{filter:saturate(1.05) brightness(1.03);box-shadow:0 14px 28px rgba(17,120,209,.28)}
.doli-btn:active{transform:translateY(1px)}

.doli-help{margin-top:14px;text-align:center;color:var(--muted);font-size:14px}
.doli-help a{color:#0e6ad4;text-decoration:none}
.doli-help a:hover{text-decoration:underline}

/* ---------- Responsive ---------- */
@media (max-width:980px){
  .doli-login-shell{grid-template-columns:1fr}
  .doli-login-shell::before{display:none}
  .doli-brandmark img{height:90px}
  .doli-left{min-height:34vh}
  
  /* Fix for mobile white space */
  body.bodylogin {
    overflow: auto;
    height: auto;
    min-height: 100vh;
  }
}
@media (max-width:640px){
  .brand-header{top:14px;left:16px}
  .brand-header img{height:36px}
}

@media (max-width:980px){
  .doli-login-shell{
    grid-template-columns:1fr;
  }
  .doli-login-shell::before{
    display:none;
  }

  .doli-left{
    min-height:38vh;
    padding:10vw 6vw 6vw;
    place-items:center start;
    text-align:center;
  }
  .doli-brandmark img{height:140px}
}

@media (max-width:640px){
  .brand-header{top:12px;left:14px}
  .brand-header img{height:42px}

  .doli-left{
    padding:16px 20px 26px;
    min-height:auto;
  }
  .doli-brandmark img{height:90px}

  .doli-right{padding:20px}
  .doli-card{
    border-radius:14px;
    padding:24px 18px;
    width:100%;
    box-shadow:var(--shadow);
  }

  .doli-input{padding:14px 16px;font-size:15px}
  .doli-btn{padding:14px;font-size:16px}
}

@media (max-width:360px){
  .doli-brandmark img{height:70px}
  .doli-hello{font-size:28px}
  .doli-sub{font-size:15px}
  .doli-input{font-size:14px}
}

@media (max-width:980px){
  .doli-support{
    position:static;
    transform:none;
    margin:24px 0 10px;
    flex-wrap:wrap;
    text-align:center;
  }

  .doli-copy{
    position:static;
    transform:none;
    margin:10px 0 0;
    flex-wrap:wrap;
    gap:4px;
    text-align:center;
  }
  .doli-copy-logo{height:20px}
}

@media (max-width:640px){
  .doli-support{margin:20px 0 8px;font-size:13px}
  .doli-copy  {font-size:13px;line-height:1.3}
  .doli-copy-logo{height:18px}
}

@media (max-width:360px){
  .doli-support,.doli-copy{font-size:12px}
  .doli-copy-logo{height:16px}
}

/* Force all text to black except specific links */
.doli-label,
.doli-remember span,
.doli-support span,
.doli-copy,
.doli-help,
.doli-sub {

  color: #000000 !important; /* Force black color */
}

/* Keep the Contact Support link blue */
.doli-support a {
  color: #0e6ad4 !important; /* Keep blue for this specific link */
}

/* Make the "Password forgotten?" link black too */
.doli-row a {
  font-size: 14px;
  color: #000000 !important;
  text-decoration: none;
}

/* Optional: Make the placeholder text darker */
.doli-input::placeholder {
  color: #666 !important;
}

.login {
    padding-top: 46px;
}

.doli-copy span {
margin-bottom: 2px;
  background: linear-gradient(90deg, #00A8B5, blue);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  -moz-background-clip: text; /* Firefox support */
  -moz-text-fill-color: transparent; /* Firefox support */
  display: inline-block;
  font-size: 15px;
}

input:not([type=checkbox], [type=radio]), select, textarea {
    height: 35px;
    margin-bottom: var(--spacing);
}
.doli-support .fa-phone {
  color: #000000 !important;
}
</style>
';
?>
<body class="body bodylogin">
<div class="doli-login-shell">
    
    <!-- HEADER LOGO (top-left) -->
	<div class="brand-header">
  <!-- <img src="/dolibarr/public/mybrand/img/realcore.png" alt="Realcore Solutions"> -->
	</div>

  <!-- LEFT BRAND PANEL -->
  <section class="doli-left">
    <div class="doli-left-inner">
      <div class="doli-logo">
        <img src="/dolibarr/public/mybrand/img/realcore.png" alt="Logo">
      </div>

      <div class="doli-brandmark">
        <img src="/dolibarr/public/mybrand/img/Realcore_solution_logo.svg" alt="AMLAK 360">
      </div>

      <div class="doli-social">
        <a href="https://instagram.com/yourpage" target="_blank" aria-label="Instagram">
          <img src="/dolibarr/public/mybrand/img/instagram-svgrepo-com.svg" alt="">
        </a>
        <a href="https://linkedin.com/company/yourpage" target="_blank" aria-label="LinkedIn">
          <img src="/dolibarr/public/mybrand/img/linkedin-svgrepo-com.svg" alt="">
        </a>
        <a href="https://facebook.com/yourpage" target="_blank" aria-label="Facebook">
          <img src="/dolibarr/public/mybrand/img/facebook-boxed-svgrepo-com (1).svg" alt="">
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
            <!-- <i class="login__icon fas fa-lock"></i> -->
            <input class="doli-input" type="password" id="password" name="password" placeholder="<?php print dol_escape_htmltag($langs->trans('Password')) ?>">
          </div>
        </div>
        <div class="doli-row">
          <label class="doli-remember">
            <input type="checkbox" name="rememberme" value="1">
            <span><?php echo $langs->trans("RememberMe"); ?></span>
          </label>
          <a href="#"><?php echo $langs->trans("PasswordForgotten"); ?></a>
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
  &copy; 2025 by <span>Realcore Solutions</span>. All Rights Reserved.
  <a class="doli-copy-brand" href="https://realcoresolutions.com" target="_blank" rel="noopener">
    <span class="sr-only"></span>
  </a>
</div>
      </form>
    </div>
  </section>
</div>
</body>