<?php
/* Copyright (C) 2001-2002  Rodolphe Quiedeville    <rodolphe@quiedeville.org>
 * Copyright (C) 2001-2002  Jean-Louis Bergamo      <jlb@j1b.org>
 * Copyright (C) 2006-2013  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2012       Regis Houssin           <regis.houssin@inodbox.com>
 * Copyright (C) 2012       J. Fernando Lagrange    <fernando@demo-tic.org>
 * Copyright (C) 2018-2025  Frédéric France         <frederic.france@free.fr>
 * Copyright (C) 2018       Alexandre Spangaro      <aspangaro@open-dsi.fr>
 * Copyright (C) 2024-2025	MDW						<mdeweerd@users.noreply.github.com>
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

/**
 *	\file       htdocs/public/project/new.php
 *	\ingroup    project
 *	\brief      Page to record a message/lead into a project/lead
 */

if (!defined('NOLOGIN')) {
	define("NOLOGIN", 1); // This means this output page does not require to be logged.
}
if (!defined('NOCSRFCHECK')) {
	define("NOCSRFCHECK", 1); // We accept to go on this page from external web site.
}
if (!defined('NOIPCHECK')) {
	define('NOIPCHECK', '1'); // Do not check IP defined into conf $dolibarr_main_restrict_ip
}
if (!defined('NOBROWSERNOTIF')) {
	define('NOBROWSERNOTIF', '1');
}


// For MultiCompany module.
// Do not use GETPOST here, function is not defined and define must be done before including main.inc.php
// Because 2 entities can have the same ref.
$entity = (!empty($_GET['entity']) ? (int) $_GET['entity'] : (!empty($_POST['entity']) ? (int) $_POST['entity'] : 1));
if (is_numeric($entity)) {
	define("DOLENTITY", $entity);
}

// Load Dolibarr environment
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';


// Init vars
$errmsg = '';
$error = 0;
$backtopage = GETPOST('backtopage', 'alpha');
$action = GETPOST('action', 'aZ09');

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Societe $mysoc
 * @var Translate $langs
 * @var User $user
 */

// Load translation files
$langs->loadLangs(array("members", "companies", "install", "other", "projects"));

if (!getDolGlobalString('PROJECT_ENABLE_PUBLIC')) {
	print $langs->trans("Form for public lead registration has not been enabled");
	exit;
}

// Initialize a technical object to manage hooks of page. Note that conf->hooks_modules contains an array of hook context
$hookmanager->initHooks(array('publicnewleadcard', 'globalcard'));

$extrafields = new ExtraFields($db);

$object = new Project($db);

$user->loadDefaultValues();

// Security check
if (empty($conf->project->enabled)) {
	httponly_accessforbidden('Module Project not enabled');
}


/**
 * Show header for new member
 *
 * Note: also called by functions.lib:recordNotFound
 *
 * @param 	string		$title				Title
 * @param 	string		$head				Head array
 * @param 	int    		$disablejs			More content into html header
 * @param 	int    		$disablehead		More content into html header
 * @param 	string[]|string	$arrayofjs			Array of complementary js files
 * @param 	string[]|string	$arrayofcss			Array of complementary css files
 * @return	void
 */
