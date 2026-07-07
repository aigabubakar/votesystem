<?php
/**
 * Modular Extensions - HMVC
 * MX_Model – base model for modules.
 */
#[\AllowDynamicProperties]
class MX_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        log_message('debug', 'MX_Model initialized: ' . get_class($this));
    }
}
