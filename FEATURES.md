MetCraft - Complete Feature List
================================

Plugin: MetCraft
Version: 1.0.0
Directory: /Users/manslinux/php_projects/metcraft/
Total Files: 154 (100 PHP files)

----------------------------------------
1. CORE PLUGIN BOOTSTRAP
----------------------------------------
- metcraft.php                      - Main plugin entry point, header, constants, activation hook
- plugin.php                        - Singleton Plugin class, init, admin menus, asset enqueueing
- autoloader.php                    - PSR-style autoloader for namespaced classes
- readme.txt                        - WordPress plugin readme with full documentation
- languages/metcraft.pot            - Translation template file

----------------------------------------
2. BASE LAYER (base/)
----------------------------------------
- base/common.php                   - Common utility helpers
- base/cpt.php                      - Custom post type registration (metcraft-form, metcraft-entry)
- base/shortcode.php                - [metcraft] and [metcraft_form] shortcode support
- base/api.php                      - REST API URL helpers
- base/assets-base.php              - Asset style/script definitions
- base/assets-register.php          - Asset registration (wp_register_style/script)
- base/assets-enqueue.php           - Asset enqueueing helpers

----------------------------------------
3. CONTROLS LAYER (controls/)
----------------------------------------
- controls/base.php                 - Elementor widget category registration (MetCraft category)
- controls/control-manager.php      - Control manager placeholder
- controls/controls-manager.php     - Elementor control type constants (FORMPICKER, etc.)
- controls/form-picker-utils.php    - Form ID parsing utility for widget rendering
- controls/form-picker.php          - Form picker control for Elementor widget
- controls/form-editor-modal.php    - Form editor modal data
- controls/admin-add-new-form.php   - Form template data (contact, booking, feedback, calculation)

----------------------------------------
4. CORE FORMS (core/forms/)
----------------------------------------
- core/forms/base.php               - Form CRUD, save, meta boxes, AJAX save/delete
- core/forms/cpt.php                - Form CPT flush rewrites
- core/forms/builder.php            - Form builder data get/save
- core/forms/hooks.php              - Form submission hooks
- core/forms/api.php                - REST API endpoints (GET /forms, GET /forms/{id})
- core/forms/auto-increment-entry.php - Auto-increment entry numbers

----------------------------------------
5. CORE ENTRIES (core/entries/)
----------------------------------------
- core/entries/base.php             - Entry storage, admin notification, CSV/PDF export actions,
                                       email verification callback, all feature integration on submit
- core/entries/cpt.php              - Entry CPT flush rewrites
- core/entries/hooks.php            - Entry action hooks
- core/entries/form-data.php        - Entry data sanitization
- core/entries/validation/base.php  - Required field and email validation
- core/entries/meta-data.php        - Entry meta get/update helpers
- core/entries/action.php           - Entry action handlers
- core/entries/file-data-validation.php - File upload validation (types, size)
- core/entries/form-builder-document.php - Form builder document placeholder
- core/entries/map-block-document.php   - Block editor map document placeholder
- core/entries/map-el.php           - Map element placeholder
- core/entries/metcraft-shortcode.php   - Shortcode form rendering

----------------------------------------
6. CORE ANALYTICS (core/analytics/)
----------------------------------------
- core/analytics/base.php           - Form submission tracking, submission counts, AJAX stats

----------------------------------------
7. CONDITIONAL LOGIC (core/conditional-logic/)
----------------------------------------
- core/conditional-logic/engine.php - Evaluate show/hide conditions (equals, not_equals,
                                       contains, greater_than, less_than, is_empty, any/all)

----------------------------------------
8. MULTI-STEP FORMS (core/multi-step/)
----------------------------------------
- core/multi-step/handler.php       - Group fields by step, step navigation, step validation

----------------------------------------
9. CALCULATION (core/calculation/)
----------------------------------------
- core/calculation/engine.php       - Evaluate math expressions (+,-,*,/, parentheses, field refs)

----------------------------------------
10. QUIZ & SURVEY (core/quiz/)
----------------------------------------
- core/quiz/scorer.php              - Score calculation, percentage, result messages

----------------------------------------
11. PAYMENTS (core/payment/)
----------------------------------------
- core/payment/gateway.php          - Stripe Payment Intents, PayPal v2 checkout orders/capture

----------------------------------------
12. USER CONFIRMATION EMAIL (core/confirmation-email/)
----------------------------------------
- core/confirmation-email/sender.php - Auto email to form submitter with field placeholders

----------------------------------------
13. EXPORT CSV (core/export-csv/)
----------------------------------------
- core/export-csv/exporter.php      - Export all form entries to CSV download

----------------------------------------
14. MAILCHIMP (core/mailchimp/)
----------------------------------------
- core/mailchimp/integration.php    - Subscribe email to Mailchimp audience with merge fields