function llxHeaderVierge($title, $head = "", $disablejs = 0, $disablehead = 0, $arrayofjs = [], $arrayofcss = [])
{
    global $conf, $langs, $mysoc;

    // Standard HTML head
    top_htmlhead($head, $title, $disablejs, $disablehead, $arrayofjs, $arrayofcss);
    print '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
	print '<body id="mainbody" class="publicnewmemberform">';

    // Topbar (before Dolibarr public header)
    $email = $mysoc->email ?: getDolGlobalString('MAIN_INFO_SOCIETE_MAIL');
    $tel1  = $mysoc->phone ?: getDolGlobalString('MAIN_INFO_SOCIETE_TEL');
    $tel2  = $mysoc->phone_mobile ?: getDolGlobalString('MAIN_INFO_SOCIETE_TEL2');
    $tel1h = preg_replace('/[^\d\+]/', '', (string) $tel1);
    $tel2h = preg_replace('/[^\d\+]/', '', (string) $tel2);

    print '
    <style>
      .public-topbar{background:#f8f9fb;border-bottom:1px solid #e6e9ef;font-size:13px}
      .public-topbar__in{max-width:1200px;margin:0 auto;padding:6px 12px;
                         display:flex;gap:12px;justify-content:space-between;align-items:center;flex-wrap:wrap}
      .public-topbar .left,.public-topbar .right{display:flex;gap:16px;align-items:center;flex-wrap:wrap}
      .public-topbar a{color:#162a4a;text-decoration:none}
      .public-topbar a:hover{text-decoration:underline}
      .public-topbar .item{display:flex;align-items:center;gap:8px;white-space:nowrap}
      .public-topbar .sep{opacity:.35}
      @media (max-width:640px){ .public-topbar__in{flex-direction:column;align-items:flex-start} }
    </style>
	';
    // <div class="public-topbar">
    //   <div class="public-topbar__in">
    //     <div class="left">'.
    //       ($email ? '<span class="item">✉ <a href="mailto:'.dol_escape_htmltag($email).'">'.dol_escape_htmltag($email).'</a></span>' : '').
    //       ($tel1  ? '<span class="item">☎ <a href="tel:'.$tel1h.'">'.dol_escape_htmltag($tel1).'</a></span>' : '').
    //       ($tel2  ? '<span class="item">📱 <a href="tel:'.$tel2h.'">'.dol_escape_htmltag($tel2).'</a></span>' : '').
    //     '</div>
    //     <div class="right">
    //       <a href="/support" class="item">Support</a>
    //       <span class="sep">|</span>
    //       <a href="'.DOL_URL_ROOT.'/index.php" class="item">Login</a>
    //     </div>
    //   </div>
    // </div>';

    // Draw Dolibarr’s public header ONCE
    include_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
    htmlPrintOnlineHeader($mysoc, $langs, 1, getDolGlobalString('PROJECT_PUBLIC_INTERFACE_TOPIC'), 'PROJECT_IMAGE_PUBLIC_NEWLEAD');

    // Your branding overrides (logo + hide "powered by")
    $myLogo = DOL_URL_ROOT.'/custom/mybrand/img/realcore.png';
    print '
    <style>
	/* ———  wrapper  ——— */
.contact-container{
    display:flex;
    justify-content:space-between;
    align-items:stretch;               /* NEW – make both columns full height */
    width:100%;
    margin:40px auto;
    background:#fff;
    border-radius:8px;
    overflow:hidden;
    box-shadow:0 4px 15px rgba(0,0,0,.1);
    max-width:1200px;                  /* NEW – keeps lines readable on very wide monitors */
}

/* ———  left column  ——— */
.contact-left{
    flex:1 1 50%;                      /* NEW – flex-basis 50 % so it can shrink on tablets  */
    background:url("/dolibarr/public/mybrand/img/backgroundform.jpg")
              center/cover no-repeat;
    color:#fff;
    padding:40px;
    font-size:14px;
    line-height:1.6;
    display:flex;
    flex-direction:column;
    justify-content:center;
    min-height:400px;                  /* edited – avoid 850 px tall banner on mobile */
}
.contact-left h3{margin-bottom:20px;font-size:20px;font-weight:600}
.contact-left p {margin:15px 0}

/* ———  right column  ——— */
.contact-right{
    flex:1 1 50%;                      /* NEW */
    padding:40px;
    background:#fff;
}
.contact-right h2{
    margin-bottom:20px;
    font-size:22px;
    font-weight:600;
    color:#333;
}
.contact-right input,
.contact-right textarea,
.contact-right select{
    width:100%;
    margin:10px 0;
    padding:12px;
    border:1px solid #ddd;
    border-radius:5px;
    font-size:14px;
}
.contact-right input[type=submit]{
    background:#28a745;
    color:#fff;
    border:none;
    cursor:pointer;
    font-weight:bold;
    transition:.3s;
}
.contact-right input[type=submit]:hover{
    background:#218838;
}

/* ———  form table tweaks ——— */
#tablesubscribe{width:100%;border-collapse:collapse;border:0}
#tablesubscribe td{padding:6px 0;border:0}

/* ———  ***  responsive breakpoints  *** ——— */
@media (max-width:992px){             /* typical “tablet” breakpoint */
    .contact-container{flex-direction:column}  /* stack cols */
    .contact-left,
    .contact-right{flex:1 1 100%}     /* full-width each */
    .contact-left{min-height:260px;padding:30px}
    .contact-right{padding:30px}
}

/* Small phones */
@media (max-width:575px){
    .contact-left{display:none}       /* hide hero image if you like – saves vertical space */
    .contact-right{padding:24px}
    .contact-right h2{font-size:20px}
}

@media (max-width:575px){
    #tablesubscribe tr{display:block;width:100%}
    #tablesubscribe td{display:block;width:100%}
}
    </style>
    <script>
      document.addEventListener("DOMContentLoaded", function(){
        document.querySelectorAll(".backgreypublicpayment img, #onlinepaymentlogo img").forEach(function(img){
          img.src = "'.addslashes($myLogo).'";
          img.srcset = "";
          img.alt = "Realcore Solutions";
          img.decoding = "async";
        });
      });
    </script>';

    print '<div class="divmainbodylarge">';
}

/**
 * Show footer for new member
 *
 * Note: also called by functions.lib:recordNotFound
 *
 * @return	void
 */
function llxFooterVierge()  // @phan-suppress-current-line PhanRedefineFunction
{
	print '</div>';

	printCommonFooter('public');

	print "</body>\n";
	print "</html>\n";
}



/*
 * Actions
 */

$parameters = array();
// Note that $action and $object may have been modified by some hooks
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action);
if ($reshook < 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

// Action called when page is submitted
if (empty($reshook) && $action == 'add') {	// Test on permission not required here. This is an anonymous public submission form. Check is done on the constant to enable feature + mitigation.
	$error = 0;
	$urlback = '';
	// --- Verify reCAPTCHA v2 (checkbox) ---------------------------------
$recaptcha_secret = getDolGlobalString('PROJECT_RECAPTCHA_SECRET');
$token = GETPOST('g-recaptcha-response', 'none'); // don't strip characters

if (empty($recaptcha_secret)) {
    $error++;
    $errmsg .= 'CAPTCHA configuration error: missing secret key.<br>';
} elseif (empty($token)) {
    $error++;
    $errmsg .= 'Please tick the "I\'m not a robot" checkbox.<br>';
} else {
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $post = http_build_query(array(
        'secret'   => $recaptcha_secret,
        'response' => $token,
        'remoteip' => getUserRemoteIP(),
    ));

    $ch = curl_init($verifyUrl);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $post,
    ));
    $resp = curl_exec($ch);
    $cerr = curl_error($ch);
    curl_close($ch);

    if ($resp === false) {
        $error++;
        $errmsg .= 'CAPTCHA verification error: '.dol_escape_htmltag($cerr).'<br>';
    } else {
        $json = json_decode($resp, true);
        if (empty($json['success'])) {
            // Optional: log $json['error-codes']
            $error++;
            $errmsg .= 'CAPTCHA failed, please try again.<br>';
        }
        // Optional hardening: check hostname returned by Google
        // if (!empty($json['hostname']) && $json['hostname'] !== $_SERVER['HTTP_HOST']) { ... }
    }
}
// --------------------------------------------------------------------

	$db->begin();

	if (!GETPOST('lastname', 'alpha')) {
		$error++;
		$errmsg .= $langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Lastname"))."<br>\n";
	}
	if (!GETPOST('firstname', 'alpha')) {
		$error++;
		$errmsg .= $langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Firstname"))."<br>\n";
	}
	if (!GETPOST('email', 'alpha')) {
		$error++;
		$errmsg .= $langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Email"))."<br>\n";
	}
	if (!GETPOST('description', 'alpha')) {
		$error++;
		$errmsg .= $langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Message"))."<br>\n";
	}
	if (GETPOST('email', 'alpha') && !isValidEmail(GETPOST('email', 'alpha'))) {
		$error++;
		$langs->load("errors");
		$errmsg .= $langs->trans("ErrorBadEMail", GETPOST('email', 'alpha'))."<br>\n";
	}
	// Set default opportunity status
	$defaultoppstatus = getDolGlobalInt('PROJECT_DEFAULT_OPPORTUNITY_STATUS_FOR_ONLINE_LEAD');
	if (empty($defaultoppstatus)) {
		$error++;
		$langs->load("errors");
		$errmsg .= $langs->trans("ErrorModuleSetupNotComplete", $langs->transnoentitiesnoconv("Project"))."<br>\n";
	}

	$visibility = getDolGlobalString('PROJET_VISIBILITY');

	$proj = new Project($db);
	$thirdparty = new Societe($db);

	if (!$error) {
		// Search thirdparty and set it if found to the new created project
		$result = $thirdparty->fetch(0, '', '', '', '', '', '', '', '', '', GETPOST('email', 'alpha'));
		if ($result > 0) {
			$proj->socid = $thirdparty->id;
		} else {
			// Create the prospect
			if (GETPOST('societe', 'alpha')) {
				$thirdparty->name =  GETPOST('societe', 'alpha');
				$thirdparty->name_alias = dolGetFirstLastname(GETPOST('firstname', 'alpha'), GETPOST('lastname', 'alpha'));
			} else {
				$thirdparty->name = dolGetFirstLastname(GETPOST('firstname', 'alpha'), GETPOST('lastname', 'alpha'));
			}
			$thirdparty->email = GETPOST('email', 'alpha');
			$thirdparty->address = GETPOST('address', 'alpha');
			$thirdparty->zip = GETPOST('zipcode', 'alpha') ?: GETPOST('zip', 'alpha');
			$thirdparty->town = GETPOST('town', 'alpha');
			$thirdparty->country_id = GETPOSTINT('country_id');
			$thirdparty->state_id = GETPOSTINT('state_id');
			$thirdparty->client = $thirdparty::PROSPECT;
			$thirdparty->code_client = 'auto';
			$thirdparty->code_fournisseur = 'auto';

			// Fill array 'array_options' with data from the form
			$extrafields->fetch_name_optionals_label($thirdparty->table_element);
			$ret = $extrafields->setOptionalsFromPost(null, $thirdparty, '', 1);
			if ($ret < 0) {
				$error++;
				$errmsg = ($extrafields->error ? $extrafields->error.'<br>' : '').implode('<br>', $extrafields->errors);
			}

			if (!$error) {
				$result = $thirdparty->create($user);
				if ($result <= 0) {
					$error++;
					$errmsg = ($thirdparty->error ? $thirdparty->error.'<br>' : '').implode('<br>', $thirdparty->errors);
				} else {
					$proj->socid = $thirdparty->id;
				}
			}
		}
	}

	// --- Create or fetch the contact person ---------------------------------
$contact = new Contact($db);

// link the contact to the third party we just found/created
$contact->socid       = (int) $proj->socid;

// form data
$contact->firstname   = GETPOST('firstname','alpha');
$contact->lastname    = GETPOST('lastname','alpha');
$contact->email       = GETPOST('email','alpha');
$contact->address     = GETPOST('address','alpha');

// handle both possible field names for zip (your form uses "zipcode")
$zip = GETPOST('zipcode','alpha');
if (!$zip) $zip = GETPOST('zip','alpha');
$contact->zip         = $zip;

$contact->town        = GETPOST('town','alpha');
$contact->country_id  = GETPOSTINT('country_id');
$contact->state_id    = GETPOSTINT('state_id');

// mark as active
$contact->status      = 1;

$contactid = 0;
if (!$error) {
    $res = $contact->create($user);
    if ($res > 0) {
        $contactid = $contact->id;
    } else {
        $error++;
        $errmsg .= ($contact->error ? $contact->error.'<br>' : '').implode('<br>', $contact->errors);
    }
}
// ------------------------------------------------------------------------

	if (!$error) {
		// Defined the ref into $defaultref
		$defaultref = '';
		$modele = getDolGlobalString('PROJECT_ADDON', 'mod_project_simple');

		// Search template files
		$file = '';
		$classname = '';
		$reldir = '';
		$filefound = 0;
		$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);
		foreach ($dirmodels as $reldir) {
			$file = dol_buildpath($reldir."core/modules/project/".$modele.'.php', 0);
			if (file_exists($file)) {
				$filefound = 1;
				$classname = $modele;
				break;
			}
		}

		if ($filefound && !empty($classname)) {
			$result = dol_include_once($reldir."core/modules/project/".$modele.'.php');
			if (class_exists($classname)) {
				$modProject = new $classname();
				'@phan-var-force ModeleNumRefProjects $modProject';

				$defaultref = $modProject->getNextValue($thirdparty, $object);
			}
		}

		if (is_numeric($defaultref) && $defaultref <= 0) {
			$defaultref = '';
		}

		if (empty($defaultref)) {
			$defaultref = 'PJ'.dol_print_date(dol_now(), 'dayrfc');
		}

		if ($visibility === "1") {
			$proj->public = 1;
		} elseif ($visibility === "0") {
			$proj->public = 0;
		} elseif (empty($visibility)) {
			$proj->public = 1;
		}

		$proj->ref         = $defaultref;
		$proj->statut      = $proj::STATUS_DRAFT;
		$proj->status      = $proj::STATUS_DRAFT;
		$proj->usage_opportunity = 1;
		$proj->title       = $langs->trans("LeadFromPublicForm");
		$proj->description = GETPOST("description", "alphanohtml");
		$proj->opp_status = $defaultoppstatus;
		$proj->fk_opp_status = $defaultoppstatus;

		$proj->ip = getUserRemoteIP();
		$nb_post_max = getDolGlobalInt("MAIN_SECURITY_MAX_POST_ON_PUBLIC_PAGES_BY_IP_ADDRESS", 200);
		$now = dol_now();
		$minmonthpost = dol_time_plus_duree($now, -1, "m");
		$nb_post_ip = 0;
		if ($nb_post_max > 0) {	// Calculate only if there is a limit to check
			$sql = "SELECT COUNT(rowid) as nb_projets";
			$sql .= " FROM ".MAIN_DB_PREFIX."projet";
			$sql .= " WHERE ip = '".$db->escape($proj->ip)."'";
			$sql .= " AND datec > '".$db->idate($minmonthpost)."'";
			$resql = $db->query($sql);
			if ($resql) {
				$num = $db->num_rows($resql);
				$i = 0;
				while ($i < $num) {
					$i++;
					$obj = $db->fetch_object($resql);
					$nb_post_ip = $obj->nb_projets;
				}
			}
		}

		// Fill array 'array_options' with data from the form
		$extrafields->fetch_name_optionals_label($proj->table_element);
		$ret = $extrafields->setOptionalsFromPost(null, $proj);
		if ($ret < 0) {
			$error++;
		}

		if ($nb_post_max > 0 && $nb_post_ip >= $nb_post_max) {
			$error++;
			$errmsg = $langs->trans("AlreadyTooMuchPostOnThisIPAdress");
			array_push($proj->errors, $langs->trans("AlreadyTooMuchPostOnThisIPAdress"));
		}
		// Create the project
		if (!$error) {
			$result = $proj->create($user);
			if ($result > 0) {
				require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
				$object = $proj;

				    // Link the contact to the project (as an external contact)
    if (!empty($contactid)) {
        // Pick a type code that exists for element = 'project' in c_type_contact
        // Common ones are: PROJECTLEADER, PROJECTCONTRIBUTOR
        $typecode = 'PROJECTCONTRIBUTOR';
        $reslink = $proj->add_contact($contactid, $typecode, 'external'); // 'external' => third-party contact
        if ($reslink < 0) {
            setEventMessages($proj->error, $proj->errors, 'warnings');
        }
    }

				if ($object->email) {
					$subject = '';
					$msg = '';

					// Send subscription email
					include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
					$formmail = new FormMail($db);
					// Set output language
					$outputlangs = new Translate('', $conf);
					$outputlangs->setDefaultLang(empty($object->thirdparty->default_lang) ? $mysoc->default_lang : $object->thirdparty->default_lang);
					// Load traductions files required by page
					$outputlangs->loadLangs(array("main", "members", "projects"));
					// Get email content from template
					$arraydefaultmessage = null;
					$labeltouse = getDolGlobalString('PROJECT_EMAIL_TEMPLATE_AUTOLEAD');

					if (!empty($labeltouse)) {
						$arraydefaultmessage = $formmail->getEMailTemplate($db, 'project', $user, $outputlangs, 0, 1, $labeltouse);
					}

					if (!empty($labeltouse) && is_object($arraydefaultmessage) && $arraydefaultmessage->id > 0) {
						$subject = $arraydefaultmessage->topic;
						$msg     = $arraydefaultmessage->content;
					}
					if (empty($labeltosue)) {
						$appli = $mysoc->name;

						$labeltouse = '['.$appli.'] '.$langs->trans("YourMessage");
						$msg = $langs->trans("YourMessageHasBeenReceived");
					}

					$substitutionarray = getCommonSubstitutionArray($outputlangs, 0, null, $object);
					complete_substitutions_array($substitutionarray, $outputlangs, $object);
					$subjecttosend = make_substitutions($subject, $substitutionarray, $outputlangs);
					$texttosend = make_substitutions($msg, $substitutionarray, $outputlangs);
					if ($subjecttosend && $texttosend) {
						$moreinheader = 'X-Dolibarr-Info: send_an_email by public/lead/new.php'."\r\n";

						$result = $object->sendEmail($texttosend, $subjecttosend, array(), array(), array(), "", "", 0, -1, '', $moreinheader);
					}
					/*if ($result < 0) {
						$error++;
						setEventMessages($object->error, $object->errors, 'errors');
					}*/
				}

				if (!empty($backtopage)) {
					$urlback = $backtopage;
				} elseif (getDolGlobalString('PROJECT_URL_REDIRECT_LEAD')) {
					$urlback = getDolGlobalString('PROJECT_URL_REDIRECT_LEAD');
					// TODO Make replacement of __AMOUNT__, etc...
				} else {
					$urlback = $_SERVER["PHP_SELF"]."?action=added&token=".newToken();
				}

				if (!empty($entity)) {
					$urlback .= '&entity='.$entity;
				}

				dol_syslog("project lead ".$proj->ref." has been created, we redirect to ".$urlback);
			} else {
				$error++;
				$errmsg .= $proj->error.'<br>'.implode('<br>', $proj->errors);
			}
		} else {
			setEventMessage($errmsg, 'errors');
		}
	}

	if (!$error) {
		$db->commit();

		header("Location: ".$urlback);
		exit;
	} else {
		$db->rollback();
	}
}



