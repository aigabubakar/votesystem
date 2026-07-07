<?php
/**
 * NUSSA Vote – CodeIgniter 3 Front Controller
 * PHP 8.2 Compatible
 */

define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

// The directory name of the "application" folder
$application_folder = 'application';

// The directory name of the "system" folder
$system_path = 'system';

// The $assign_to_config array below will be passed dynamically
// to the config class when initialized.
$assign_to_config['name'] = 'NUSSA Vote';

/*
 |---------------------------------------------------------------
 | DEFAULT CONTROLLER
 |---------------------------------------------------------------
 */
$routing['default_controller'] = 'auth';
$routing['404_override'] = '';
$routing['translate_uri_dashes'] = FALSE;

// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */
if (defined('STDIN')) {
    chdir(dirname(__FILE__));
}

if (($_temp = realpath($system_path)) !== FALSE) {
    $system_path = $_temp . DIRECTORY_SEPARATOR;
} else {
    // Ensure there's a trailing slash
    $system_path = strtr(rtrim($system_path, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
}

// Is the system path correct?
if (!is_dir($system_path)) {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: ' . pathinfo(__FILE__, PATHINFO_BASENAME);
    exit(3);
}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_path);
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('SYSDIR', basename(BASEPATH));

// Is the application folder above or below the WEBROOT?
if (is_dir($application_folder)) {
    if (($_temp = realpath($application_folder)) !== FALSE) {
        $application_folder = $_temp;
    }
    define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);
} else {
    if (!is_dir(BASEPATH . $application_folder . DIRECTORY_SEPARATOR)) {
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: ' . SELF;
        exit(3);
    }
    define('APPPATH', BASEPATH . $application_folder . DIRECTORY_SEPARATOR);
}

// The path to the "views" directory
$view_folder = '';
if (empty($view_folder)) {
    $view_folder = $application_folder . DIRECTORY_SEPARATOR . 'views';
}
if (($_temp = realpath($view_folder)) !== FALSE) {
    $view_folder = $_temp . DIRECTORY_SEPARATOR;
} else {
    $view_folder = rtrim($view_folder, '/\\') . DIRECTORY_SEPARATOR;
}
define('VIEWPATH', $view_folder);

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 */
require_once BASEPATH . 'core/CodeIgniter.php';