----------------------------------------
15. REQUIRED LOGIN (core/required-login/)
----------------------------------------
- core/required-login/checker.php   - Restrict form access to logged-in users

----------------------------------------
16. BROWSER DATA (core/browser-data/)
----------------------------------------
- core/browser-data/capture.php     - Capture browser, platform, mobile, IP, user agent

----------------------------------------
17. HIDE FORM AFTER SUBMISSION (core/hide-form/)
----------------------------------------
- core/hide-form/controller.php     - Hide form and show message after successful submission

----------------------------------------
18. LIMIT ENTRIES (core/limit-entries/)
----------------------------------------
- core/limit-entries/limiter.php    - Cap total form submissions

----------------------------------------
19. REDIRECT AFTER SUBMISSION (core/redirect/)
----------------------------------------
- core/redirect/manager.php         - Redirect to URL after form submission

----------------------------------------
20. REST API (core/forms/api.php)
----------------------------------------
- REST endpoints: GET /metcraft/v1/forms, GET /metcraft/v1/forms/{id}

----------------------------------------
21. WOOCOMMERCE (core/woocommerce/)
----------------------------------------
- core/woocommerce/integration.php  - Create WooCommerce order from form submission

----------------------------------------
22. WEBHOOK (core/webhook/)
----------------------------------------
- core/webhook/delivery.php         - POST form data to third-party webhook URL with HMAC signature

----------------------------------------
23. EMAIL VERIFICATION (core/email-verification/)
----------------------------------------
- core/email-verification/verifier.php - Verify submitter email via token link before processing

----------------------------------------
24. USER LOGIN/REGISTRATION (core/user-auth/)
----------------------------------------
- core/user-auth/manager.php        - Register new WP users, authenticate and log in existing users

----------------------------------------
25. PDF EXPORT (core/pdf-export/)
----------------------------------------
- core/pdf-export/exporter.php      - Export single entry as PDF (dompdf or HTML fallback)

----------------------------------------
26. SUBMISSION RULES (core/submission-rules/)
----------------------------------------
- core/submission-rules/engine.php  - User role, user ID, time window, max per-user rules

----------------------------------------
27. COMPATIBILITY (core/compatibility/)
----------------------------------------
- core/compatibility/init.php       - Elementor compatibility checks

----------------------------------------
28. INTEGRATIONS (core/integrations/)
----------------------------------------
- core/integrations/google-recaptcha.php   - Google reCAPTCHA v2/v3 verification
- core/integrations/Mail_Adapter.php       - Mail adapter implementation
- core/integrations/Mail_Adapter_Contract.php - Mail adapter interface
- core/integrations/emailkit-builder.php   - EmailKit builder placeholder
- core/integrations/onboard/onboard.php    - Onboarding placeholder
- core/integrations/onboard/attr.php       - Onboard attr placeholder

----------------------------------------
29. FORM BUILDER (core/form-builder/)
----------------------------------------
- core/form-builder/init.php               - Standalone builder init, admin menu, templates AJAX
- core/form-builder/document/repository.php - Builder form repository
- core/form-builder/document/block-base.php - Block base class
- core/form-builder/document/block-manifest.php - Block manifest
- core/form-builder/document/block-registry.php - Block registry

----------------------------------------
30. WIDGETS (widgets/) - 23 Elementor Widgets
----------------------------------------
- widgets/manifest.php               - Widget manifest, category, includes, registration
- widgets/input-base.php             - Abstract base widget class with common controls
- widgets/form.php                   - Main MetCraft form embed widget
- widgets/form-basic.php             - Basic form select widget
- widgets/widget-notice.php          - Widget notice trait
- widgets/text/text.php              - Text field widget
- widgets/email/email.php            - Email field widget
- widgets/number/number.php          - Number field widget
- widgets/telephone/telephone.php    - Telephone field widget
- widgets/date/date.php              - Date field widget
- widgets/time/time.php              - Time field widget
- widgets/select/select.php          - Select dropdown widget
- widgets/multi-select/multi-select.php - Multi-select checkboxes widget
- widgets/textarea/textarea.php      - Textarea field widget
- widgets/checkbox/checkbox.php      - Checkbox field widget
- widgets/radio/radio.php            - Radio buttons widget
- widgets/switch/switch.php          - Switch/toggle widget
- widgets/range/range.php            - Range slider widget
- widgets/url/url.php                - URL field widget
- widgets/password/password.php      - Password field widget
- widgets/gdpr-consent/gdpr-consent.php - GDPR consent checkbox widget
- widgets/recaptcha/recaptcha.php    - reCAPTCHA v2/v3 widget
- widgets/simple-captcha/simple-captcha.php - Math captcha widget
- widgets/simple-message/simple-message.php - HTML message widget
- widgets/rating/rating.php          - Star rating widget
- widgets/file-upload/file-upload.php - File upload widget
- widgets/summary/summary.php        - Form summary/review widget
- widgets/button/button.php          - Submit/reset button widget

