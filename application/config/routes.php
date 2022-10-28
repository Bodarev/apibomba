<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$langs_array = array('ru', 'ro', 'en');
//$CI = &get_instance();
//$langs_array = $CI->language();
$langs = '(' . implode('|', $langs_array) . ')';

$route['default_controller'] = 'pages';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/* Frontend */
$route['content/sliders'] = 'content/sliders';

$route['category/all'] = 'category/find';
$route['category/tree'] = 'category/tree';
$route['category/popular'] = 'category/popular';
$route['category/search'] = 'category/search';
$route['category/subcategories'] = 'category/subcategories';
$route['category/findByIds'] = 'category/findByIds';
$route['category/findOne'] = 'category/findOne';
$route['category/deleteCache'] = 'category/deleteCache';

$route['product/search'] = 'product/search';
$route['product/find'] = 'product/find';
$route['product/filtered'] = 'product/filtered';
$route['product/findOne'] = 'product/findOne';
$route['product/findByType'] = 'product/findByType';

$route['cache/filters_for_category'] = 'cache/filters_for_category';
$route['cache/filters_for_set'] = 'cache/filters_for_set';
$route['cache/attributes_for_product'] = 'cache/attributes_for_product';
$route['cache/init_category_cache'] = 'cache/init_category_cache';

$route['uds/find'] = 'uds/find';
$route['uds/calc'] = 'uds/calc';
$route['uds/reduction'] = 'uds/reduction';
$route['uds/operations'] = 'uds/operations';
$route['uds/reward'] = 'uds/reward';
$route['uds/refund'] = 'uds/refund';

$route['delivery/dates'] = 'delivery/dates';
$route['delivery/amount'] = 'delivery/amount';
$route['delivery/online'] = 'delivery/online';

$route['una/send'] = 'una/send';

/* Default Routing */
$route[$langs . '/:any/:any/:any/:any/:any'] = 'pages/text_pages';
$route[$langs . '/:any/:any/:any/:any'] = 'pages/text_pages';
$route[$langs . '/:any/:any/:any'] = 'pages/text_pages';
$route[$langs . '/:any/:any'] = 'pages/text_pages';
$route[$langs . '/:any'] = 'pages/text_pages';

/* Dashboard */
$route['cp'] = "backend/auth/login";
$route['cp/login'] = "backend/auth/login";
$route['cp/logout'] = "backend/auth/logout";
$route['cp/delete_photo'] = "backend/dashboard/delete_photo";
$route['cp/delete_img_row'] = "backend/dashboard/delete_img_row";
$route['cp/delete_file'] = "backend/dashboard/delete_file";
$route['cp/change_select'] = "backend/dashboard/change_select";
$route['cp/change_check'] = "backend/dashboard/change_check";

$route['cp/(:any)'] = "backend/$1";
$route['cp/(:any)/(:any)'] = "backend/$1/$2";
$route['cp/(:any)/(:any)/(:any)'] = "backend/$1/$2/$3";
$route['cp/(:any)/(:any)/(:any)/(:any)'] = "backend/$1/$2/$3/$4";
