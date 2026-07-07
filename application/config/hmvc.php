<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| HMVC Setup – loaded by CI3 via MY_Loader / MY_Router
|--------------------------------------------------------------------------
|
| This file bootstraps the Wiredesignz MX HMVC extension.
| It is included from the MY_Loader.php and MY_Router.php core overrides.
|
*/

// Load HMVC core classes
require_once APPPATH . 'third_party/MX/Modules.php';

// Register module locations: directory => path offset
MX_Modules::register([
    APPPATH . 'modules/' => '../modules/',
]);
