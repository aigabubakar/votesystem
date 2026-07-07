<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Hooks
|--------------------------------------------------------------------------
| Using hooks to auto-update election status based on date/time.
*/
$hook['post_controller_constructor'] = array(
    'class'    => 'ElectionStatusHook',
    'function' => 'update_statuses',
    'filename' => 'ElectionStatusHook.php',
    'filepath' => 'hooks'
);