// Action called after a submitted was send and member created successfully
// backtopage parameter with an url was set on member submit page, we never go here because a redirect was done to this url.
if (empty($reshook) && $action == 'added') {	// Test on permission not required here
	llxHeaderVierge($langs->trans("NewLeadForm"));

	// Si on a pas ete redirige
	print '<br><br>';
	print '<div class="center">';
	print $langs->trans("NewLeadbyWeb");
	print '</div>';

	llxFooterVierge();
	exit;

}



/*
 * View
 */

$form = new Form($db);
$formcompany = new FormCompany($db);

$extrafields->fetch_name_optionals_label($object->table_element); // fetch optionals attributes and labels

llxHeaderVierge($langs->trans("NewContact"));

print '<style>
  .subscriptionformhelptext{ text-align:center!important; }

/* ------------------------------------------------------------------ */
/*    EXTRA MOBILE PATCH  •  drop this at the end of the big <style> */
/* ------------------------------------------------------------------ */
@media (max-width:575px){

  /* 1. Bring the hero back, on top */
  .contact-container       {flex-direction:column}
  .contact-left            {display:block; order:-1;  /* show first */
                             min-height:160px; padding:24px 0}

  /* 2. Make inputs truly fluid */
  .contact-right input,
  .contact-right select,
  .contact-right textarea  {min-width:0; width:100%; max-width:100%}

  /* kill the old fixed width helper class */
  .minwidth150             {min-width:0 !important; width:100%!important}

  /* 3. Force each row of the <table> to a single column */
  #tablesubscribe tr       {display:block; width:100%}
  #tablesubscribe td       {display:block; width:100%}

  /* tidy up paddings / font sizes */
  .contact-right           {padding:20px}
  .contact-right h2        {font-size:18px}
}

