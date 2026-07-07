<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ElectionStatusHook
 * Automatically transitions election statuses based on current date/time.
 * Runs on every request (post_controller_constructor).
 */
class ElectionStatusHook
{
    public function update_statuses(): void
    {
        $CI =& get_instance();
        $now = date('Y-m-d H:i:s');

        // Activate pending elections whose start_date has passed
        $CI->db->set('status', 'active')
               ->where('status', 'pending')
               ->where('start_date <=', $now)
               ->update('elections');

        // Close active elections whose end_date has passed
        $CI->db->set('status', 'closed')
               ->where('status', 'active')
               ->where('end_date <=', $now)
               ->update('elections');
    }
}
