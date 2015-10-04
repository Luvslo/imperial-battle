<?
/*
* Database engine v0.4b
* Rogier van Dam (rogier@pureworx.nl)
*
* Class to do communication with the database
*/

class Database {

	var $_ip;
	var $_port;
	var $_username;
	var $_password;
	var $_database;

	var $link_id	= 0;
	var $query_id	= 0;
	var $last_query = 0;
	var $num_results= 0;
	var $record 	= array();

	/* Set default values to prevent problems */
	function Database() {
		$this->setIP('127.0.0.1');
		$this->setPort(3306);
		$this->setUsername('root');
		$this->setPassword('');
		$this->setDatabase('');
	}
	/* Initialise all */
	function initialise($ip, $port, $username, $password, $database, $con, $select) {
		if ($ip && $port && $username && $password && $database) {
			$i = $this->setIP($ip);
			$p = $this->setPort($port);
			$u = $this->setUsername($username);
			$p = $this->setPassword($password);
			$d = $this->setDatabase($database);
			if ($con)
			$this->connect();
			if ($select)
			$this->selectDatabase();
		}
	}
	/* Method to set IP address */
	function setIP($ip) {
		$this->_ip = $ip;
		return $this->_ip;
	}

	/* Method to set port number */
	function setPort($port) {
		$this->_port = $port;
		return $this->_port;
	}

	/* Method to set username */
	function setUsername($username) {
		$this->_username = $username;
		return $this->_username;
	}

	/* Method to set password */
	function setPassword($password) {
		$this->_password = $password;
		return $this->_password;
	}

	/* Method to set database where we work in  */
	function setDatabase($database) {
		$this->_database = $database;
		return $this->_database;
	}

	/* Method to connect to the MySQL server */
	function connect() {
		$this->link_id = mysql_connect($this->_ip, $this->_username, $this->_password);
		if (!$this->link_id) {
			return FALSE;
		}
		else {
			return $this->link_id;
		}
	}

	/* Method to select the database we want to work in */
	function selectDatabase() {
		if ($this->link_id) {
			$verification = mysql_select_db($this->_database);
			if (!$verification) {
				return FALSE;
			}
			else {
				return TRUE;
			}
		}
		else {
			return FALSE;
		}
	}

	/* Executes query. */
	function doQuery($query) {
		$this->last_query = $query;
		if ($this->link_id) {
			$this->query_id = mysql_query($query);
			if (@mysql_num_rows($this->query_id)) {
				$this->num_results = mysql_num_rows($this->query_id);
			}
			$this->record = $this->fetchArray($this->query_id);
			if (!$this->query_id) {
				return FALSE;
			}
			else {
				return $this->query_id;
			}
		}
	}

	/* Executes query and returns first row as result. */
	function doQueryFirst($query) {
		if ($this->link_id) {
			$this->query_id = $this->doQuery($query);
			if ($result = $this->fetchArray())
			return $result;
			else
			return FALSE;
		}
		else {
			return FALSE;
		}
	}

	/* Returns lasted executed query */
	function getQuery() {
		return $this->last_query;
	}

	/* Returns the results in an array */
	function fetchArray($query_id) {
		if (!$query_id) {
			$query_id = $this->query_id;
		}
		if (!$this->record) {
			$this->record = mysql_fetch_array($query_id);
		}
		return $this->record;
	}

	function getFetchArray() {
		return $this->record;
	}

	function numResults($query_id) {
		if (!$query_id) {
			$query_id = $this->query_id;
		}
		$this->num_results = mysql_num_rows($query_id);

		return $this->num_results;
	}

	function getNumResults() {
		return $this->num_results;
	}
}
