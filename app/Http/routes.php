<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$api 							= app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) 
{
    $api->group(['namespace' => 'App\Http\Controllers'], function ($api) 
	{
		$api->get('/schedules',
			[
				'uses'				=> 'ScheduleController@index',
				// 'middleware'		=> 'jwt|company:read-purchase.order',
			]
		);

		$api->post('/schedules',
			[
				'uses'				=> 'ScheduleController@post',
				// 'middleware'		=> 'jwt|company:store-purchase.order',
			]
		);

		$api->delete('/schedules',
			[
				'uses'				=> 'ScheduleController@delete',
				// 'middleware'		=> 'jwt|company:delete-purchase.order',
			]
		);
	});
});
