<?php
class DB {
	static $connection;

	/**
     * Connect to the database.
     */
	static function connect() {
		self::$connection = mysqli_connect(_CONF_MYSQL["host"], _CONF_MYSQL["username"], _CONF_MYSQL["password"], _CONF_MYSQL["database"]);
	}

	/**
     * Make a query to the database.
     * If you want to get the assoc array - use isArray param in TRUE.
     *
     * @param  string  $sql
     * @param  boolean  $isArray
     * @return mixed
     */
	static function query($sql, $isArray = false) {
		echo "<p>$sql</p>";
		$dbresult = mysqli_query(self::$connection, $sql);
		if($isArray) return $dbresult ? mysqli_fetch_all($dbresult, MYSQLI_ASSOC) : $dbresult;
		return $dbresult;
	}

	/**
     * Get the last id from request.
     */
	static function last_id() {
		return mysqli_insert_id(self::$connection);
	}
}
?>