div.backgreypublicpayment {
    text-align: left;
	background-color: transparent;
    border-bottom: none;
}
</style>';

// print '<br>';
// path/URL to your logo (SVG/PNG). Put your file in htdocs/custom/... or any public URL.
$myLogo = DOL_URL_ROOT.'/public/mybrand/img/realcore.png';
print '
<style>
  /* Left-align the header logo row printed by htmlPrintOnlineHeader() */

    

  body.publicnewmemberform .backgreypublicpayment .center{
    text-align:left !important;
    justify-content:flex-start !important;
  }

  /* Swap the Dolibarr logo with your image */
  body.publicnewmemberform .backgreypublicpayment img{
    content: url("'.$myLogo.'");
    height:56px; 
    width:auto;
  }

  /* Hide "Powered by Dolibarr" in any theme */
  body.publicnewmemberform .onlinepoweredby,
  body.publicnewmemberform .poweredby,
  body.publicnewmemberform [class*="poweredby"],
  body.publicnewmemberform [id*="poweredby"]{
    display:none !important;
  }
</style>

<script>
  document.addEventListener("DOMContentLoaded", function(){
    // Swap the left (big) logo
    var logo = document.querySelector(".divmainbodylarge > .center:first-of-type img");
    if (logo){
      logo.src    = "'.$myLogo.'";
      logo.srcset = "";               // neutralize srcset if present
      logo.alt    = "My Company";
      logo.decoding = "async";
    }
    // Extra safety: remove any remaining "powered by" node if the selector above missed it
    document.querySelectorAll(".onlinepoweredby, .poweredby, [class*=poweredby], [id*=poweredby]").forEach(function(el){
      el.remove();
    });
  });
