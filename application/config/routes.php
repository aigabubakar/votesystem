<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Default Route – redirect to auth/login
|--------------------------------------------------------------------------
*/
$route['default_controller'] = 'auth';
$route['404_override']       = '';
$route['translate_uri_dashes'] = FALSE;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
$route['login']           = 'auth/login';
$route['logout']          = 'auth/logout';
$route['register']        = 'auth/register';

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
$route['admin']                          = 'admin/dashboard';
$route['admin/elections']               = 'admin/elections/index';
$route['admin/elections/create']        = 'admin/elections/create';
$route['admin/elections/edit/(:num)']   = 'admin/elections/edit/$1';
$route['admin/elections/delete/(:num)'] = 'admin/elections/delete/$1';
$route['admin/elections/status/(:num)/(:alpha)'] = 'admin/elections/change_status/$1/$2';

$route['admin/positions/(:num)']              = 'admin/positions/index/$1';
$route['admin/positions/create/(:num)']       = 'admin/positions/create/$1';
$route['admin/positions/edit/(:num)']         = 'admin/positions/edit/$1';
$route['admin/positions/delete/(:num)']       = 'admin/positions/delete/$1';

$route['admin/candidates']                          = 'admin/candidates/index';
$route['admin/candidates/election/(:num)']          = 'admin/candidates/by_election/$1';
$route['admin/candidates/approve/(:num)']           = 'admin/candidates/approve/$1';
$route['admin/candidates/reject/(:num)']            = 'admin/candidates/reject/$1';
$route['admin/candidates/create']                   = 'admin/candidates/create';
$route['admin/candidates/delete/(:num)']            = 'admin/candidates/delete/$1';

$route['admin/students']                    = 'admin/students/index';
$route['admin/students/create']             = 'admin/students/create';
$route['admin/students/edit/(:num)']        = 'admin/students/edit/$1';
$route['admin/students/delete/(:num)']      = 'admin/students/delete/$1';
$route['admin/students/toggle/(:num)']      = 'admin/students/toggle/$1';

$route['admin/departments']               = 'admin/departments/index';
$route['admin/departments/create']        = 'admin/departments/create';
$route['admin/departments/edit/(:num)']   = 'admin/departments/edit/$1';
$route['admin/departments/delete/(:num)'] = 'admin/departments/delete/$1';

$route['admin/results']                   = 'admin/results/index';
$route['admin/results/view/(:num)']       = 'admin/results/view/$1';
$route['admin/results/publish/(:num)']    = 'admin/results/publish/$1';

/*
|--------------------------------------------------------------------------
| Voter Routes
|--------------------------------------------------------------------------
*/
$route['voter']                          = 'voter/dashboard';
$route['voter/ballot/(:num)']            = 'voter/vote/ballot/$1';
$route['voter/cast']                     = 'voter/vote/cast';
$route['voter/results/(:num)']           = 'voter/vote/results/$1';
$route['voter/profile']                  = 'voter/dashboard/profile';
$route['voter/apply/(:num)']             = 'voter/dashboard/apply/$1';

$route['verify']              = 'auth/verify/index';
$route['verify/email']        = 'auth/verify/email';
$route['verify/resend_email'] = 'auth/verify/resend_email';

$route['admin/students/verify_matric/(:num)'] = 'admin/students/verify_matric/$1';
