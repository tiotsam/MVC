<?php 

/**
 * Used to define the routes in the system.
 * 
 * A route should be defined with a key matching the URL and an
 * controller#action-to-call method. E.g.:
 * 
 * '/' => 'index#index',
 * '/calendar' => 'calendar#index'
 */
$routes = array(
	'/test' => 'test#index',
	'/add/categorie' => 'application#addcateg',
	'/categories' => 'application#categories',
	'/update/categorie' => 'application#updatecateg',
	'/updatecategorie'=>'ajax#updatecategorie',

	'/add/film' => 'application#addfilm',
	'/films'=>'application#films',
	'/films/detail'=>'application#detailfilm',
	'/films/searchajaxparcategorie' => 'ajax#searchajaxparcategorie',

	'/user/register'=> 'user#registeruser',
	'/user/login'=> 'user#login',
	'/user/logout'=> 'user#logout',

	'/panier' => 'user#panier',
	'/addpanier' => 'ajax#addpanier',
	'/minpanier' => 'ajax#minpanier'
);