</script>';



print load_fiche_titre($langs->trans("NewContact"), '', '', 0, '', 'center');

print '<div class="center subscriptionformhelptext opacitymedium">';
if (getDolGlobalString('PROJECT_NEWFORM_TEXT')) {
	print $langs->trans(getDolGlobalString('PROJECT_NEWFORM_TEXT'))."<br>\n";
} else {
	print $langs->trans("FormForNewLeadDesc", getDolGlobalString("MAIN_INFO_SOCIETE_MAIL"))."<br>\n";
}   
print '</div>';

print '<div align="center">';
// print '<div id="divsubscribe">';
// 2-column layout wrapper
print '<div class="contact-container">';

// LEFT column (static contact info — edit as you like)
print '<div class="contact-left">';
// print '<h3>'.$langs->trans("ContactInformation").'</h3>';
// print '<p><strong>'.$langs->trans("Address").':</strong><br>Mada Center 8th floor, 379 Hudson St,<br>New York, NY 10018 US</p>';
// print '<p><strong>'.$langs->trans("LetsTalk").':</strong><br>+1 800 1236879</p>';
// print '<p><strong>'.$langs->trans("GeneralSupport").':</strong><br>contact@example.com</p>';
print '</div>';

// RIGHT column (put your help text + form here)
print '<div class="contact-right" id="divsubscribe">';




