Documentation for new.php - Public Lead Registration Form
Context & Purpose
This file (new.php) is a custom public self-registration form for Dolibarr ERP. It allows external visitors to submit lead information which gets converted into a Project (opportunity) and associated Thirdparty (company) and Contact within Dolibarr. Key customizations include a completely redesigned responsive form layout and the integration of Google reCAPTCHA v2 to prevent spam submissions.

Location: htdocs/custom/public/project/new.php

Key Customizations & Features
Public Form Security: Designed to be accessed without login (NOLOGIN defined), with CSRF and IP checks disabled for public access.

Branding Overhaul:
Custom header with company contact information and links.
Complete restyling using a modern, two-column layout with a background image.
Custom logo integration, replacing the default Dolibarr logo.
Responsive design that adapts to mobile devices (hiding the image column on very small screens).
Google reCAPTCHA v2: Added server-side validation to verify the user is not a robot before processing the form submission.
Enhanced Data Capture: The form captures lead data and creates three linked entities in Dolibarr:
A Thirdparty (Company/Prospect).
A Contact for the individual, linked to the Thirdparty.
A Project (of type Opportunity), linked to both the Thirdparty and the Contact.
Anti-Spam Measure: Implements a check to limit the number of submissions from a single IP address within a month.

Code Breakdown
1. Initialization & Configuration
•	Access Constants: NOLOGIN, NOCSRFCHECK, and NOIPCHECK are set to allow public access.
•	Multi-Company Support: Handles the entity parameter for Dolibarr instances managing multiple companies.
•	Dolibarr Environment: Standard includes for core libraries and classes (project.class.php, extrafields.class.php, etc.) are loaded.

2. HTML Rendering Functions: llxHeaderVierge() & llxFooterVierge()
These functions override the standard Dolibarr headers and footers to provide a custom public-facing layout.

llxHeaderVierge():

Prints a top bar with company email, phone numbers, and links (Support, Login).

Incorporates Dolibarr's standard public header but heavily styles it.

Injects extensive custom CSS for the two-column form layout (contact-container, contact-left, contact-right) and responsive behavior.

Includes JavaScript to replace the default Dolibarr logo with a custom logo from /custom/mybrand/img/realcore.png.

llxFooterVierge(): Closes the HTML structure opened by the custom header.

3. Form Action Processing (if ($action == 'add'))
This block handles the form submission.
reCAPTCHA Validation: First, it validates the Google reCAPTCHA response using the secret key stored in the Dolibarr configuration (PROJECT_RECAPTCHA_SECRET). It sends a verification request to Google's API and fails if the check is unsuccessful.

Input Validation: Checks for required fields (Lastname, Firstname, Email, Message) and validates the email format.

Database Transaction: Starts a database transaction to ensure data consistency.

Thirdparty Handling: Searches for an existing company based on the provided email. If not found, it creates a new prospect (Societe).

Contact Creation: Creates a new Contact record and links it to the found/created Thirdparty.

Project Creation: Generates a reference for the new project and creates a new Project record of type "Opportunity". It populates it with the form data and links it to the Thirdparty.

Contact Linking: Links the newly created Contact to the Project with the role PROJECTCONTRIBUTOR.

IP Rate Limiting: Checks if the submitter's IP hasn't exceeded the maximum allowed submissions.

Email Notification: If configured and an email template is set, sends a confirmation email to the lead.

Commit/Redirect: On success, commits the transaction and redirects the user to a success page or a custom URL (PROJECT_URL_REDIRECT_LEAD). On error, rolls back the transaction and displays errors.

4. Form Display View
•	Custom Styling: Additional CSS rules are printed to fine-tune the layout, especially for mobile viewports.

•	Logo Replacement Script: Further ensures the custom logo is displayed correctly.

•	Form Structure: The form is built with a two-column HTML structure.

•	Left Column (contact-left): Displays a background image (loaded from /custom/mybrand/img/backgroundform.jpg).

•	Right Column (contact-right): Contains the actual form built with a Dolibarr table (<table id="tablesubscribe">).

•	Form Fields: Renders inputs for all necessary lead information: Firstname, Lastname, Email, Company, Address, Zip/Town, Country, State, custom extra fields, and a Message.

•	reCAPTCHA Widget: Includes the necessary <div> and <script> tag to load the Google reCAPTCHA v2 checkbox widget using the site key from PROJECT_RECAPTCHA_SITEKEY.

Configuration Requirements
For this form to function correctly, the following Dolibarr configuration constants must be set:

•	PROJECT_ENABLE_PUBLIC: Must be enabled to allow public access.

•	PROJECT_RECAPTCHA_SITEKEY: Your Google reCAPTCHA v2 Site Key.

•	PROJECT_RECAPTCHA_SECRET: Your Google reCAPTCHA v2 Secret Key.

•	PROJECT_DEFAULT_OPPORTUNITY_STATUS_FOR_ONLINE_LEAD: The status ID for newly created opportunities.

•	(Optional) PROJECT_EMAIL_TEMPLATE_AUTOLEAD: The email template label for the auto-response email.

•	(Optional) PROJECT_URL_REDIRECT_LEAD: A URL to redirect to after successful submission.
•	
Adding Google captche Keys

Other Setup->PROJECT_RECAPTCHA_SECRET->KEY
 Other Setup-> PROJECT_RECAPTCHA_SITEKEY ->KEY
	
Summary
This new.php file is a robust, secure, and aesthetically customized public lead capture form. It seamlessly integrates with Dolibarr's core objects (Project, Thirdparty, Contact), includes anti-spam measures (reCAPTCHA, IP limiting), and provides a modern, branded user experience that works across all device types.



Documentation for create_ticket.php - Public Ticket Creation Form
Context & Purpose
This file (create_ticket.php) is the public-facing form in Dolibarr ERP that allows customers and visitors to submit new support tickets without logging in. The page has been customized with a new header design and the integration of Google reCAPTCHA v2 to enhance security and prevent spam submissions.

Location: htdocs/public/ticket/create_ticket.php

Key Customizations & Features
Public Access & Security: Configured for public access (NOLOGIN, NOIPCHECK) while maintaining security through other means.

Branding Overhaul:

•	A custom top contact bar displaying the company email, phone numbers, and links (Support, Login) has been added above the main header.

•	Custom logo integration (realcore.png) replaces the default Dolibarr logo in the header.

•	Responsive CSS ensures the layout adapts to mobile devices.

•	Google reCAPTCHA v2 Integration: Added as the primary CAPTCHA method, superseding Dolibarr's native CAPTCHA on this page when configured. Uses the same keys as the project form (PROJECT_RECAPTCHA_SITEKEY and PROJECT_RECAPTCHA_SECRET).

•	Conditional CAPTCHA Logic: Intelligently switches between Google reCAPTCHA and Dolibarr's native CAPTCHA based on configuration, ensuring a fallback option exists.

Code Breakdown
1. Initialization & Configuration
Access Constants: Standard Dolibarr constants are set to allow public access (NOLOGIN, NOIPCHECK, NOREQUIREMENU, NOREQUIREHTML).

Multi-Company Support: Handles the entity parameter.

Dolibarr Environment: Loads necessary libraries and classes specific to ticket handling (actions_ticket.class.php, formticket.class.php, etc.).

2. reCAPTCHA Configuration
A dedicated section early in the code handles the CAPTCHA setup:

php
$RECAPTCHA_SITEKEY = getDolGlobalString('PROJECT_RECAPTCHA_SITEKEY');
$RECAPTCHA_SECRET  = getDolGlobalString('PROJECT_RECAPTCHA_SECRET');
$USE_RECAPTCHA     = (!empty($RECAPTCHA_SITEKEY) && !empty($RECAPTCHA_SECRET));
// Disable Dolibarr's native CAPTCHA if reCAPTCHA is active
if ($USE_RECAPTCHA) {
    $conf->global->MAIN_SECURITY_ENABLECAPTCHA_TICKET = 0;
}
This ensures only one CAPTCHA system is active, preventing user confusion.

