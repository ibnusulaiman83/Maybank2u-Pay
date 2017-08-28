<?php
/*
 *  Start Session
 */
session_start();

/*
 * This file will require all needed files.
 * Will use it as use (namespace)
 * It must be imported according to the priority
 * 1. Configuration File
 * 2. Database Connector
 * 3. Database Main
 */

/*
 * Load Configuration File
 */
require __DIR__ . '/../config.php';

/*
 * Load Database Helper File.
 * It must load DB Connect file first.
 */
require __DIR__ . '/../models/db_connect.php';
require __DIR__ . '/../models/db_main.php';

/*
 * Load all helper file
 */

require __DIR__ . '/../helpers/route.php';
require __DIR__ . '/../helpers/M2UPay.php';
require __DIR__ . '/../helpers/M2UCallback.php';
require __DIR__ . '/../helpers/create.php';
require __DIR__ . '/../helpers/pay.php';
require __DIR__ . '/../helpers/ajax.php';