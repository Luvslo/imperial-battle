<?	
/*
* User engine v0.7b
* Rogier van Dam (rogier@pureworx.nl)
*
* Class to handle users for login system.
* We use Database.php to do the database work.
*/

//require('Database.class.php');


class User {

	var $table;					/* Table to read data from. */
	var $f_id;					/* Field name for the user ID. */
	var $f_username;			/* Field name for the username. */
	var $f_password;			/* Field name for the password. */
	var $init = FALSE;			/* Are we initialised? */
	var $db;

	var $username	= NULL;		/* Username of the object. */
	var $password	= NULL;		/* Password of the object. (Prefered encrypted). */
	var $uid		= NULL;		/* UserID of the object. */
	var $session_id = NULL;		/* Session ID of the user. */
	var $db_object	= NULL;		/* Database object. */

	function User($table, $f_id, $f_username, $f_password, $db_object) {
		$this->table = $table;
		$this->f_id = $f_id;
		$this->f_username = $f_username;
		$this->f_password = $f_password;
		$this->db_object = $db_object;
	}

	function initialise($table, $f_id, $f_username, $f_password) {
		if ($table && $id && $username && $password) {
			$t = $this->setTable($table);
			$i = $this->setFieldId($f_id);
			$u = $this->setFieldUsername($f_username);
			$p = $this->setFieldPassword($f_password);
			if ($t && $i && $u && $p) {
				$this->init = TRUE;
				return $this->init;
			}
		}
	}

	function __wakeup() {
		$this->db_object->connect();
	}
	function setTable($table) {
		$this->table = $table;
		return $this->table;
	}
	function setFieldUsername($username) {
		$this->f_username = $username;
		return $this->f_username;
	}
	function setFieldPassword($password) {
		$this->f_password = $password;
		return $this->f_password;
	}
	function setFieldId($id) {
		$this->f_id = $id;
		return $this->f_id;
	}
	function setUsername($username) {
		$this->username = $username;
		return $this->username;
	}
	function getUsername() {
		return $this->username;
	}
	function setPassword($password) {
		$this->password = $password;
		return $this->password;
	}
	function getPassword() {
		return $this->password;
	}
	function setUid($uid) {
		$this->uid = $uid;
		return $this->uid;
	}
	function getUid() {
		return $this->uid;
	}
	function setSessionId($session_id) {
		$this->session_id = $session_id;
		return $this->session_id;
	}
	function getSessionId() {
		return $this->session_id;
	}
	function createSession() {
		if (!session_id()) {
			session_start();
		}
		$_SESSION['login']		= 1;
		$_SESSION['uid']		= $this->getUid();
		$_SESSION['username']	= $this->getUsername();
		$_SESSION['password']	= $this->getPassword();
		$this->setSessionId(session_id());
	}
	function destroySession() {
		$_SESSION['login']		= NULL;
		$_SESSION['uid']		= NULL;
		$_SESSION['username']	= NULL;
		$_SESSION['password']	= NULL;
		session_destroy();
		$this->setSessionId(session_regenerate_id());
	}
	function loginUser($username, $password) {
		if ($username && $password) {
			$sql_login = "SELECT `" . $this->f_id . "`, `" . $this->f_username . "`, `" . $this->f_password . "`, `activated` FROM `" . $this->table .  "` WHERE `" . $this->f_username . "` = '" . $username . "' AND `" . $this->f_password . "` = '" . $password . "'";
		
			$id = $this->db_object->doQuery($sql_login);
			if ($this->db_object->getNumResults() != 0) {
				$rec = $this->db_object->fetchArray($id);			/* Username/password match the database. */
				$this->setUid($rec['id']);
				$this->setUsername($rec['username']);
				$this->setPassword($rec['password']);
				$this->createSession();
				return TRUE;
			}
			else {
				return FALSE;								/* Username/password do not match the database. */
			}
		}
	}
	function logoutUser() {
		$this->destroySession();
		$this->setUid(NULL);
		$this->setUsername(NULL);
		$this->setPassword(NULL);
		return true;
	}
	function checkLogin() {
		$status = FALSE;
		if (!$this->username && !$this->password) {
			return $status;
		}
		$sql_login = "SELECT `" . $this->f_id . "`, `" . $this->f_username . "`, `" . $this->f_password . "` FROM `" . $this->table .  "` WHERE `" . $this->f_username . "` = '" . $this->getUsername() . "' AND `" . $this->f_password . "` = '" . $this->getPassword() . "'";
		$id = $this->db_object->doQuery($sql_login);
		if (($this->db_object->getNumResults() != 0) &&
		($this->getSessionId() == session_id()) &&
		($_SESSION['login'] == 1)) {
			$status = TRUE;
		}
		return $status;
	}
}
?>