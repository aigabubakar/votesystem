<?php
/**
 * Modular Extensions - HMVC
 * Wiredesignz – PHP 8.2 patched
 *
 * MX_Controller – base controller for modules.
 */
#[\AllowDynamicProperties]
class MX_Controller extends CI_Controller
{
    /** @var CI_Controller */
    public ?CI_Controller $ci = null;

    public function __construct()
    {
        parent::__construct();
        $this->ci =& get_instance();

        // Copy CI properties to this controller
        foreach (get_object_vars($this->ci) as $key => $val) {
            $this->$key =& $this->ci->$key;
        }

        log_message('debug', 'MX_Controller initialized: ' . get_class($this));
    }
}