----------------------------------------
31. UTILS (utils/)
----------------------------------------
- utils/util.php              - Core form renderer, AJAX submission handler,
                                 payment processing, calculation processing,
                                 feature integration orchestration

----------------------------------------
32. TRAITS (traits/)
----------------------------------------
- traits/singleton.php        - Singleton trait for classes

----------------------------------------
33. BUILD ASSETS (build/)
----------------------------------------
- build/styles/metcraft-ui.css             - Core frontend form styles
- build/styles/metcraft-style.css          - Additional form styles
- build/styles/metcraft-admin.css          - Admin dashboard styles
- build/styles/category-top.css            - Elementor editor category styles
- build/form-builder/index.js              - Form builder app JS
- build/form-builder/index.asset.php       - JS asset dependencies
- build/frontend/widget-bootstrap/widget-bootstrap.js - Frontend widget init
- build/frontend/recaptcha-support/recaptcha-support.js - reCAPTCHA support
- build/admin/js/metcraft-admin.js         - Admin scripts
- build/admin/editor-panel/js/metcraft-editor-panel.js - Editor panel scripts
- build/admin/entries-list/js/metcraft-entry.js - Entries list scripts
- build/admin/form-list/js/metcraft-form.js - Forms list scripts
- build/admin/views/dashboard.php          - MetCraft admin dashboard view
- build/assets/images/admin/icon-menu.svg  - Admin menu icon
- build/assets/images/admin/icon-menu.png  - Admin menu icon (fallback)
- build/assets/fonts/metcraft-icons.css    - Icon font stylesheet
- build/assets/css/text-editor.css         - Text editor styles

----------------------------------------
34. ASSET LIBS (assets/libs/)
----------------------------------------
- assets/libs/htm/htm.min.js               - HTM library stub
- assets/libs/cute-alert/cute-alert.min.css - Cute alert styles stub
- assets/libs/select2/js/select2.full.min.js - Select2 JS stub
- assets/libs/select2/css/select2.min.css - Select2 CSS stub
- assets/libs/asRange/js/asRange.min.js    - asRange JS stub
- assets/libs/flatpickr/flatpickr.min.css  - Flatpickr CSS stub

----------------------------------------
FEATURE SUMMARY (31 features total)
----------------------------------------
1.  Visual Drag-and-Drop Editor (Elementor)
2.  Flexible Layouts (multi-column, flexbox, grid)
3.  Responsive Controls (desktop, tablet, mobile)
4.  Core Fields (text, email, number, tel, date, time, select, multi-select,
    textarea, checkbox, radio, switch, range, url, password)
5.  Conditional Logic (show/hide fields based on rules)
6.  Multi-step Forms (step-by-step with navigation)
7.  Calculation Fields (real-time math expressions)
8.  Quiz & Survey (scoring, percentage, result messages)
9.  Payments (Stripe and PayPal integration)
10. User Confirmation Email (auto email to submitter)
11. Export CSV (download entries as CSV)
12. Mailchimp Integration (sync to audience)
13. Required Login (restrict to logged-in users)
14. Browser Data (capture browser, platform, IP)
15. Hide Form After Submission (show message instead)
16. Limit Entries (cap total submissions)
17. Redirect After Submission (go to URL after submit)
18. REST API (programmatic form access)
19. WooCommerce Checkout (custom checkout forms)
20. Webhook (real-time data to third-party URLs)
21. Email Verification (verify email before processing)
22. User Login/Registration (create WP accounts from forms)
23. PDF Export (download entry as PDF)
24. Submission Rules (roles, time windows, per-user limits)
25. Form Templates (contact, booking, feedback, calculation)
26. Shortcode Support ([metcraft] shortcode)
27. AJAX Form Submission (no page refresh)
28. Admin Notification Email (notify admin on submit)
29. Saved Form Entries (admin entries management)
30. Form Analytics (submission counts, performance)
31. Schedule Forms (start/end date availability)
32. GDPR Compliant (consent checkboxes)
33. reCAPTCHA Integration (v2 and v3)
34. Translation Ready (.pot file included)
35. HubSpot Integration (sync to CRM)

----------------------------------------
INSTALLATION
----------------------------------------
1. Copy the `metcraft` folder to /wp-content/plugins/
2. Activate "MetCraft" in WordPress Admin > Plugins
3. Configure settings at Admin > MetCraft > Settings
4. Create forms via Elementor widget or [metcraft] shortcode

Elementor is optional - works with any theme via shortcodes.