dol_htmloutput_errors($errmsg);

// Print form
print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST" name="newlead">'."\n";
print '<input type="hidden" name="token" value="'.newToken().'" / >';
print '<input type="hidden" name="entity" value="'.$entity.'" />';
print '<input type="hidden" name="action" value="add" />';

print '<br>';

// print '<br><span class="opacitymedium">'.$langs->trans("FieldsWithAreMandatory", '*').'</span><br>';
//print $langs->trans("FieldsWithIsForPublic",'**').'<br>';

print dol_get_fiche_head();

print '<script type="text/javascript">
jQuery(document).ready(function () {
    jQuery(document).ready(function () {
        jQuery("#selectcountry_id").change(function() {
           document.newlead.action.value="create";
           document.newlead.submit();
        });
    });
});
</script>';


print '<table class="border" summary="form to subscribe" id="tablesubscribe">'."\n";
// Firstname
print '<tr><td>'.$langs->trans("Firstname").' <span class="star">*</span></td><td><input type="text" name="firstname" class="minwidth150" value="'.dol_escape_htmltag(GETPOST('firstname')).'" required></td></tr>'."\n";
// Lastname
print '<tr><td>'.$langs->trans("Lastname").' <span class="star">*</span></td><td><input type="text" name="lastname" class="minwidth150" value="'.dol_escape_htmltag(GETPOST('lastname')).'" required></td></tr>'."\n";
// EMail
print '<tr><td>'.$langs->trans("Email").' <span class="star">*</span></td><td><input type="text" name="email" maxlength="255" class="minwidth150" value="'.dol_escape_htmltag(GETPOST('email')).'" required></td></tr>'."\n";
// Company
print '<tr id="trcompany" class="trcompany"><td>'.$langs->trans("Company").'</td><td><input type="text" name="societe" class="minwidth150" value="'.dol_escape_htmltag(GETPOST('societe')).'"></td></tr>'."\n";
// Address
print '<tr><td>'.$langs->trans("Address").'</td><td>'."\n";
print '<textarea name="address" id="address" wrap="soft" class="quatrevingtpercent" rows="'.ROWS_2.'">'.dol_escape_htmltag(GETPOST('address', 'restricthtml'), 0, 1).'</textarea></td></tr>'."\n";
// // Zip / Town
// print '<tr><td>'.$langs->trans('Zip').' / '.$langs->trans('Town').'</td><td>';
// print $formcompany->select_ziptown(GETPOST('zipcode'), 'zipcode', array('town', 'selectcountry_id', 'state_id'), 6, 1);
// print ' / ';
// print $formcompany->select_ziptown(GETPOST('town'), 'town', array('zipcode', 'selectcountry_id', 'state_id'), 0, 1);
// print '</td></tr>';
// Zip / Town - Table layout for better control
print '<tr><td>'.$langs->trans('Zip').' / '.$langs->trans('Town').'</td><td>';
print '<table style="width: 100%; border: none; margin: 0; padding: 0;"><tr>';
print '<td style="width: 45%; padding: 0; border: none; vertical-align: middle;">';
print $formcompany->select_ziptown(GETPOST('zipcode'), 'zipcode', array('town', 'selectcountry_id', 'state_id'), 6, 1);
print '</td>';
print '<td style="width: 10%; text-align: center; padding: 0 2px; border: none; vertical-align: middle; color: #666;">/</td>';
print '<td style="width: 45%; padding: 0; border: none; vertical-align: middle;">';
print $formcompany->select_ziptown(GETPOST('town'), 'town', array('zipcode', 'selectcountry_id', 'state_id'), 0, 1);
print '</td>';
print '</tr></table>';
print '</td></tr>';
// Country
print '<tr><td>'.$langs->trans('Country').'</td><td>';
$country_id = GETPOST('country_id');
if (!$country_id && getDolGlobalString('PROJECT_NEWFORM_FORCECOUNTRYCODE')) {
	$country_id = getCountry($conf->global->PROJECT_NEWFORM_FORCECOUNTRYCODE, '2', $db, $langs);
}
if (!$country_id && !empty($conf->geoipmaxmind->enabled)) {
	$country_code = dol_user_country();
	//print $country_code;
	if ($country_code) {
		$new_country_id = getCountry($country_code, '3', $db, $langs);
		//print 'xxx'.$country_code.' - '.$new_country_id;
		if ($new_country_id) {
			$country_id = $new_country_id;
		}
	}
}
$country_code = getCountry($country_id, '2', $db, $langs);
print $form->select_country($country_id, 'country_id');
print '</td></tr>';
// State
if (!getDolGlobalString('SOCIETE_DISABLE_STATE')) {
	print '<tr><td>'.$langs->trans('State').'</td><td>';
	if ($country_code) {
		print $formcompany->select_state(GETPOSTINT("state_id"), $country_code);
	} else {
		print '';
	}
	print '</td></tr>';
}

