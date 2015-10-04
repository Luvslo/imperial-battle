<?
require_once('global.inc.php');
?>
<html>
<head>
<meta http-equiv="Content-Language" content="nl">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Imperial-Battle | Login screen</title>
<link href="css/default.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#223137" background="img/main_bg.gif" style="background-repeat: no-repeat" onload="document.forms(0).username.focus();">
<p align="center">You're about to enter a new game!<br>
If you do not have an account yet, you can register at the <a href="register.php" target="_top"><b>register page</b></a>.<br>

<form method="POST" action="index.php?mod=login">
	<p align="center"><font face="Tahoma">Username:
	<input type="text" name="username" size="20"><br>
	Password: <input type="password" name="password" size="20"><br>
	<br>
	<input type="submit" value="Login" name="login"></font></p>
</form>

<?
If ($_POST['login']) {
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
		echo "<p align=\"center\"><span class=\"error\"><b>Login failed.</b><br>Your username or password is incorrect.</span></p>\n";
	}
}
?>