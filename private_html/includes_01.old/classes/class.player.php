<?

class Player {
	
	var $_id		= null;
	var $_username	= null;
	var $_password	= null;
	var $_planetid	= null;
	
	function Player($id, $username, $password) {
		$this->_id = $id;
		$this->_username = $username;
		$this->_password = $password;
	}
	
	function loginPlayer($username, $password) {
		$user->loginUser($username, $passowrd);
	}
	
	function getPlanetId($playerid) {
		if (!$playerid) { $playerid = $this->_id; }
		// Get Planet ID
	}
	
}
	
?>