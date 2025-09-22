<?php
/* Copyright (C) 2009-2015 	Regis Houssin           <regis.houssin@inodbox.com>
 * Copyright (C) 2011-2022 	Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2024		MDW						<mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024       Frédéric France         <frederic.france@free.fr>
 * Copyright (C) 2024       Charlene Benke          <charlene@patas-monkey.com>
 * Copyright (C) 2025       Marc de Lima Lucio      <marc-dll@user.noreply.github.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

// Need global variable $urllogo, $title and $titletruedolibarrversion to be defined by caller (like dol_loginfunction in security2.lib.php)
// Caller can also set 	$morelogincontent = array(['options']=>array('js'=>..., 'table'=>...);
// $titletruedolibarrversion must be defined

if (!defined('NOBROWSERNOTIF')) {
	define('NOBROWSERNOTIF', 1);
}
/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var Translate $langs
 * @var User $user
 *
 * @var string $dolibarr_main_force_https
 *
 * @var string $captcha
 *
 * @var int<0,1> $dol_hide_leftmenu
 * @var int<0,1> $dol_hide_topmenu
 * @var int<0,1> $dol_no_mouse_hover
 * @var int<0,1> $dol_optimize_smallscreen
 * @var int<0,1> $dol_use_jmobile
 * @var string $focus_element
 * @var string $login
 * @var string $main_authentication
 * @var string $main_home
 * @var string $password
 * @var string $session_name
 * @var string $title
 * @var string $titletruedolibarrversion
 * @var string $urllogo
 * @var int<0,1> $forgetpasslink
 */
// Protection to avoid direct call of template
if (empty($conf) || !is_object($conf)) {
	print "Error, template page can't be called as URL";
	exit(1);
}

