<?
require_once("global.inc.php");

if ($_POST['login']) {
	$username = addslashes($_POST['username']);
	$password = md5($_POST['password']);
	if ($user->loginUser($username, $password)) {
		$userid = $user->getUid();
		$sql_checkact = "SELECT `id`, `activated` FROM $table[players] WHERE `id` = '$userid'";
		$rec_checkact = mysql_fetch_array(mysql_query($sql_checkact));
		if ($rec_checkact['activated'] > 0) {
			$dispUsername = $user->getUsername();
			echo "<p align=\"center\">$dispUsername logged in succesfull.<br>Redirecting to the game in 1 second.<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=index.php\"><br> \n";
			$_SESSION['user'] = $user;
		}
		else {
			$user->logoutUser();
			echo "<p align=\"center\"><span class=\"error\"><b>Login failed.</b><br>Your account is not activated yet.<br>Check your email INBOX for activation instructions.</span></p>\n";
		}
	}
	else {
		echo "<p align=\"center\"><span class=\"error\"><b>Login failed.</b><br>Your username or password is incorrect.</span></p><META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=http://www.imperial-battle.com/\">\n";
	}
}
?>