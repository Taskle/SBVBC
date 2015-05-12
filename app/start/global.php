<?php

/*
  |--------------------------------------------------------------------------
  | Register The Laravel Class Loader
  |--------------------------------------------------------------------------
  |
  | In addition to using Composer, you may use the Laravel class loader to
  | load your controllers and models. This is useful for keeping all of
  | your classes in the "global" namespace without Composer updating.
  |
 */

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

));

/*
  |--------------------------------------------------------------------------
  | Application Error Logger
  |--------------------------------------------------------------------------
  |
  | Here we will configure the error logger setup for the application which
  | is built on top of the wonderful Monolog library. By default we will
  | build a basic log file setup which creates a single file for logs.
  |
 */

Log::useFiles(storage_path().'/logs/laravel.log');

/**
 * Handler for 404s
 */
App::missing(function($exception)
{
    return Response::view('errors.404', array(), 404);
});

/*
  |--------------------------------------------------------------------------
  | Application Error Handler
  |--------------------------------------------------------------------------
  |
  | Here you may handle any errors that occur in your application, including
  | logging them or displaying custom views for specific errors. You may
  | even register several error handlers to handle different types of
  | exceptions. If nothing is returned, the default error view is
  | shown, which includes a detailed stack trace during debug.
  |
 */

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
	
	$message = 'Url: ' . Request::fullUrl() .
                '<br><br>Input: ' . json_encode(Input::all()) .
                '<br><br>' . $exception . 
                '<br><br>' . $exception->getTraceAsString();
	
	// ignore 404s and MethodNotAllowedHttpExceptions
	if ($code == 404 || $exception instanceof MethodNotAllowedHttpException) {
		return Response::view('errors.404', array(), 404);
	}
	
	if (Config::getEnvironment() == 'production') {
		$data = array('exception' => $message);
		Mail::send('emails.error', $data, function($message) {
			$message->to(Config::get('app.error_email'))->subject('SBVBC Website Error');
		});
		Log::info('Error Email sent to ' . Config::get('settings.error_email'));
		return Response::view('errors.500', array(), 500);
	}
});

/*
  |--------------------------------------------------------------------------
  | Maintenance Mode Handler
  |--------------------------------------------------------------------------
  |
  | The "down" Artisan command gives you the ability to put an application
  | into maintenance mode. Here, you will define what is displayed back
  | to the user if maintenance mode is in effect for the application.
  |
 */

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
  |--------------------------------------------------------------------------
  | Require The Filters File
  |--------------------------------------------------------------------------
  |
  | Next we will load the filters file for the application. This gives us
  | a nice separate location to store our route and application filter
  | definitions instead of putting them all in the main routes file.
  |
 */

require app_path().'/filters.php';


/** Add Cloudflare proxies (see https://www.cloudflare.com/ips)
 *  so we get secure = true persisting
 */
Request::setTrustedProxies(array(
	'199.27.128.0/21',
	'173.245.48.0/20',
	'103.21.244.0/22',
	'103.22.200.0/22',
	'103.31.4.0/22',
	'141.101.64.0/18',
	'108.162.192.0/18',
	'190.93.240.0/20',
	'188.114.96.0/20',
	'197.234.240.0/22',
	'198.41.128.0/17',
	'162.158.0.0/15',
	'104.16.0.0/12'
));
