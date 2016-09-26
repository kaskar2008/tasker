<?php
class Routes {
	private static $routes = array();

	/**
	 * Compare all routes with current URI.
	 *
	 */
	private static function check() {
		$ROUTE = explode('?', $_SERVER["REQUEST_URI"])[0];
		$METHOD = $_SERVER["REQUEST_METHOD"];
		$isMatch = false;
		foreach (self::$routes as $key=>$item) {
			$item_method = $item["method"];
			$item_uri = $item["uri"];
			$callback = $item["callback"];
			$middleware = $item["middleware"];
			$pattern = '/{([^}]+)}/i';
			$replacement = '(?P<$1>[a-z0-9]+)';
			$regex = '#^'.preg_replace($pattern, $replacement, $item_uri).'/?$#i';
			if (preg_match($regex, $ROUTE, $matches) && $item_method == $METHOD) {
				if($middleware != null) {
					if(is_callable($middleware)) {
						$middleware();
					}
					else {
						if(is_string($middleware)) {
							$exploded_method = explode('@', $middleware);
							$class_name = $exploded_method[0];
							$method_name = $exploded_method[1];
							if(class_exists($class_name)) {
								if(method_exists($class_name, $method_name)) {
									$class_name::$method_name();
								}
							}
						}
					}
				}

				$isMatch = true;
				$uri_params = array_filter(
					$matches,
					function ($key) {
						return !is_numeric($key);
					},
					ARRAY_FILTER_USE_KEY
				);

				if(is_callable($callback)) {
					$callback($uri_params);
				}
				else {
					if(is_string($callback)) {
						$exploded_method = explode('@', $callback);
						$class_name = $exploded_method[0];
						$method_name = $exploded_method[1];
						if(class_exists($class_name)) {
							if(method_exists($class_name, $method_name)) {
								$class_name::$method_name($uri_params);
							}
						}
					}
				}
				break;
			}
		}
		if(!$isMatch) render("404");	
	}

	/**
	 * Set the GET route.
	 *
	 * @param  string  $uri
	 * @param  mixed  $callback
	 * @param  mixed  $middleware
	 *
	 */
	static function get($uri, $callback, $middleware = null) {
		$item = array(
			"uri" => $uri,
			"method" => "GET",
			"callback" => $callback,
			"middleware" => $middleware
		);
		self::$routes[] = $item;
	}

	/**
	 * Set the POST route.
	 *
	 * @param  string  $uri
	 * @param  mixed  $callback
	 * @param  mixed  $middleware
	 *
	 */
	static function post($uri, $callback, $middleware = null) {
		$item = array(
			"uri" => $uri,
			"method" => "POST",
			"callback" => $callback,
			"middleware" => $middleware
		);
		self::$routes[] = $item;
	}

	/**
	 * Set the PUT route.
	 *
	 * @param  string  $uri
	 * @param  mixed  $callback
	 * @param  mixed  $middleware
	 *
	 */
	static function put($uri, $callback, $middleware = null) {
		$item = array(
			"uri" => $uri,
			"method" => "PUT",
			"callback" => $callback,
			"middleware" => $middleware
		);
		self::$routes[] = $item;
	}

	/**
	 * Set the DELETE route.
	 *
	 * @param  string  $uri
	 * @param  mixed  $callback
	 * @param  mixed  $middleware
	 *
	 */
	static function delete($uri, $callback, $middleware = null) {
		$item = array(
			"uri" => $uri,
			"method" => "DELETE",
			"callback" => $callback,
			"middleware" => $middleware
		);
		self::$routes[] = $item;
	}

	/**
	 * Entry point to all of the routes.
	 * Must be called after all the routes are declared
	 *
	 */
	static function deal() {
		self::check();
	}
}
?>