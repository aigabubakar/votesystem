<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Bootstrap HMVC
require_once APPPATH . 'third_party/MX/Modules.php';
require_once APPPATH . 'third_party/MX/Loader.php';

if (file_exists(APPPATH . 'core/MY_Model.php')) {
    require_once APPPATH . 'core/MY_Model.php';
}

MX_Modules::register([
    APPPATH . 'modules/' => APPPATH . 'modules/',
]);

/**
 * MY_Loader – extends MX_Loader (which extends CI_Loader).
 */
#[\AllowDynamicProperties]
class MY_Loader extends MX_Loader
{
    public function __construct()
    {
        parent::__construct();
    }
}
