<?php
class Auth {
	private static $user = null;

	/**
	 * Check if user logged in.
	 */
	static function check() {
		$sessid = empty($_SESSION["sessid"]) ? null : $_SESSION["sessid"];
		if($sessid != null) {
			$sql = "SELECT id,name,email FROM users WHERE session='$sessid'";
			$dbresult = DB::query($sql, true);
			if(count($dbresult)) {
				self::accept($dbresult[0]);
				return true;
			}
			else {
				return false;
			}
		} else {
			return false;
		}
	}

	static function user() {
		return self::$user;
	}

	/**
	 * Let user get the session.
	 */
	static function accept($fields, $isLogin = false) {
		if($isLogin) {
			$sessid = md5($fields["email"]._TASKER_SALT);
			$sql = "UPDATE users SET session = '$sessid' WHERE email='{$fields["email"]}'";
			$dbresult = DB::query($sql);
			$_SESSION["sessid"] = $sessid;
		}
		$user = new stdClass();
		foreach ($fields as $key => $value) {
            $user->$key = $value;
        }
		self::$user = $user;
	}

	/**
	 * Prevent current page from showing to guests.
	 * For middleware reasons.
	 */
	static function denyGuest() {
		if(!self::user()) {
			render("login");
			exit;
		}
	}
}
?>