// DDOS protection
$size = (empty($_SERVER['CONTENT_LENGTH']) ? 0 : (int) $_SERVER['CONTENT_LENGTH']);
if ($size > 10000) {
	$langs->loadLangs(array("errors", "install"));
	httponly_accessforbidden('<center>'.$langs->trans("ErrorRequestTooLarge").'.<br><a href="'.DOL_URL_ROOT.'">'.$langs->trans("ClickHereToGoToApp").'</a></center>', 413, 1);
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

'
@phan-var-force HookManager $hookmanager
@phan-var-force string $action
@phan-var-force string $captcha
@phan-var-force int<0,1> $dol_hide_leftmenu
@phan-var-force int<0,1> $dol_hide_topmenu
@phan-var-force int<0,1> $dol_no_mouse_hover
@phan-var-force int<0,1> $dol_optimize_smallscreen
@phan-var-force int<0,1> $dol_use_jmobile
@phan-var-force string $focus_element
@phan-var-force string $login
@phan-var-force string $main_authentication
@phan-var-force string $main_home
@phan-var-force string $password
@phan-var-force string $session_name
@phan-var-force string $titletruedolibarrversion
@phan-var-force string $urllogo
@phan-var-force int<0,1> $forgetpasslink
';

/**
 * @var HookManager $hookmanager
 * @var string $action
 * @var string $captcha
 * @var string $message
 * @var string $title
 */


/*
 * View
 */

header('Cache-Control: Public, must-revalidate');

if (GETPOST('dol_hide_topmenu')) {
	$conf->dol_hide_topmenu = 1;
}
if (GETPOST('dol_hide_leftmenu')) {
	$conf->dol_hide_leftmenu = 1;
}
if (GETPOST('dol_optimize_smallscreen')) {
	$conf->dol_optimize_smallscreen = 1;
}
if (GETPOST('dol_no_mouse_hover')) {
	$conf->dol_no_mouse_hover = 1;
}
if (GETPOST('dol_use_jmobile')) {
	$conf->dol_use_jmobile = 1;
}

// If we force to use jmobile, then we reenable javascript
if (!empty($conf->dol_use_jmobile)) {
	$conf->use_javascript_ajax = 1;
}

$php_self = empty($php_self) ? dol_escape_htmltag($_SERVER['PHP_SELF']) : $php_self;
if (!empty($_SERVER["QUERY_STRING"]) && dol_escape_htmltag($_SERVER["QUERY_STRING"])) {
	$php_self .= '?'.dol_escape_htmltag($_SERVER["QUERY_STRING"]);
}
if (!preg_match('/mainmenu=/', $php_self)) {
	$php_self .= (preg_match('/\?/', $php_self) ? '&' : '?').'mainmenu=home';
}
if (preg_match('/'.preg_quote('core/modules/oauth', '/').'/', $php_self)) {
	$php_self = DOL_URL_ROOT.'/index.php?mainmenu=home';
}
$php_self = preg_replace('/(\?|&amp;|&)action=[^&]+/', '\1', $php_self);
$php_self = preg_replace('/(\?|&amp;|&)actionlogin=[^&]+/', '\1', $php_self);
$php_self = preg_replace('/(\?|&amp;|&)afteroauthloginreturn=[^&]+/', '\1', $php_self);
$php_self = preg_replace('/(\?|&amp;|&)username=[^&]*/', '\1', $php_self);
$php_self = preg_replace('/(\?|&amp;|&)entity=\d+/', '\1', $php_self);
$php_self = preg_replace('/(\?|&amp;|&)massaction=[^&]+/', '\1', $php_self);
$php_self = preg_replace('/(\?|&amp;|&)token=[^&]+/', '\1', $php_self);
$php_self = preg_replace('/(&amp;)+/', '&amp;', $php_self);

// Javascript code on logon page only to detect user tz, dst_observed, dst_first, dst_second
$arrayofjs = array(
	'/core/js/dst.js'.(empty($conf->dol_use_jmobile) ? '' : '?version='.urlencode(DOL_VERSION))
);

// We display application title
$application = constant('DOL_APPLICATION_TITLE');
$applicationcustom = getDolGlobalString('MAIN_APPLICATION_TITLE');
if ($applicationcustom) {
	$application = (preg_match('/^\+/', $applicationcustom) ? $application : '').$applicationcustom;
}

// We define login title
if ($applicationcustom) {
	$titleofloginpage = $langs->trans('Login').' '.$application;
} else {
	$titleofloginpage = $langs->trans('Login');
}
// Title of HTML page must have pattern ' @ (?:Doli[a-zA-Z]+ |)(\\d+)\\.(\\d+)\\.([^\\s]+)' to be detected as THE login page by webviews.
$titleofloginpage .= ' @ '.$titletruedolibarrversion; // $titletruedolibarrversion is defined by dol_loginfunction in security2.lib.php. We must keep the @, some tools use it to know it is login page and find true dolibarr version.

$disablenofollow = 1;
if (!preg_match('/'.constant('DOL_APPLICATION_TITLE').'/', $title)) {
	$disablenofollow = 0;
}
if (getDolGlobalString('MAIN_OPTIMIZEFORTEXTBROWSER')) {
	$disablenofollow = 0;
}

// If OpenID Connect is set as an authentication
if (getDolGlobalInt('MAIN_MODULE_OPENIDCONNECT', 0) > 0 && isset($conf->file->main_authentication) && preg_match('/openid_connect/', $conf->file->main_authentication)) {
	// Set a cookie to transfer rollback page information
	$prefix = dol_getprefix('');
	if (empty($_COOKIE["DOL_rollback_url_$prefix"])) {
		dolSetCookie('DOL_rollback_url_'.$prefix, $_SERVER['REQUEST_URI'], time() + 3600);	// $_SERVER["REQUEST_URI"] is for example /mydolibarr/mypage.php
	}

	// Auto redirect if OpenID Connect is the only authentication
	if ($conf->file->main_authentication === 'openid_connect') {
		// Avoid redirection hell
		if (empty(GETPOST('openid_mode'))) {
			dol_include_once('/core/lib/openid_connect.lib.php');
			header("Location: " . openid_connect_get_url(), true, 302);
		} elseif (!empty($_SESSION['dol_loginmesg'])) {
			// Show login error without the login form
			print '<div class="center login_main_message"><div class="error">' . dol_escape_htmltag($_SESSION['dol_loginmesg']) . '</div></div>';
		}
		// We shouldn't continue executing this page
		exit();
	}
}

top_htmlhead('', $titleofloginpage, 0, 0, $arrayofjs, array(), 1, $disablenofollow);

$helpcenterlink = getDolGlobalString('MAIN_HELPCENTER_LINKTOUSE');

$colorbackhmenu1 = '60,70,100'; // topmenu
if (!isset($conf->global->THEME_ELDY_TOPMENU_BACK1)) {
	$conf->global->THEME_ELDY_TOPMENU_BACK1 = $colorbackhmenu1;
}
$colorbackhmenu1 = getDolUserString('THEME_ELDY_ENABLE_PERSONALIZED') ? getDolUserString('THEME_ELDY_TOPMENU_BACK1', $colorbackhmenu1) : getDolGlobalString('THEME_ELDY_TOPMENU_BACK1', $colorbackhmenu1);
$colorbackhmenu1 = implode(',', colorStringToArray($colorbackhmenu1)); // Normalize value to 'x,y,z'

print "<!-- BEGIN PHP TEMPLATE LOGIN.TPL.PHP -->\n";

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style>
:root{
  --brand1:#0f8ea8; --brand2:#1178d1;
  --ink:#171a1f; --muted:#6b7280; --bg:#f7f8fb;
  --card:#ffffff; --ring:rgba(17,120,209,.18);
  --shadow:0 10px 30px rgba(0,0,0,.08); --radius:18px;
}

html,body{
  height:100%;
  background:var(--bg);
  color:var(--ink);
  font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif
}
*{box-sizing:border-box}

/* Page background image */
.body.bodylogin{
  background:url('<?php echo DOL_URL_ROOT; ?>/custom/mybrand/img/background.jpg')
             center center / cover no-repeat fixed !important;
}

/* ---------- Header logo (top-left) ---------- */
.brand-header{position:fixed;top:22px;left:32px;z-index:1000}
.brand-header img{height:70px;width:auto}

/* ---------- Two-column shell + divider ---------- */
.doli-login-shell{
  display:grid;
  grid-template-columns:1fr 1fr;
  min-height:100dvh;
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
  color:#fff; /* only affects small text you add */
}
.doli-left-inner{
  max-width:560px;width:100%;
  display:flex;flex-direction:column;
  align-items:center;justify-content:center;
  gap:14px;min-height:60vh;
}

/* Make the left section a positioning context */
.doli-left{ position:relative; }


/* Hide legacy inline logo block above AMLAK text, if present */
.doli-left .doli-logo{display:none}

/* AMLAK mark */
.doli-brandmark{margin:8px 0 6px}
.doli-brandmark img{
  height:500px; /* tweak as needed; was 500 which overflowed */
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
  bottom: 24px;
  transform: translateX(-50%);
  display: flex;
  justify-content: center;
  gap: 6px;        /* <-- minimal gap */
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
  width: 20px;     /* <-- bigger size */
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
  position:absolute;             /* add */
  left:50%; transform:translateX(-50%);
  bottom:54px;                   /* sits just above copyright */
  display:flex; align-items:center; justify-content:center;
  gap:8px; color:black; font-size:14px
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
  bottom:24px;

  display:flex;                 /* put text + logo on one line */
  align-items:center;           /* vertically center the logo */
  justify-content:center;
  gap:8px;                      /* spacing between chunks */
  flex-wrap:nowrap;             /* don't wrap on desktop */
  color:black;
  font-size:14px;
  line-height:1.2;
}
.doli-copy a{color:#0e6ad4;text-decoration:none}
.doli-copy a:hover{text-decoration:none}

.doli-copy-logo {
   height:16px;                  /* try 26–30px to taste */
  width:auto;
  display:inline-block;
  vertical-align:middle;
  margin:0;                     /* no extra gaps */
  filter:none; 
}

@media (max-width:980px){
  .doli-copy{
    position:static;
    transform:none;
    margin-top:10px;
    flex-wrap:wrap;             /* allow wrapping on small screens */
    gap:6px;
  }
}

/* ---------- Right panel (login card) ---------- */
.doli-right{
	position: relative;
	display:grid;
	place-items:center;
	/* padding:clamp(24px,5vw,56px) */
}
.doli-card{
  width:min(560px,100%);
  border-radius:var(--radius);
  padding:clamp(24px,4vw,40px);
  
}
.doli-hello{
  margin:0 0 4px;
  font-size:30px;
}
.doli-sub{margin:0 0 22px;
  color:black;
  font-weight:500}

/* Form fields */
.doli-field{margin:14px 0 16px}
.doli-label{display:block;font-size:13px;color:black;margin-bottom:8px}
.doli-input-wrap{position:relative}
.doli-input{
  width:100%;border:1px solid #e5e7eb;border-radius:12px;
  padding:14px 44px 14px 14px;font-size:16px;outline:none;background:#fff;
  transition:box-shadow .15s,border-color .15s
}
.doli-input:focus{border-color:var(--brand2);box-shadow:0 0 0 6px var(--ring)}

.doli-row{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:8px}
.doli-remember{display:inline-flex;align-items:center;gap:8px;color:black;font-size:14px}

.doli-cta{margin-top:18px}
.doli-btn{
  width:100%;border:0;padding:14px 18px;border-radius:12px;font-weight:700;font-size:16px;color:#fff;
  background-image:linear-gradient(90deg,var(--brand1),var(--brand2));
  cursor:pointer;box-shadow:0 10px 20px rgba(17,120,209,.25);
  transition:transform .06s,filter .15s,box-shadow .15s
}
.doli-btn:hover{filter:saturate(1.05) brightness(1.03);box-shadow:0 14px 28px rgba(17,120,209,.28)}
.doli-btn:active{transform:translateY(1px)}

.doli-help{margin-top:14px;text-align:center;color:black;font-size:14px}
.doli-help a{color:#0e6ad4;text-decoration:none}
.doli-help a:hover{text-decoration:underline}

/* ---------- Responsive ---------- */
@media (max-width:980px){
  .doli-login-shell{grid-template-columns:1fr}
  .doli-login-shell::before{display:none}
  .doli-brandmark img{height:90px}
  .doli-left{min-height:34vh}
}
@media (max-width:640px){
  .brand-header{top:14px;left:16px}
  .brand-header img{height:36px}
}

@media (max-width:980px){
  .doli-login-shell{
    grid-template-columns:1fr;     /* one column */
  }
  .doli-login-shell::before{
    display:none;                  /* remove the vertical divider */
  }

  /* Left “brand” panel shrinks to a nice hero banner */
  .doli-left{
    min-height:38vh;               /* shorter hero */
    padding:10vw 6vw 6vw;
    place-items:center start;      /* center horizontally; start vertically */
    text-align:center;
  }
  .doli-brandmark img{height:140px}

  /* Social strip drops under the banner automatically (thanks to your flex) */
}


/* ──────────────────────────────────────────────────────────── */
/* 3) MOBILE  (≤ 640 px) –  tighter gutters & smaller artwork  */
/* ──────────────────────────────────────────────────────────── */
@media (max-width:640px){
  .brand-header{top:12px;left:14px}
  .brand-header img{height:42px}

  .doli-left{
    padding:16px 20px 26px;
    min-height:auto;
  }
  .doli-brandmark img{height:90px}

  /* Make the login card breathe but stay edge-to-edge */
  .doli-right{padding:20px}
  .doli-card{
    border-radius:14px;
    padding:24px 18px;
    width:100%;
    box-shadow:var(--shadow);      /* keep the shadow even on phones */
  }

  /* Inputs: full width & bigger hit area */
  .doli-input{padding:14px 16px;font-size:15px}
  .doli-btn{padding:14px;font-size:15px}
}


/* ──────────────────────────────────────────────────────────── */
/* 4) ULTRA-NARROW (≤ 360 px) –  last-ditch tweaks             */
/* ──────────────────────────────────────────────────────────── */
@media (max-width:360px){
  .doli-brandmark img{height:70px}
  .doli-hello{font-size:28px}
  .doli-sub{font-size:15px}
  .doli-input{font-size:14px}
}

/* ─────  FOOTER RESPONSIVENESS  ───────────────────────────── */

/* Tablet & smaller (≤ 980 px) – let the footer flow */
@media (max-width:980px){
  /* Support strip */
  .doli-support{
    position:static;           /* take part in normal flow   */
    transform:none;
    margin:24px 0 10px;        /* breathing room             */
    flex-wrap:wrap;            /* allow line breaks          */
    text-align:center;
  }

  /* Copyright strip */
  .doli-copy{
    position:static;
    transform:none;
    margin:10px 0 0;
    flex-wrap:wrap;            /* logo + text can wrap       */
    gap:4px;                   /* tighter gap on small scrn  */
    text-align:center;
  }
  .doli-copy-logo{height:20px} /* shrink logo a touch        */
}

/* Phones (≤ 640 px) – tighten a bit more */
@media (max-width:640px){
  .doli-support{margin:20px 0 8px;font-size:13px}
  .doli-copy  {font-size:13px;line-height:1.3}
  .doli-copy-logo{height:18px}
}

/* Ultra-narrow (≤ 360 px) – final tweak */
@media (max-width:360px){
  .doli-support,.doli-copy{font-size:12px}
  .doli-copy-logo{height:16px}
}

.doli-copy span {
  background: linear-gradient(90deg, var(--brand1), var(--brand2));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  display: inline-block;
}

</style>



<body class="body bodylogin">


<?php if (empty($conf->dol_use_jmobile)) { ?>
<script>
$(document).ready(function () {
	/* Set focus on correct field */
	<?php if ($focus_element) {
		?>$('#<?php echo $focus_element; ?>').focus(); <?php
	} ?>		// Warning to use this only on visible element
});
</script>
<?php } ?>

<div class="doli-login-shell">

	<!-- <div class="brand-header">
  <img src="<?php echo DOL_URL_ROOT; ?>/public/mybrand/img/realcore.png" alt="Realcore Solutions">
	</div> -->

  <!-- LEFT BRAND PANEL -->
  <section class="doli-left">
    <div class="doli-left-inner">
      <div class="doli-logo">
        <img src="<?php echo DOL_URL_ROOT; ?>/public/mybrand/img/realcore.png" alt="Logo">
      </div>

	<?php
$appTitle = getDolGlobalString('MAIN_APPLICATION_TITLE', 'AMLAK 360');
$brandLogoRel  = '/public/mybrand/img/Realcore_solution_logo.svg';                // <- put your logo file here
$brandLogoPath = DOL_DOCUMENT_ROOT.$brandLogoRel;

if (is_readable($brandLogoPath)) {
    echo '<div class="doli-brandmark">
            <img src="'.DOL_URL_ROOT.$brandLogoRel.'" alt="'.dol_escape_htmltag($appTitle).'">
          </div>';
} else {
    // Fallback to text if the image isn’t found
    echo '<div class="doli-product">'.dol_escape_htmltag($appTitle).'</div>';
}
?>
	<div class="doli-social">
  <a href="https://instagram.com/yourpage" target="_blank" aria-label="Instagram">
    <img src="<?php echo DOL_URL_ROOT; ?>/public/mybrand/img/instagram-svgrepo-com.svg" alt="">
  </a>
  <a href="https://linkedin.com/company/yourpage" target="_blank" aria-label="LinkedIn">
    <img src="<?php echo DOL_URL_ROOT; ?>/public/mybrand/img/linkedin-svgrepo-com.svg" alt="">
  </a>
  <a href="https://facebook.com/yourpage" target="_blank" aria-label="Facebook">
    <img src="<?php echo DOL_URL_ROOT; ?>/public/mybrand/img/facebook-boxed-svgrepo-com (1).svg" alt="">
  </a>
</div>

    </div>
  </section>

  <!-- RIGHT FORM CARD -->
  <section class="doli-right">
    <div class="doli-card">
      <h1 class="doli-hello"><?php echo $langs->trans("Welcome to Employee Portal"); ?></h1>
      <p class="doli-sub"><?php echo $langs->trans("Please Login To Your Account"); ?></p>

      <form id="login" name="login" method="post" action="<?php echo $php_self; ?>">
        <input type="hidden" name="token" value="<?php echo newToken(); ?>" />
        <input type="hidden" name="actionlogin" id="actionlogin" value="login">
        <input type="hidden" name="loginfunction" id="loginfunction" value="loginfunction" />
        <input type="hidden" name="backtopage" value="<?php echo GETPOST('backtopage'); ?>" />
        <!-- keep tz/screen/flags -->
        <input type="hidden" name="tz" id="tz" value="" />
        <input type="hidden" name="tz_string" id="tz_string" value="" />
        <input type="hidden" name="dst_observed" id="dst_observed" value="" />
        <input type="hidden" name="dst_first" id="dst_first" value="" />
        <input type="hidden" name="dst_second" id="dst_second" value="" />
        <input type="hidden" name="screenwidth" id="screenwidth" value="" />
        <input type="hidden" name="screenheight" id="screenheight" value="" />
        <input type="hidden" name="dol_hide_topmenu" id="dol_hide_topmenu" value="<?php echo $dol_hide_topmenu; ?>" />
        <input type="hidden" name="dol_hide_leftmenu" id="dol_hide_leftmenu" value="<?php echo $dol_hide_leftmenu; ?>" />
        <input type="hidden" name="dol_optimize_smallscreen" id="dol_optimize_smallscreen" value="<?php echo $dol_optimize_smallscreen; ?>" />
        <input type="hidden" name="dol_no_mouse_hover" id="dol_no_mouse_hover" value="<?php echo $dol_no_mouse_hover; ?>" />
        <input type="hidden" name="dol_use_jmobile" id="dol_use_jmobile" value="<?php echo $dol_use_jmobile; ?>" />

        <?php if (!isset($conf->file->main_authentication) || $conf->file->main_authentication != 'googleoauth') { ?>
          <div class="doli-field">
            <label class="doli-label" for="username"><?php echo $langs->trans("Login"); ?></label>
            <div class="doli-input-wrap">
              <input class="doli-input" type="text" id="username" maxlength="255"
                     placeholder="<?php echo $langs->trans("User Name"); ?>" name="username"
                     value="<?php echo dol_escape_htmltag($login); ?>" tabindex="1"
                     autocapitalize="off" autocomplete="on" spellcheck="false" autocorrect="off" autofocus>
            </div>
          </div>

          <div class="doli-field">
            <label class="doli-label" for="password"><?php echo $langs->trans("Password"); ?></label>
            <div class="doli-input-wrap">
              <input class="doli-input" type="password" id="password" maxlength="128"
                     placeholder="<?php echo $langs->trans("Password"); ?>" name="password"
                     value="<?php echo dol_escape_htmltag($password); ?>" tabindex="2"
                     autocomplete="<?php echo !getDolGlobalString('MAIN_LOGIN_ENABLE_PASSWORD_AUTOCOMPLETE') ? 'off' : 'on'; ?>">
              <?php
                include_once DOL_DOCUMENT_ROOT.'/core/lib/security2.lib.php';
                print showEyeForField('togglepassword', 'password');
              ?>
            </div>
          </div>
        <?php } ?>

        <!-- Captcha (unchanged, from original) -->
        <?php
          if (!empty($captcha)) {
            $php_self = preg_replace('/[&\?]time=(\d+)/', '', $php_self);
            if (preg_match('/\?/', $php_self)) $php_self .= '&time='.dol_print_date(dol_now(), 'dayhourlog');
            else $php_self .= '?time='.dol_print_date(dol_now(), 'dayhourlog');

            $dirModCaptcha = array_merge(array('main' => '/core/modules/security/captcha/'), (isset($conf->modules_parts['captcha']) && is_array($conf->modules_parts['captcha'])) ? $conf->modules_parts['captcha'] : array());
            $fullpathclassfile = '';
            foreach ($dirModCaptcha as $dir) { $fullpathclassfile = dol_buildpath($dir."modCaptcha".ucfirst($captcha).'.class.php', 0, 2); if ($fullpathclassfile) break; }
            if ($fullpathclassfile) {
              include_once $fullpathclassfile;
              $classname = "modCaptcha".ucfirst($captcha);
              if (class_exists($classname)) {
                $captchaobj = new $classname($db, $conf, $langs, null);
                if (is_object($captchaobj) && method_exists($captchaobj, 'getCaptchaCodeForForm')) {
                  print $captchaobj->getCaptchaCodeForForm($php_self);
                } else { print 'Error, captcha handler has no getCaptchaCodeForForm()'; }
              } else { print 'Error, captcha handler class '.$classname.' not found'; }
            } else { print 'Error, captcha handler '.$captcha.' not found'; }
          }
        ?>

        <?php if (!isset($conf->file->main_authentication) || $conf->file->main_authentication != 'googleoauth') { ?>
          <div class="doli-row">
            <label class="doli-remember">
              <input type="checkbox" name="rememberme" value="1">
              <span><?php echo $langs->trans("RememberMe"); ?></span>
            </label>

            <?php
              $url = DOL_URL_ROOT.'/user/passwordforgotten.php';
              if (getDolGlobalString('MAIN_PASSWORD_FORGOTLINK')) $url = getDolGlobalString('MAIN_PASSWORD_FORGOTLINK');
              echo '<a href="'.dol_escape_htmltag($url).'">'.$langs->trans("PasswordForgotten").'</a>';
            ?>
          </div>

          <div class="doli-cta">
            <button class="doli-btn" type="submit" tabindex="5"><?php echo $langs->trans('Connection'); ?></button>
          </div>
        <?php } ?>

        <p class="doli-help">
          <i class="fa-solid fa-lock"></i>  
          <?php echo $langs->trans("Your Connection Is Secure And Encrypted"); ?>
          <?php if ($helpcenterlink) { ?> ·
            <a href="<?php echo dol_escape_htmltag($helpcenterlink); ?>" target="_blank" rel="noopener"><?php echo $langs->trans('NeedHelpCenter'); ?></a>
          <?php } ?>
        </p>

        <!-- Keep hooks/extras -->
        <?php
          if (!empty($morelogincontent)) {
            if (is_array($morelogincontent)) {
              foreach ($morelogincontent as $format => $option) if ($format == 'table') echo $option;
            } else { echo $morelogincontent; }
          }
        ?>

        <!-- Keep OpenID/Google blocks & error handling as in original file -->
      </form>

		  <?php
  // Pick an existing help center link if set, else fallback to a mailto:
  $supportLink = $helpcenterlink ?: 'mailto:support@realcoresolutions.com';
?>
<div class="doli-support">
  <!-- tiny headset icon (inline SVG so no extra file needed) -->
  <svg class="icon-16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
    <path d="M12 2a8 8 0 0 0-8 8v6a3 3 0 0 0 3 3h2v-6H7v-3a5 5 0 1 1 10 0v3h-2v6h2a3 3 0 0 0 3-3v-6a8 8 0 0 0-8-8zM9 20h6v2H9z"/>
  </svg>
  <span>Need help? <a href="/dolibarr/public/ticket/index.php?entity=1" target="_blank" rel="noopener">Contact Support</a></span>
</div>


<div class="doli-copy">
  &copy; 2025 by <span style="background: linear-gradient(90deg, #0f8ea8, #1178d1); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Realcore Solutions</span>. All Rights Reserved.
  <a class="doli-copy-brand" href="https://realcoresolutions.com" target="_blank" rel="noopener">
    <span class="sr-only"></span>
  </a>
</div>


      <?php
        // Error messages, OpenID/Google, MAIN_EASTER_EGG_COMMITSTRIP, MAIN_HTML_FOOTER, hooks...
        // Keep the original blocks from your file below this <form> as-is.
      ?>
    </div>
  </section>
</div>


<?php
$message = '';
// Show error message if defined
if (!empty($_SESSION['dol_loginmesg'])) {
	$message = $_SESSION['dol_loginmesg'];	// By default this is an error message
}
if (!empty($message)) {
	if (!empty($conf->use_javascript_ajax)) {
		if (preg_match('/<!-- warning -->/', $message)) {	// if it contains this comment, this is a warning message
			$message = str_replace('<!-- warning -->', '', $message);
			dol_htmloutput_mesg($message, array(), 'warning');
		} else {
			dol_htmloutput_mesg($message, array(), 'error');
		}
		print '<script>
			$(document).ready(function() {
				$(".jnotify-container").addClass("jnotify-container-login");
			});
		</script>';
	} else {
		?>
		<div class="center login_main_message">
		<?php
		if (preg_match('/<!-- warning -->/', $message)) {	// if it contains this comment, this is a warning message
			$message = str_replace('<!-- warning -->', '', $message);
			print '<div class="warning" role="alert">';
		} else {
			print '<div class="error" role="alert">';
		}
		print dol_escape_htmltag($message);
		print '</div>'; ?>
		</div>
		<?php
	}
}

// Add commit strip
if (getDolGlobalString('MAIN_EASTER_EGG_COMMITSTRIP')) {
	include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';
	if (substr($langs->defaultlang, 0, 2) == 'fr') {
		$resgetcommitstrip = getURLContent("https://www.commitstrip.com/fr/feed/");
	} else {
		$resgetcommitstrip = getURLContent("https://www.commitstrip.com/en/feed/");
	}
	if ($resgetcommitstrip && $resgetcommitstrip['http_code'] == '200') {
		if (LIBXML_VERSION < 20900) {
			// Avoid load of external entities (security problem).
			// Required only if LIBXML_VERSION < 20900
			// @phan-suppress-next-line PhanDeprecatedFunctionInternal
			libxml_disable_entity_loader(true);
		}

		$xml = simplexml_load_string($resgetcommitstrip['content'], 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);
		// @phan-suppress-next-line PhanPluginUnknownObjectMethodCall
		$little = $xml->channel->item[0]->children('content', true);
		print preg_replace('/width="650" height="658"/', '', $little->encoded);
	}
}

?>

<?php if ($main_home) {
	?>
	<div class="center login_main_home paddingtopbottom <?php echo !getDolGlobalString('MAIN_LOGIN_BACKGROUND') ? '' : ' backgroundsemitransparent boxshadow'; ?>" style="max-width: 70%">
	<?php echo $main_home; ?>
	</div><br>
	<?php
}
?>

<!-- authentication mode = <?php echo $main_authentication ?> -->
<!-- cookie name used for this session = <?php echo $session_name ?> -->
<!-- urlfrom in this session = <?php echo isset($_SESSION["urlfrom"]) ? $_SESSION["urlfrom"] : ''; ?> -->

<!-- Common footer is not used for login page, this is same than footer but inside login tpl -->

<?php

print getDolGlobalString('MAIN_HTML_FOOTER');

if (!empty($morelogincontent) && is_array($morelogincontent)) {
	foreach ($morelogincontent as $format => $option) {
		if ($format == 'js') {
			echo "\n".'<!-- Javascript by hook -->';
			echo $option."\n";
		}
	}
} elseif (!empty($moreloginextracontent)) {
	echo '<!-- Javascript by hook -->';
	echo $moreloginextracontent;
}

// Can add extra content
$parameters = array();
$dummyobject = new stdClass();
$result = $hookmanager->executeHooks('getLoginPageExtraContent', $parameters, $dummyobject, $action);
print $hookmanager->resPrint;

?>




</body>
</html>
<!-- END PHP TEMPLATE -->
