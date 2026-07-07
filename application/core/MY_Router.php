<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'third_party/MX/Modules.php';
require_once APPPATH . 'third_party/MX/Router.php';

MX_Modules::register([
    APPPATH . 'modules/' => APPPATH . 'modules/',
]);

/**
 * MY_Router – extends MX_Router which handles module segment resolution.
 */
#[\AllowDynamicProperties]
class MY_Router extends MX_Router
{
    public function __construct($routing = NULL)
    {
        parent::__construct($routing);
        log_message('debug', 'MY_Router initialized with HMVC support');
    }
}
