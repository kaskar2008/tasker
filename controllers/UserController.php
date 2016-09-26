<?php

class UserController {

	/**
	 * Find user with email.
	 *
	 * @param  string  $email
	 * @return array or null
	 */
	static function find($email) {
		$sql = "SELECT id,name,email FROM users WHERE email='$email'";
		$res = DB::query($sql, true);
		return count($res) ? $res[0] : null;
	}

	/**
	 * Show login form.
	 */
	static function loginShow() {
	    render("login");
	}

	/**
	 * Login user to the system.
	 *
	 */
	static function loginDo() {
		// Check
		$email = $_POST["email"] ? real_escape_string($_POST["email"]) : null;
		$password = $_POST["password"] ? salt_password($_POST["password"]) : null;

		if($email == "" || $password == "") {
			$errors = new stdClass();
			if($email == "") $errors->email = "Поле не должно быть пустым";
			if($password == "") $errors->password = "Поле не должно быть пустым";
			return render("login", ["errors" => $errors]);
		} 

		$sql = "SELECT password FROM users WHERE email='$email'";
		$dbresult = DB::query($sql, true);
		if(!count($dbresult)) {
			$errors = new stdClass();
			$errors->email = "Пользователь не найден";
			return render("login", ["errors" => $errors]);
		}
		$dbpassword = $dbresult[0]["password"];
		
		if($dbpassword != $password) {
			$errors = new stdClass();
			$errors->password = "Пароль неправильный";
			return render("login", ["errors" => $errors]);
		}

		$user = self::find($email);
		Auth::accept($user, true);

		return redirect("/");
	}

	/**
	 * Login user to the system.
	 *
	 */
	static function logout() {
		$_SESSION["sessid"] = null;

		return redirect("/");
	}

	/**
	 * Show register form.
	 */
	static function registerShow() {
		render("register");
	}

	/**
	 * Register user in the system.
	 *
	 */
	static function registerDo() {
		// Check
		$fields = array(
			"email" => $_POST["email"] ? real_escape_string($_POST["email"]) : null,
			"password" => $_POST["password"] ? salt_password($_POST["password"]) : null,
			"name" => $_POST["name"] ? real_escape_string($_POST["name"]) : null
		);

		if($fields["email"] == "" || $fields["password"] == "" || $fields["name"] == "") {
			$errors = new stdClass();
			if($fields["email"] == "") $errors->email = "Поле не должно быть пустым";
			if($fields["password"] == "") $errors->password = "Поле не должно быть пустым";
			if($fields["name"] == "") $errors->name = "Поле не должно быть пустым";
			return render("register", ["errors" => $errors]);
		}

		if(isForbiddenChars($fields["email"].$fields["name"])) {
			$errors = new stdClass();
			foreach ($fields as $key=>$value) {
				if(isForbiddenChars($value)) $errors->$key = "Поле содержит вредные символы";
			}
			return render("register", ["errors" => $errors]);
		}

		$user = self::find($fields["email"]);
		if(!$user) {
			$created_at = time();
			$sql = "INSERT INTO users (name, email, password, created_at) VALUES ('{$fields["name"]}', '{$fields["email"]}', '{$fields["password"]}', $created_at)";
			$dbresult = DB::query($sql);
			$user = self::find($fields["email"]);
			Auth::accept($user, true);
			return redirect("/");
		} else {
			$errors = new stdClass();
			$errors->email = "Email уже используется";
			return render("register", ["errors" => $errors]);
		}
	}

}