// Other attributes
$parameters['tpl_context'] = 'public';	// define template context to public
include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_add.tpl.php';
// Comments
print '<tr>';
print '<td class="tdtop">'.$langs->trans("Message").' <span class="star">*</span></td>';
print '<td class="tdtop"><textarea name="description" id="description" wrap="soft" class="quatrevingtpercent" rows="'.ROWS_5.'" required>'.dol_escape_htmltag(GETPOST('description', 'restricthtml'), 0, 1).'</textarea></td>';
print '</tr>'."\n";

print "</table>\n";

print dol_get_fiche_end();

// reCAPTCHA v2 (checkbox)
$sitekey = dol_escape_htmltag(getDolGlobalString('PROJECT_RECAPTCHA_SITEKEY'));
print '<div style="margin:14px 0">';
print '<div class="g-recaptcha" data-sitekey="'.$sitekey.'"></div>';
print '</div>';
print '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';


// Save
print '<div class="center">';
print '<input type="submit" value="'.$langs->trans("Submit").'" id="submitsave" class="button">';
if (!empty($backtopage)) {
	print ' &nbsp; &nbsp; <input type="submit" value="'.$langs->trans("Cancel").'" id="submitcancel" class="button button-cancel">';
}
print '</div>';


// print "</form>\n";
// print "<br>";
// print '</div></div>';
print "</form>\n";
print "<br>";
print '</div>';            // close .contact-right
print '</div>'; 
print '</div>';

llxFooterVierge();

$db->close();