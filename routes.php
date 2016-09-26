<?php

/*
Methods: get, post, put, delete
Template: 
	Routes::get(string $uri, mixed $callback, mixed $middleware);
		$uri -	can be a string only with pattern in "{}"
			each pattern represents an item in assoc array, which will
			be sent to the callback
		$callback - can be a string with a format CLASSNAME@METHOD_NAME
			can be a function
		$middleware - can be a string with a format CLASSNAME@METHOD_NAME
			can be a function
			For a method, which will be called before callback
*/

Routes::get('/', 'TaskController@index', 'Auth@denyGuest');
Routes::get('/logout', 'UserController@logout');
Routes::get('/login', 'UserController@loginShow');
Routes::post('/login', 'UserController@loginDo');
Routes::get('/register', 'UserController@registerShow');
Routes::post('/register', 'UserController@registerDo');

Routes::get('/api/tasks/{id}', function($params) {
	echo "TaskID: ".$params["id"];
});

Routes::get('/api/tasks', 'TaskController@tasks', 'Auth@denyGuest');
Routes::put('/api/tasks/{id}', 'TaskController@save', 'Auth@denyGuest');
Routes::put('/api/tasks/{id}/state', 'TaskController@changeState', 'Auth@denyGuest');
Routes::post('/api/tasks', 'TaskController@add', 'Auth@denyGuest');
Routes::delete('/api/tasks', 'TaskController@delete', 'Auth@denyGuest');

Routes::deal();
?>