<?php
/*
 * Database Information
 */
define('DB_SERVER', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'change_here');
define('DB_PREFIX', 'm2u_');
define('DB_USERNAME', 'change_here');
define('DB_PASSWORD', 'change_here');

/*
 * System Information
 */
define('SYSTEM_URL', 'https://yourdomain.com/'); // MUST end WITH thrailing slash
define('DEVELOPMENT_MODE', false); // Unset this if not in development mode

/*
 * Signature for Validation Purpose
 */
define('SIGNATURE', 'aaabbbcccddd'); // Change to your preferred RANDOM key

/*
 * Maybank2U Pay Information
 * MODE: UAT/PRODUCTION
 */
define('PAYEE_CODE', 'AAA');
define('MODE', 'UAT');
define('CALLBACK_ID', 'eeffgghhhii'); // <SYSTEM_URL> callback/<CALLBACK_ID>

/*
 * Bill Settings
 * Note: Bill Expiration will only prevent the Bills Page from being loaded
 */
define('BILL_ID_LENGTH', 8);
define('BILL_EXPIRY', 10); // 10 Minutes