3. Form Action Processing (if ($action == 'create_ticket' && GETPOST('save', 'alpha')))
This block processes the form submission. The key addition is the CAPTCHA validation logic.

Input Validation: Standard checks for required fields (Email, Lastname, Firstname, etc.).

CAPTCHA Verification:

Google reCAPTCHA Path: If $USE_RECAPTCHA is true, it validates the g-recaptcha-response token by sending a verification request to Google's API. Errors are added to the object if validation fails.

Fallback Path: If reCAPTCHA is not configured, it falls back to validating Dolibarr's native CAPTCHA code (MAIN_SECURITY_ENABLECAPTCHA_TICKET).

Ticket Creation: If all validations pass, proceeds to create the Ticket, associated Thirdparty, and Contact objects.

Email Notification: Sends confirmation emails to both the customer and the support team (configured via TICKET_NOTIFICATION_EMAIL_TO).

Success Handling: On successful creation, stores a success message and redirects to avoid duplicate submissions.

4. View & Form Rendering
The form display is managed by Dolibarr's FormTicket class ($formticket->showForm()). The customizations are injected via HTML <style> and <script> tags.

Custom Styling (<style> tag):

Styles for the custom header band (backgreypublicpayment).

Positions the custom logo absolutely on the left side of the header (#rc-logo-left).

Hides any "Powered by Dolibarr" elements.

Adds responsive rules for mobile devices.

JavaScript Injection (<script> tag):

Logo Replacement: Dynamically replaces the source of any existing header logo with the custom realcore.png image.

Inject Left Logo: Creates and injects a new <div id="rc-logo-left"> containing the logo into the header if it doesn't exist.

Top Contact Bar: Injects a new top bar (.rc-topbar) above the main header containing the company email, phone numbers, and links.

Cleanup: Removes any remaining "powered by" elements from the DOM.

reCAPTCHA Widget Injection: The PHP conditionally outputs JavaScript to inject the Google reCAPTCHA widget. A script finds the form and strategically places the CAPTCHA <div> in a centered container just above the submit button, ensuring good visual integration.

php
<?php if ($USE_RECAPTCHA): ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
// JavaScript to inject and center the reCAPTCHA widget
</script>
<?php endif; ?>
Configuration Requirements
For the customizations to function correctly, ensure these Dolibarr configuration constants are set:

General Ticket Module:

TICKET_ENABLE_PUBLIC_INTERFACE: Must be enabled.

TICKET_NOTIFICATION_EMAIL_FROM: Must be set for email notifications to work.

Google reCAPTCHA (Optional but recommended):

PROJECT_RECAPTCHA_SITEKEY: Your Google reCAPTCHA v2 Site Key.

PROJECT_RECAPTCHA_SECRET: Your Google reCAPTCHA v2 Secret Key.

Dolibarr Native CAPTCHA (Fallback):

MAIN_SECURITY_ENABLECAPTCHA_TICKET: Will be used automatically if reCAPTCHA keys are not set.

Summary
The create_ticket.php page has been enhanced to provide a more branded and secure public experience. The key improvements are:

A visually updated header with a custom logo and a convenient top contact bar.

The integration of Google reCAPTCHA v2 as the primary spam prevention measure, with a seamless fallback to the native system.

Maintains all original functionality of ticket creation, contact/company association, and email notifications.

The customizations are implemented non-destructively, relying on Dolibarr's core classes for form rendering and business logic, while using CSS and JavaScript to overlay the new visual design and security feature.


Documentation for login.tpl.php - Redesigned Login Page
Context & Purpose
This file (login.tpl.php) is the main Dolibarr ERP login page template. It has been completely redesigned to provide a modern, branded, and user-friendly authentication experience. The redesign features a two-column layout with a prominent brand presence, improved aesthetics, and responsive design for all devices.

Location: htdocs/core/tpl/login.tpl.php

Key Customizations & Features
Modern Two-Column Layout:
•	Left Panel: Dedicated brand showcase area with a large logo, application title, and social media links.
•	Right Panel: Clean login form card with intuitive input fields and styling.
•	A vertical divider separates the two columns on desktop views.

Complete Visual Overhaul:

•	Custom CSS variables for consistent theming (--brand1, --brand2, etc.).
•	Modern card-based design with shadows, rounded corners, and gradient buttons.
•	Custom background image applied to the entire login page.

Enhanced Branding:
•	Custom logo placement in the top-left corner.
•	Application title display (configurable via MAIN_APPLICATION_TITLE).
•	Social media icons (Instagram, LinkedIn, Facebook) in the left panel.
•	Support link and copyright information in the footer.

Responsive Design:

•	Fully responsive layout that adapts to mobile, tablet, and desktop screens.
•	The two-column layout collapses to a single column on mobile devices.
•	Adjustable font sizes, spacing, and element sizing for different screen widths.

Maintained Functionality:

•	Preserves all original Dolibarr login functionality, including:
•	CAPTCHA support
•	Password visibility toggle
•	"Remember me" option
•	Password reset link
•	Hidden fields for timezone, screen size, and UI preferences
•	Hook support for additional content

Code Breakdown
1. CSS Styling
•	The extensive <style> section contains all the customizations:
•	CSS Variables: Defines a color scheme and design tokens for consistent styling.
•	Background: Applies a full-page background image from custom/mybrand/img/background.jpg.
•	Layout: Uses CSS Grid for the two-column layout (.doli-login-shell).
•	Brand Header: Positions the logo absolutely in the top-left corner (.brand-header).
•	Left Panel: Styles the brand display area with centered content and social icons.
•	Right Panel: Styles the login form card with modern input fields and buttons.
•	Responsive Rules: Multiple media queries adjust the layout for tablets (max-width: 980px), phones (max-width: 640px), and very small screens (max-width: 360px).

2. HTML Structure
The template maintains the original PHP functionality while wrapping it in new HTML structure:

•	Brand Header: Fixed-position logo in the top-left corner.
•	Two-Column Container: .doli-login-shell containing both panels.
•	Left Brand Panel: Displays the application logo/title and social media links.
•	Right Form Panel: Contains the actual login form with all Dolibarr fields.
•	Footer Elements: Support link and copyright information.

3. PHP Integration
The template preserves all original Dolibarr PHP functionality:

•	Form Fields: All hidden fields for security, timezone detection, and UI preferences are maintained.
•	CAPTCHA Support: Full support for Dolibarr's CAPTCHA system.
•	Authentication Methods: Compatibility with various authentication methods including OpenID Connect.
•	Error Handling: Proper display of login error messages.
•	Hook Support: Integration points for additional content via hooks.
•	Multi-language Support: All text uses Dolibarr's translation system.

4. Responsive Behavior
The design responds to different screen sizes:

•	Desktop: Two-column layout with vertical divider
•	Tablet (< 980px): Single column layout, left panel becomes a header section
•	Mobile (< 640px): Compact form with adjusted spacing and sizing
•	Very Small (< 360px): Further size reductions for ultra-small screens

Configuration Requirements
For the customizations to work properly:

Image Files: The following images should be placed in the appropriate directories:

•	htdocs/custom/mybrand/img/background.jpg - Background image
•	htdocs/public/mybrand/img/realcore.png - Main logo
•	htdocs/public/mybrand/img/Realcore_solution_logo.svg - Brand logo for left panel
•	Social media icons in SVG format

Dolibarr Configuration:

•	MAIN_APPLICATION_TITLE - Sets the application name displayed in the left panel
•	MAIN_HELPCENTER_LINKTOUSE - Configures the support link URL

Browser Compatibility
The design uses modern CSS features including:

•	CSS Grid and Flexbox for layout
•	CSS Variables for theming
•	CSS Clamp for responsive typography
•	CSS Transitions for interactive elements

These features are supported in all modern browsers but may have limited support in older browsers like Internet Explorer.

Summary
The redesigned login.tpl.php provides a significantly improved user experience while maintaining full compatibility with Dolibarr's authentication system. The key improvements are:

•	Modern Aesthetics: Clean, professional design with custom branding
•	Improved Usability: Intuitive form layout with clear visual hierarchy
•	Responsive Design: Works seamlessly across all device types
•	Brand Consistency: Strong brand presence throughout the login experience
•	Maintained Compatibility: All original Dolibarr functionality preserved

Dolibarr ERP Web Portal Customization Documentation
Overview
The customized index.php file in dolibarr/public/webportal/ transforms the default Dolibarr ERP public login page into a modern, visually appealing, and responsive interface. The design mirrors the main login page, featuring a two-column layout (branding on the left, login form on the right) on larger screens and a single-column layout on smaller devices. The customization enhances user experience with a clean design, branded elements, and responsive behavior.
________________________________________
Key Features
1.	Modern Two-Column Layout: 

o	The page is divided into two sections: 
	Left Panel (Branding): Displays the company logo, brand mark, and social media links with a background image.
	Right Panel (Login Form): Contains a login form with fields for username, password, a "Remember Me" checkbox, and a password recovery link.
o	A vertical divider visually separates the two panels on larger screens.

2.	Responsive Design: 

o	The layout adapts to various screen sizes: 
	Large Screens (>980px): Two-column layout with a divider.
	Medium/Small Screens (≤980px): Single-column layout, stacking the branding and login form vertically.
	Extra Small Screens (≤640px, ≤360px): Further optimized padding, font sizes, and image scaling for mobile devices.

3.	Branding and Visual Design: 

o	Color Scheme: Uses CSS custom properties (e.g., --brand1: #0f8ea8, --brand2: #1178d1) for consistent branding.

o	Background Image: A fixed background image (/public/mybrand/img/background.jpg) enhances visual appeal.

o	Logo and Brandmark: Displays the company logo (realcore.png) in the top-left corner and a larger brand mark (Realcore_solution_logo.svg) in the left panel.

o	Social Media Links: Icons for Instagram, LinkedIn, and Facebook are positioned at the bottom of the left panel (or below the brand mark on mobile).
o	Typography: Uses system fonts (ui-sans-serif, system-ui, etc.) for cross-platform consistency.

4.	Login Form: 

o	Fields: Username and password inputs with placeholders, a "Remember Me" checkbox, and a "Forgot Password" link.
o	Security: Includes a form token ($context->getFormToken()) to prevent CSRF attacks.
o	Styling: Inputs have a modern look with rounded corners, focus states, and subtle animations (e.g., shadow and border color changes on focus).
o	Call-to-Action (CTA): A gradient-styled login button with hover and active states for interactivity.

5.	Footer Elements: 

o	Support Link: A "Contact Support" link (mailto:support@realcoresolutions.com) with an icon for user assistance.
o	Copyright Notice: Displays "© 2025 by Realcore Solutions. All Rights Reserved" with a link to the company website.
________________________________________
Code Structure
1. PHP Protection
•	The template includes a security check to prevent direct access: 
php
if (empty($context) || !is_object($context)) {
    print "Error, template page can't be called as URL";
    exit(1);
}
•	A PHAN annotation ensures type safety for the $context object.
2. CSS Styling
•	Custom Properties: Defines reusable variables for colors, shadows, and radius (e.g., --brand1, --shadow).
•	Global Styles: Sets html and body to full height, removes margins, and applies a background image (background.jpg).
•	Layout: 
o	Uses CSS Grid for the two-column layout (doli-login-shell).
o	A pseudo-element (::before) creates a vertical divider between panels on large screens.
•	Left Panel (.doli-left): 
o	Centered content with a logo, brand mark, and social icons.
o	Social icons are positioned absolutely at the bottom on large screens and stack normally on mobile.
•	Right Panel (.doli-right): 
o	Contains a card (.doli-card) with the login form, styled with padding, shadows, and rounded corners.
o	Form elements include labels, inputs, a checkbox, and a submit button with a gradient background.
•	Responsive Breakpoints: 
o	max-width: 980px: Switches to a single-column layout, hides the divider, and adjusts padding and image sizes.
o	max-width: 640px: Further reduces padding, font sizes, and image heights for smaller devices.
o	max-width: 360px: Optimizes for very small screens with smaller fonts and images.
3. HTML Structure
•	Body: Uses the bodylogin class to apply the background image.
•	Container (doli-login-shell): 
o	Header Logo: A fixed logo (realcore.png) in the top-left corner.
o	Left Panel: 
	Contains a hidden legacy logo (.doli-logo), a brand mark (.doli-brandmark), and social media links (.doli-social).
o	Right Panel: 
	A login form with a welcome message, subtitle, input fields, a checkbox, a forgot password link, a submit button, a secure connection message, a support link, and a copyright notice.
4. Form Functionality
•	The form submits via POST with a hidden action_login field set to login.
•	Input fields for username and password use Dolibarr’s translation system ($langs->trans) for internationalization.
•	A CSRF token ensures secure form submission.
________________________________________
Implementation Details
1.	File Path: 
o	Located at dolibarr/public/webportal/index.php.
o	References assets in /dolibarr/public/mybrand/img/ (e.g., realcore.png, background.jpg, social media icons).
2.	Dependencies: 
o	Relies on Dolibarr’s $context object for form token generation.
o	Uses $langs->trans for multilingual support.
o	Assumes assets (logos, background image, social icons) are available in the specified paths.
3.	Styling Approach: 
o	Uses modern CSS techniques (e.g., CSS Grid, custom properties, clamp() for responsive sizing).
o	Employs transitions for interactive elements (e.g., button hover effects, input focus states).
o	Ensures accessibility with aria-label for social links and sr-only for screen reader content.
4.	Responsive Behavior: 
o	Breakpoints at 980px, 640px, and 360px ensure compatibility across desktops, tablets, and phones.
o	Adjusts image sizes, padding, and font sizes to prevent overflow and maintain readability on small screens.
________________________________________
Potential Improvements
1.	Accessibility: 
o	Add aria-label to the login button for screen readers.
o	Ensure sufficient color contrast for text and links (e.g., --muted color may need adjustment).
o	Add keyboard navigation support for social links and form elements.
2.	Asset Optimization: 
o	Compress images (realcore.png, background.jpg) to reduce load times.
o	Consider lazy-loading the background image for better performance.
3.	Error Handling: 
o	Add validation feedback for form inputs (e.g., invalid username/password).
o	Display error messages from Dolibarr’s authentication process.
4.	Mobile Optimization: 
o	Test on very small screens (e.g., <320px) to ensure no content clipping.
o	Consider reducing the size of the brand mark image on mobile for faster loading.
5.	Security: 
o	Ensure all external links (mailto, social media, company website) use rel="noopener" to prevent window.opener vulnerabilities.
o	Validate the background image path to avoid broken assets if the file is missing.
________________________________________
Example Screenshots
(Note: Since I cannot generate or view images, describe how the page looks based on the code.)
•	Large Screens: A split-screen layout with a background image, a logo in the top-left, a centered brand mark with social icons on the left, and a login form card on the right with a gradient button.
•	Mobile Screens: A single-column layout with the logo at the top, followed by the brand mark, social icons, login form, support link, and copyright notice.
________________________________________
Usage Instructions
1.	File Placement: Ensure the file is placed in dolibarr/public/webportal/index.php.
2.	Asset Setup: Upload required images (realcore.png, Realcore_solution_logo.svg, background.jpg, social icons) to /dolibarr/public/mybrand/img/.
3.	Testing: 
o	Verify the form submits correctly and integrates with Dolibarr’s authentication.
o	Test responsiveness across devices (desktop, tablet, mobile).
o	Check translation strings ($langs->trans) for all supported languages.
4.	Maintenance: 
o	Update the copyright year in .doli-copy as needed.
o	Monitor external links (social media, support) for accuracy.

Dolibarr ERP Recruitment Module Customization Documentation
Overview
The view.php file, located at dolibarr/public/recruitment/index.php, is a public-facing page in the Dolibarr ERP system that displays details of a specific job opening and allows candidates to submit applications. The customization enhances the default Dolibarr job opening page with a modern card-based design, consistent with the branding and styling of the web portal login page (dolibarr/public/webportal/index.php). The updated design improves user experience with a clean layout, branded elements, and a responsive form for job applications.
________________________________________
Key Features
1.	Card-Based Job Display: 
o	The job opening details (label, expected start date, remuneration, contact information, and description) are presented in a clean, card-like layout.
o	The design aligns with the branding used in the web portal login page, using similar colors, typography, and visual elements.
2.	Application Form: 
o	Candidates can submit applications with fields for: 
	First Name
	Last Name (required)
	Email (required)
	Phone
	Date of Birth
	Requested Remuneration
	Message
	Extra fields (if defined in Dolibarr’s extrafields configuration)

o	The form includes validation to ensure required fields (Last Name, Email) are filled and the email is valid.

o	A CSRF token (newToken()) ensures secure form submission.
3.	Branding and Visual Design: 

o	Logo: Displays the company logo (or a recruitment-specific logo if configured via ONLINE_RECRUITMENT_LOGO_<suffix> or ONLINE_RECRUITMENT_LOGO).

o	Background: Optionally includes a background image if RECRUITMENT_IMAGE_PUBLIC_INTERFACE is set in the configuration.

o	Styling: Uses CSS to override default Dolibarr styles, making the page visually consistent with the web portal login page.

4.	Responsive Design: 
o	The layout is centered and adapts to various screen sizes, ensuring usability on desktops, tablets, and mobile devices.
o	Input fields and buttons are styled for clarity and accessibility on smaller screens.

5.	Security Features: 
o	Prevents direct access with NOLOGIN, NOCSRFCHECK, and NOIPCHECK definitions.
o	Limits submissions per IP address (MAIN_SECURITY_MAX_POST_ON_PUBLIC_PAGES_BY_IP_ADDRESS) to prevent spam.
o	Validates candidature to prevent duplicate submissions based on email.
6.	Status Indicators: 
o	Displays warnings if the job is closed (STATUS_RECRUITED) or canceled (STATUS_CANCELED).
________________________________________
Code Structure
1. PHP Logic
•	Environment Setup: 
o	Defines constants (NOLOGIN, NOCSRFCHECK, NOIPCHECK, NOBROWSERNOTIF) to allow public access without login, bypass CSRF checks for this page, and disable IP restrictions.
o	Loads Dolibarr core files and recruitment-specific classes (RecruitmentJobPosition, RecruitmentCandidature, etc.).

o	Loads translation files for multilingual support (companies, other, recruitment).

•	Parameters:
 
o	Retrieves form inputs (ref, email, firstname, lastname, birthday, phone, message, requestedremuneration).

o	Uses GETPOST to safely handle user input.

•	Security Checks: 

o	Ensures the recruitment module is enabled.
o	Validates the presence of a job reference (ref) and fetches the job details using RecruitmentJobPosition.

•	Form Submission (action == "dosubmit"): 

o	Validates required fields (Last Name, Email) and email format.
o	Checks for duplicate candidatures based on email.
o	Limits submissions per IP address to prevent abuse.
o	Creates and validates a RecruitmentCandidature object with user inputs and extrafields.
o	Commits the transaction on success or rolls back on error, redirecting to the job listings page (index.php) on success.

•	Email Notifications: 

o	Includes actions_sendmails.inc.php to handle email notifications with the trigger CANDIDATURE_SENTBYMAIL.
2. CSS Styling
•	Custom Styles: 
css
div.backgreypublicpayment {
    background-color: transparent;
    border-bottom: none;
    text-align: left;
}
.poweredbypublicpayment {
    display: none;
}
o	Overrides the default Dolibarr background to be transparent and removes the border.
o	Hides the "Powered by Dolibarr" footer to align with the custom branding.


•	Input Styling: 

o	Applies a --success class to input fields, likely for visual feedback (e.g., green border or checkmark on valid input, though the exact style is not defined in the provided code).
o	Uses minwidth400 and minwidth100 classes for responsive input sizing.

•	Layout: 

o	Centers the content with <div class="center"> and uses a table (#dolpaymenttable) for structuring job details and the form.
o	Applies opacitymedium to the introductory text for a subtle effect.
3. HTML Structure
•	Header: 
o	Outputs a company logo (or recruitment-specific logo) if available, linked to the company website if configured.
o	Optionally displays a background image (RECRUITMENT_IMAGE_PUBLIC_INTERFACE).

•	Main Content: 
o	Displays job details in a table: 
	Label: The job title ($object->label).
	Date Expected: Planned start date or "ASAP" if overdue.
	Remuneration: Suggested remuneration ($object->remuneration_suggested).
	Contact: Recruiter’s name and email (falls back to company email if not set).
	Description: Full job description ($object->description).
o	Shows status warnings (recruited or canceled) if applicable.

•	Form: 
o	A centered form (#dolpaymentform) with hidden fields for token, action, tag, suffix, securekey, entity, and ref.
o	Input fields for candidate details, styled with Dolibarr’s flat class and custom --success class.
o	A date picker for birth date using $form->selectDate.
o	A textarea for the candidate’s message (5 rows, 80% width).
o	Save and Cancel buttons generated by $form->buttonsSaveCancel.

•	Footer: 

o	Calls htmlPrintOnlineFooter to render a standard Dolibarr footer (excluding the hidden "Powered by" section).
________________________________________
Customization Details
1.	Alignment with Web Portal Login Page: 

o	The styling is designed to match the web portal login page (dolibarr/public/webportal/index.php): 
	Transparent background (backgreypublicpayment) aligns with the login page’s background image approach.
	Centered, card-like layout for the form mirrors the .doli-card style.
	Consistent typography and input field styling (e.g., rounded corners, responsive sizing).
o	The --success class suggests a similar visual feedback mechanism (e.g., green borders or icons) as used in the login page’s .doli-input.
2.	Card-Based Design: 

o	The job details and form are presented in a clean, card-like container (#tablepublicpayment), visually distinct from Dolibarr’s default table-based layout.
o	The removal of the poweredbypublicpayment section ensures a branded, distraction-free experience.
3.	Responsive Design: 

o	The form and job details are centered and use responsive classes (minwidth400, minwidth100) to adapt to different screen sizes.
o	The table-based layout (#dolpaymenttable) ensures alignment, with CSS tweaks to remove default borders and backgrounds.
4.	Branding: 

o	Supports a custom logo via ONLINE_RECRUITMENT_LOGO_<suffix> or ONLINE_RECRUITMENT_LOGO.
o	Optionally includes a background image (RECRUITMENT_IMAGE_PUBLIC_INTERFACE) to match the login page’s aesthetic.
________________________________________
Implementation Details
1.	File Path: 
o	Located at dolibarr/public/recruitment/view.php.
o	References assets in conf->mycompany->dir_output/logos/ or conf->mycompany->dir_output/logos/thumbs/ for logos.
2.	Dependencies: 
o	Relies on Dolibarr’s recruitment module (recruitment).
o	Uses core classes: RecruitmentJobPosition, RecruitmentCandidature, Form, ExtraFields.
o	Requires translation strings (companies, other, recruitment).
o	Optionally uses a background image or logo defined in Dolibarr’s configuration.

3.	Styling Approach: 
o	Overrides default Dolibarr styles to create a modern, card-based look.
o	Uses inline CSS for simplicity, though external stylesheets could be added via MAIN_RECRUITMENT_CSS_URL.
o	Leverages Dolibarr’s flat, minwidthXXX, and custom --success classes for form inputs.
4.	Security: 

o	Validates form inputs and checks for duplicate candidatures.
o	Limits submissions per IP address to prevent spam.
o	Uses CSRF tokens for secure form submission.
________________________________________
Potential Improvements
1.	Styling Enhancements: 

o	Define the --success class explicitly in the CSS to match the login page’s input styles (e.g., green border on valid input).
o	Add hover and focus states for inputs and buttons, similar to the login page’s .doli-input and .doli-btn.
o	Use CSS custom properties (e.g., --brand1, --brand2) from the login page for consistent colors.

2.	Accessibility: 

o	Add aria-label or aria-describedby to form fields for screen reader support.
o	Ensure sufficient color contrast for text and icons (e.g., opacitymedium text).
o	Make the date picker keyboard-accessible.
3.	Form Validation: 

o	Add client-side validation (JavaScript) to provide immediate feedback on invalid inputs (e.g., email format).
o	Display error messages next to fields instead of at the top.
4.	Responsive Design: 

o	Replace the table-based layout (#dolpaymenttable) with CSS Grid or Flexbox to match the login page’s modern layout.
o	Test on very small screens (<360px) to ensure no content overflow.
5.	Asset Optimization: 

o	Compress logo and background images to reduce load times.
o	Consider lazy-loading the background image (RECRUITMENT_IMAGE_PUBLIC_INTERFACE).
6.	Error Handling: 

o	Improve error messaging for failed submissions (e.g., duplicate candidature) with user-friendly text.
o	Log errors for debugging purposes.
________________________________________
Example Visual Description
•	Desktop View: A centered card with the company logo at the top, followed by job details (title, date, remuneration, contact, description) in a clean layout. Below, a form with labeled input fields, a date picker, a textarea, and Save/Cancel buttons. A background image (if set) enhances the aesthetic.

•	Mobile View: The same content stacks vertically, with input fields resizing to fit smaller screens. The logo and form remain centered, with no borders or background distractions.
________________________________________
Usage Instructions
1.	File Placement: Ensure the file is placed at dolibarr/public/recruitment/view.php.

2.	Asset Setup: 
o	Upload logo images to conf->mycompany->dir_output/logos/ or logos/thumbs/.
o	Set RECRUITMENT_IMAGE_PUBLIC_INTERFACE in Dolibarr’s configuration for a background image.

3.	Configuration: 
o	Enable the recruitment module (recruitment) in Dolibarr.
o	Set ONLINE_RECRUITMENT_LOGO_<suffix> or ONLINE_RECRUITMENT_LOGO for a custom logo.
o	Configure RECRUITMENT_ENABLE_PUBLIC_INTERFACE to enable the public interface.

4.	Testing:
 
o	Verify job details display correctly for different statuses (open, recruited, canceled).
o	Test form submission with valid and invalid inputs.
o	Check responsiveness across devices (desktop, tablet, mobile).
o	Ensure translations ($langs->trans) work for all supported languages.

5.	Maintenance: 
o	Update logo and background image paths if assets change.
o	Monitor email notifications (CANDIDATURE_SENTBYMAIL) for proper delivery.
________________________________________
Notes
•	The file references dolibarr/public/recruitment/index.php as the backtopage redirect target after successful form submission.
•	The --success class is not defined in the provided CSS, suggesting it may be inherited from a global stylesheet or the login page’s styles. Consider adding it to ensure consistency.

Dolibarr ERP Membership Module Customization Documentation
Overview
The new.php file, located at dolibarr/public/members/new.php, is a public-facing page in the Dolibarr ERP system that allows visitors to register as members. The customization enhances the default Dolibarr membership form by:
1.	Adding a CAPTCHA to prevent automated submissions and improve security.
2.	Redesigning the header to align with the branding and styling of other customized pages, such as the web portal login page. The page supports both individual and organizational memberships, with configurable fields, payment options, and multilingual support.
________________________________________
Key Features
1.	CAPTCHA Integration: 

o	A CAPTCHA field is added when MAIN_SECURITY_ENABLECAPTCHA_MEMBER is enabled in Dolibarr’s configuration.
o	Displays a security code input and an image generated by core/antispamimage.php, with a refresh button to regenerate the code.
o	Validates the user-entered code against the session-stored value (dol_antispam_value).

2.	Redesigned Header: 

o	The header is customized to match the aesthetic of the web portal login page (dolibarr/public/webportal/index.php).
o	Removes the default gray background and border (backgreypublicpayment) for a cleaner, transparent look.
o	Repositions the company logo to the top-left corner with padding.
o	Hides the "Powered by Dolibarr" link to maintain a branded experience.

3.	Membership Form: 

o	Allows users to register as members with fields for: 
	Member Type (required, unless forced via MEMBER_NEWFORM_FORCETYPE)
	Nature (Physical or Moral, unless forced via MEMBER_NEWFORM_FORCEMORPHY)
	Company Name (for Moral entities)
	Title (optional, based on MEMBER_NEWFORM_ASK_TITLE)
	First Name (required)
	Last Name (required)
	Email (required if ADHERENT_MAIL_REQUIRED is set)
	Login and Password (required unless ADHERENT_LOGIN_NOT_REQUIRED)
	Gender
	Address
	Zip/Town
	Country (can be forced via MEMBER_NEWFORM_FORCECOUNTRYCODE)
	State
	Date of Birth
	Photo URL
	Public Membership Checkbox
	Comments
	Turnover/Budget (if MEMBER_NEWFORM_DOLIBARRTURNOVER is enabled)
	Subscription Amount (if MEMBER_NEWFORM_PAYONLINE is enabled)
o	Supports extrafields for additional customization.

4.	Responsive Design:
 
o	The form is centered and uses responsive classes (minwidth150, minwidth200, quatrevingtpercent) for input fields and textareas.
o	The table-based layout (#tablesubscribe) adapts to different screen sizes.

5.	Security Features:
 
o	CAPTCHA validation to prevent spam.
o	Limits submissions per IP address (MAIN_SECURITY_MAX_POST_ON_PUBLIC_PAGES_BY_IP_ADDRESS).
o	Validates login uniqueness and email format.
o	Uses CSRF tokens (newToken()) for secure form submission.

6.	Payment Integration: 
o	Supports online payment (PayPal, Paybox, or Stripe) if MEMBER_NEWFORM_PAYONLINE is enabled.
o	Redirects to a payment URL after successful submission if configured.
7.	Email Notifications: 

o	Sends a confirmation email to the member using the ADHERENT_EMAIL_TEMPLATE_AUTOREGISTER template.
o	Notifies the organization (MAIN_INFO_SOCIETE_MAIL) of new member registrations if configured.
________________________________________
Code Structure
1. PHP Logic
•	Environment Setup: 

o	Defines constants (NOLOGIN, NOCSRFCHECK, NOBROWSERNOTIF) to allow public access, bypass CSRF checks for this page, and disable browser notifications.
o	Loads Dolibarr core files and membership-related classes (Adherent, AdherentType, FormCompany, etc.).
o	Supports multi-entity setups with DOLENTITY based on the entity parameter.

•	Parameters:
 
o	Retrieves form inputs (action, backtopage, firstname, lastname, email, etc.) using GETPOST.

•	Security Checks: 

o	Ensures the membership module is enabled (member).
o	Verifies MEMBER_ENABLE_PUBLIC is set to allow public access.

•	Form Submission (action == "add"): 

o	Validates required fields (Type, Nature, First Name, Last Name, Email if required, Login/Password if required).
o	Checks for duplicate login and valid email format.
o	Validates CAPTCHA if enabled.
o	Limits submissions per IP address to prevent abuse.
o	Creates an Adherent object, sets its properties, and saves it to the database.
o	Optionally creates a linked third-party (Societe) if ADHERENT_DEFAULT_CREATE_THIRDPARTY is enabled.
o	Sends email notifications to the member and organization.
o	Redirects to a payment URL, a custom URL (MEMBER_URL_REDIRECT_SUBSCRIPTION), or the same page with action=added.

•	Success Page (action == "added"): 
o	Displays a confirmation message if no redirect is configured.
2. CSS Styling
•	Header Redesign: 
css
div.backgreypublicpayment {
    background-color: transparent;
    border-bottom: none;
}
.backgreypublicpayment a {
    display: none;
}
.logopublicpayment #dolpaymentlogo {
    text-align: left !important;
    float: left;
    padding: 10px;
}
o	Sets a transparent background for the header (backgreypublicpayment) to align with the web portal login page’s clean aesthetic.
o	Hides links in the header (e.g., "Powered by Dolibarr") for a branded look.
o	Positions the logo (#dolpaymentlogo) in the top-left corner with 10px padding.

•	Form Styling: 

o	Uses Dolibarr’s default classes (minwidth150, minwidth200, quatrevingtpercent) for responsive input sizing.
o	Applies classfortooltip for required fields with tooltips.
o	Styles the CAPTCHA input and image with input-icon-security, width150, and inline-block for alignment.
3. HTML Structure
•	Header: 

o	Calls llxHeaderVierge to render a custom header with the company logo in the top-left corner.
o	Includes a transparent background and hidden "Powered by Dolibarr" link.

•	Main Content: 
o	Displays a title (NewSubscription) with a member icon.
o	Shows a help text (MEMBER_NEWFORM_TEXT or a default description).
o	Renders errors and events using dol_htmloutput_errors and dol_htmloutput_events.
•	Form: 
o	A form (name="newmember") with hidden fields for token, entity, action, and page_y.
o	Displays a table (#tablesubscribe) with fields for member type, nature, company, title, first name, last name, email, login/password, gender, address, zip/town, country, state, birth date, photo URL, public membership, comments, turnover (if enabled), and subscription amount (if enabled).
o	Includes a CAPTCHA field (input and image) if MAIN_SECURITY_ENABLECAPTCHA_MEMBER is enabled.
o	Save and Cancel buttons are centered at the bottom.

•	Membership Types Table (if MEMBER_SKIP_TABLE is not set and no type is forced): 
o	Lists available member types with columns for Label, Duration, Amount, Nature, Vote Allowed (optional), Members (optional), and a Subscribe button.
o	Clicking a Subscribe button submits the form with the selected typeid.

•	Footer:
 
o	Calls llxFooterVierge to render a standard footer without the "Powered by Dolibarr" section.
4. JavaScript:
•	Handles dynamic form behavior: 
o	Toggles visibility of company and turnover fields based on morphy (Physical hides them, Moral shows them).
o	Updates the subscription amount based on the turnover selection (budget).
o	Submits the form on changes to typeid or country_id to refresh dependent fields (e.g., state dropdown).
________________________________________
Customization Details
1.	CAPTCHA Addition:
 
o	Enabled via MAIN_SECURITY_ENABLECAPTCHA_MEMBER.
o	Adds a security code input (name="code") and an image generated by core/antispamimage.php.
o	Includes a refresh button (img_picto('refresh')) to regenerate the CAPTCHA.
o	Validates the code server-side by comparing it to $_SESSION['dol_antispam_value'].

2.	Header Redesign: 

o	Aligns with the web portal login page (dolibarr/public/webportal/index.php): 
	Transparent background (backgreypublicpayment) matches the login page’s clean aesthetic.
	Logo positioning (float: left; padding: 10px) mirrors the .brand-header style.
	Hides default links to maintain a branded, distraction-free header.
o	Uses llxHeaderVierge and htmlPrintOnlineHeader to render the customized header.

3.	Alignment with Other Pages: 

o	The header styling is consistent with the web portal login page and recruitment page (dolibarr/public/recruitment/view.php).
o	The form’s table-based layout could be further modernized to use CSS Grid or Flexbox, as seen in the login page’s .doli-login-shell.
________________________________________
Implementation Details
1.	File Path: 

o	Located at dolibarr/public/members/new.php.
o	References assets in core/antispamimage.php for CAPTCHA and company logo paths for the header.
2.	Dependencies:
 
o	Relies on the membership module (member).
o	Uses core classes: Adherent, AdherentType, FormCompany, CUnits, FormMail.
o	Requires translation strings (main, members, companies, install, other, errors).
o	Uses jQuery for dynamic form behavior (lib_foot.js.php).
3.	Styling Approach: 

o	Inline CSS for header redesign ensures minimal changes to Dolibarr’s default styles.
o	Leverages existing Dolibarr classes (minwidth150, quatrevingtpercent) for form inputs.
o	CAPTCHA styling uses Dolibarr’s input-icon-security and inline-block for alignment.
4.	Security:
 
o	CAPTCHA prevents automated submissions.
o	Limits submissions per IP address (MAIN_SECURITY_MAX_POST_ON_PUBLIC_PAGES_BY_IP_ADDRESS).
o	Validates login uniqueness, email format, and password matching.
o	Uses CSRF tokens for secure form submission.
________________________________________
Potential Improvements
1.	Styling Enhancements: 
o	Adopt CSS custom properties (e.g., --brand1, --brand2) from the web portal login page for consistent colors.
o	Replace the table-based form layout (#tablesubscribe) with CSS Grid or Flexbox to match the login page’s modern design.
o	Add hover/focus states for inputs and buttons, similar to .doli-input and .doli-btn.

2.	CAPTCHA Enhancements: 

o	Consider a more modern CAPTCHA solution (e.g., reCAPTCHA) for better user experience and security.
o	Add client-side validation for the CAPTCHA field to provide immediate feedback.
3.	Accessibility:
 
o	Add aria-label or aria-describedby to form fields and CAPTCHA for screen reader support.
o	Ensure sufficient color contrast for text and icons (e.g., .star for required fields).
o	Make the CAPTCHA refresh button keyboard-accessible.
4.	Form Validation:
 
o	Add client-side JavaScript validation for required fields and email format.
o	Display error messages next to fields instead of at the top.

5.	Responsive Design:
 
o	Test on very small screens (<360px) to ensure no content overflow.
o	Adjust CAPTCHA image size (width="80" height="32") for mobile devices.

6.	Asset Optimization:
 
o	Compress the CAPTCHA image to reduce load times.
o	Cache the logo and CAPTCHA image for better performance.

7.	Error Handling: 

o	Improve error messaging for failed submissions (e.g., duplicate login, invalid CAPTCHA) with user-friendly text.
o	Log errors for debugging purposes.
________________________________________
Example Visual Description
•	Desktop View: A clean header with the company logo in the top-left corner, followed by a centered title (“New Subscription”). The form is displayed in a table with labeled fields, a CAPTCHA input with an image and refresh button, and Save/Cancel buttons. If no type is selected, a table lists membership types with Subscribe buttons.

•	Mobile View: The content stacks vertically, with the logo at the top-left, followed by the form or membership types table. Input fields and the CAPTCHA adjust to fit smaller screens.
________________________________________
Usage Instructions
1.	File Placement: Ensure the file is placed at dolibarr/public/members/new.php.

2.	Asset Setup:
 
o	Configure the company logo in Dolibarr’s settings or use MEMBER_IMAGE_PUBLIC_REGISTRATION.
o	Enable MAIN_SECURITY_ENABLECAPTCHA_MEMBER for CAPTCHA functionality.
3.	Configuration:
 
o	Enable the membership module (member) and public interface (MEMBER_ENABLE_PUBLIC).
o	Set optional constants: MEMBER_NEWFORM_FORCETYPE, MEMBER_NEWFORM_FORCEMORPHY, MEMBER_NEWFORM_FORCECOUNTRYCODE, MEMBER_NEWFORM_PAYONLINE, MEMBER_NEWFORM_DOLIBARRTURNOVER.

4.	Testing
: 
o	Verify CAPTCHA functionality (code validation, refresh button).
o	Test form submission with valid and invalid inputs, including duplicate logins and incorrect CAPTCHA.
o	Check responsiveness across devices (desktop, tablet, mobile).
o	Ensure translations ($langs->trans) work for all supported languages.
o	Test email notifications and payment redirects (if enabled).
5.	Maintenance:
 
o	Update logo and CAPTCHA image paths if assets change.
o	Monitor email notifications (ADHERENT_EMAIL_TEMPLATE_AUTOREGISTER) for delivery.
________________________________________
Notes
•	The CAPTCHA and header redesign align well with the web portal login page’s aesthetic but could adopt more of its CSS properties (e.g., --radius, --shadow) for a unified look.
•	The table-based layout for the form and membership types could be modernized to match the login page’s grid-based design.
•	The CAPTCHA implementation is basic; consider upgrading to a third-party solution for better security and user experience.
•	
Dolibarr ERP Partnership Module Customization Documentation
Overview
The new.php file, located at dolibarr/public/partnership/new.php, is a public-facing page in the Dolibarr ERP system that allows visitors to submit a partnership request. The customization enhances the default Dolibarr partnership form by:
1.	Adding a Google reCAPTCHA to prevent automated submissions and enhance security.
2.	Redesigning the header to align with the aesthetic of other customized pages, ensuring a consistent, branded user experience. The page supports partnership requests linked to third parties (or members, depending on configuration), with fields for company and contact details, and supports multilingual and multi-entity setups.
________________________________________
Key Features
1.	Google reCAPTCHA Integration: 
o	Enabled when PROJECT_RECAPTCHA_SITEKEY and PROJECT_RECAPTCHA_SECRET are configured in Dolibarr’s settings.
o	Displays a Google reCAPTCHA widget (v2 checkbox) using the site key.
o	Validates the reCAPTCHA response server-side via an API call to Google’s verification endpoint.
o	Prevents form submission if the reCAPTCHA is not completed or invalid.

2.	Redesigned Header
: 
o	The header is customized to match the aesthetic of the web portal login page and other public pages (dolibarr/public/members/new.php, dolibarr/public/recruitment/view.php).
o	Uses a transparent background (backgreypublicpayment) with no border for a clean look.
o	Hides default links (e.g., "Powered by Dolibarr") to maintain a branded experience.
o	Left-aligns the content for consistency with other pages.

3.	Partnership Form: 

o	Allows users to submit a partnership request with fields for: 
	Partnership Type (required, unless forced via PARTNERSHIP_NEWFORM_FORCETYPE)
	Company Name (required)
	First Name (required)
	Last Name (required)
	Email (required)
	URL (required, with optional validation against PARTNERSHIP_BACKLINKS_TO_CHECK)
	Address
	Zip/Town
	Country (can be forced via PARTNERSHIP_NEWFORM_FORCECOUNTRYCODE)
	State
	Comments
	Extrafields (if defined)
o	Supports linking to an existing or new third party (Societe) based on PARTNERSHIP_IS_MANAGED_FOR.

4.	Responsive Design: 

o	The form is centered and uses responsive classes (minwidth150, maxwidth300, quatrevingtpercent) for input fields and textareas.
o	The table-based layout (#tablesubscribe) adapts to different screen sizes.

5.	Security Features:
 
o	Google reCAPTCHA prevents automated submissions.
o	Limits submissions per IP address (MAIN_SECURITY_MAX_POST_ON_PUBLIC_PAGES_BY_IP_ADDRESS).
o	Validates required fields and email format.
o	Uses CSRF tokens (newToken()) for secure form submission.
o	Checks for duplicate third parties by name or email.

6.	Email Notifications:
 
o	Includes commented-out code for sending confirmation emails to the partner (PARTNERSHIP_EMAIL_TEMPLATE_AUTOREGISTER) and notifications to the organization (PARTNERSHIP_AUTOREGISTER_NOTIF_MAIL).
o	Currently disabled, but can be enabled with proper configuration.

7.	Payment Integration: 

o	Includes commented-out code for online payment integration (PayPal, Paybox, Stripe) via PARTNERSHIP_NEWFORM_PAYONLINE.
o	Supports redirecting to a payment URL after submission if enabled.
________________________________________
Code Structure
1. PHP Logic
•	Environment Setup: 
o	Defines constants (NOLOGIN, NOCSRFCHECK, NOIPCHECK, NOBROWSERNOTIF) to allow public access, bypass CSRF checks, disable IP restrictions, and suppress browser notifications.
o	Loads Dolibarr core files and partnership-related classes (Partnership, PartnershipType, Societe, FormCompany, etc.).
o	Supports multi-entity setups with DOLENTITY based on the entity parameter.

•	Parameters:
 
o	Retrieves form inputs (action, backtopage, partnershiptype, societe, firstname, lastname, email, etc.) using GETPOST.

•	Security Checks: 

o	Ensures the partnership module is enabled (partnership).
o	Verifies PARTNERSHIP_ENABLE_PUBLIC is set to allow public access.

•	reCAPTCHA Validation: 

o	Checks for PROJECT_RECAPTCHA_SITEKEY and PROJECT_RECAPTCHA_SECRET.
o	Validates the g-recaptcha-response token using a cURL request to Google’s API (https://www.google.com/recaptcha/api/siteverify).
o	Adds errors for missing or invalid reCAPTCHA responses.

•	Form Submission (action == "add"):
 
o	Validates required fields (Partnership Type, Company, First Name, Last Name, Email, URL).
o	Checks email format and third-party uniqueness (by name or email).
o	Creates or updates a Societe (third party) based on input.
o	Creates a Partnership object, sets its properties, and saves it to the database.
o	Limits submissions per IP address to prevent abuse.
o	Redirects to a custom URL (PARTNERSHIP_URL_REDIRECT_SUBSCRIPTION), backtopage, or the same page with action=added.

•	Success Page (action == "added"):
 
o	Displays a confirmation message (NewPartnershipbyWeb) if no redirect is configured.
2. CSS Styling
•	Header Redesign: 
css
div.backgreypublicpayment {
    background-color: transparent;
    border-bottom: none;
    text-align: left;
}
.backgreypublicpayment a {
    display: none;
}
o	Sets a transparent background (backgreypublicpayment) with no border to match the clean aesthetic of the web portal login page.
o	Hides default links (e.g., "Powered by Dolibarr") for a branded look.
o	Left-aligns content for consistency with other pages.

•	Form Styling:
 
o	Uses Dolibarr’s default classes (minwidth150, maxwidth300, quatrevingtpercent) for responsive input sizing.
o	Applies classfortooltip for required fields with tooltips.
o	Centers the reCAPTCHA widget with text-align: center; padding: 20px 0.
3. HTML Structure
•	Header: 

o	Calls llxHeaderVierge to render a custom header with the company logo.
o	Includes the Google reCAPTCHA script (https://www.google.com/recaptcha/api.js) if PROJECT_RECAPTCHA_SITEKEY is set.
o	Uses a transparent background and left-aligned content.

•	Main Content:
 
o	Displays a title (NewPartnershipRequest) with a hands-helping icon.
o	Shows a help text (PARTNERSHIP_NEWFORM_TEXT or a default description).
o	Renders errors using dol_htmloutput_errors.

•	Form: 

o	A form (name="newmember") with hidden fields for token, entity, and action.
o	Displays a table (#tablesubscribe) with fields for partnership type, company, first name, last name, email, URL, address, zip/town, country, state, comments, and extrafields.
o	Includes a reCAPTCHA widget (g-recaptcha) if enabled, centered in the table.
o	Save and Cancel buttons are centered at the bottom.

•	Footer: 

o	Calls llxFooterVierge to render a standard footer without the "Powered by Dolibarr" section.
4. JavaScript:
•	Handles dynamic form behavior: 

o	Submits the form on changes to country_id to refresh dependent fields (e.g., state dropdown).

•	Loads lib_foot.js.php for jQuery and Dolibarr’s standard JavaScript utilities.
•	Includes the Google reCAPTCHA script for the widget.
________________________________________
Customization Details
1.	Google reCAPTCHA Addition: 

o	Enabled via PROJECT_RECAPTCHA_SITEKEY and PROJECT_RECAPTCHA_SECRET.
o	Adds a reCAPTCHA v2 checkbox widget (<div class="g-recaptcha" data-sitekey="...">).
o	Validates the response server-side using a cURL request to Google’s API.
o	Displays errors for missing or invalid reCAPTCHA responses.

2.	Header Redesign:
 
o	Aligns with the web portal login page (dolibarr/public/webportal/index.php), membership form (dolibarr/public/members/new.php), and recruitment page (dolibarr/public/recruitment/view.php): 
	Transparent background (backgreypublicpayment) matches the clean aesthetic.
	Hides default links to maintain a branded look.
	Left-aligns content (text-align: left) for consistency.
o	Uses llxHeaderVierge and htmlPrintOnlineHeader to render the customized header.

3.	Alignment with Other Pages: 

o	The header styling is consistent across public pages, using a transparent background and hidden links.
o	The form’s table-based layout could be modernized to use CSS Grid or Flexbox, as seen in the login page’s .doli-login-shell.
________________________________________
Implementation Details
1.	File Path: 

o	Located at dolibarr/public/partnership/new.php.
o	References Google’s reCAPTCHA API and company logo paths for the header.

2.	Dependencies:
 
o	Relies on the partnership module (partnership).
o	Uses core classes: Partnership, PartnershipType, Societe, FormCompany, ExtraFields.
o	Requires translation strings (main, members, partnership, companies, install, other).
o	Uses cURL for reCAPTCHA validation and jQuery for form behavior.

3.	Styling Approach:
 
o	Inline CSS for header redesign ensures minimal changes to Dolibarr’s default styles.
o	Leverages existing Dolibarr classes (minwidth150, quatrevingtpercent) for form inputs.
o	Centers the reCAPTCHA widget with inline styles for proper alignment.

4.	Security:
 
o	Google reCAPTCHA prevents automated submissions.
o	Limits submissions per IP address (MAIN_SECURITY_MAX_POST_ON_PUBLIC_PAGES_BY_IP_ADDRESS).
o	Validates required fields, email format, and third-party uniqueness.
o	Uses CSRF tokens for secure form submission.
________________________________________
Potential Improvements
1.	Styling Enhancements: 

o	Adopt CSS custom properties (e.g., --brand1, --brand2) from the web portal login page for consistent colors.
o	Replace the table-based form layout (#tablesubscribe) with CSS Grid or Flexbox to match the login page’s modern design.
o	Add hover/focus states for inputs and buttons, similar to .doli-input and .doli-btn.

2.	reCAPTCHA Enhancements:
 
o	Consider using reCAPTCHA v3 for a less intrusive experience (invisible CAPTCHA).
o	Add client-side validation to provide immediate feedback on reCAPTCHA errors.

3.	Accessibility: 

o	Add aria-label or aria-describedby to form fields and reCAPTCHA for screen reader support.
o	Ensure sufficient color contrast for text and icons (e.g., .star for required fields).
o	Make the reCAPTCHA widget keyboard-accessible.

4.	Form Validation:
 
o	Add client-side JavaScript validation for required fields, email format, and URL.
o	Display error messages next to fields instead of at the top.

5.	Responsive Design:
 
o	Test on very small screens (<360px) to ensure no content overflow.
o	Adjust reCAPTCHA widget size for mobile devices.

6.	Email Notifications:
 
o	Enable the commented-out email notification code for confirmation emails and organization notifications.
o	Ensure templates (PARTNERSHIP_EMAIL_TEMPLATE_AUTOREGISTER) are configured correctly.

7.	Payment Integration:
 
o	Enable the commented-out payment integration code if online payments are needed.
o	Test redirects to PayPal, Paybox, or Stripe with proper security tokens.

8.	Error Handling: 

o	Improve error messaging for failed submissions (e.g., duplicate third party, invalid reCAPTCHA) with user-friendly text.
o	Log errors for debugging purposes.
________________________________________
Example Visual Description
•	Desktop View: A clean header with a transparent background and left-aligned content, followed by a centered title (“New Partnership Request”). The form is displayed in a table with labeled fields, a reCAPTCHA widget centered at the bottom, and Save/Cancel buttons. The layout is clean and professional, matching other public pages.

•	Mobile View: The content stacks vertically, with input fields and the reCAPTCHA widget resizing to fit smaller screens. The header remains transparent, and the form is centered for usability.
________________________________________
Usage Instructions
1.	File Placement: Ensure the file is placed at dolibarr/public/partnership/new.php.
2.	Asset Setup: 

o	Configure the company logo in Dolibarr’s settings or use PARTNERSHIP_IMAGE_PUBLIC_REGISTRATION.
o	Set PROJECT_RECAPTCHA_SITEKEY and PROJECT_RECAPTCHA_SECRET for reCAPTCHA functionality.
3.	Configuration: 

o	Enable the partnership module (partnership) and public interface (PARTNERSHIP_ENABLE_PUBLIC).
o	Set optional constants: PARTNERSHIP_NEWFORM_FORCETYPE, PARTNERSHIP_NEWFORM_FORCECOUNTRYCODE, PARTNERSHIP_BACKLINKS_TO_CHECK.
4.	Testing:
 
o	Verify reCAPTCHA functionality (widget display, validation, error handling).
o	Test form submission with valid and invalid inputs, including duplicate third parties and incorrect reCAPTCHA.
o	Check responsiveness across devices (desktop, tablet, mobile).
o	Ensure translations ($langs->trans) work for all supported languages.
o	Test redirects (backtopage, PARTNERSHIP_URL_REDIRECT_SUBSCRIPTION).

5.	Maintenance: 
o	Update reCAPTCHA keys and logo paths if assets change.
o	Monitor cURL requests for reCAPTCHA validation to ensure reliability.
________________________________________
Notes
•	The reCAPTCHA and header redesign align well with the web portal login page, membership form, and recruitment page, but could adopt more CSS properties (e.g., --radius, --shadow) for a unified look.
•	The table-based form layout could be modernized to use CSS Grid or Flexbox, as seen in the login page.
•	The commented-out email and payment integration code suggests potential features that could be enabled with further configuration.






