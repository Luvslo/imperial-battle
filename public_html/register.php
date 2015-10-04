<?
require_once("global.inc.php");
unset ($user, $_SESSION['user']);
if ($_POST['submit']) {
	$secretcode = '3485ghfgh98ghfdghq31qqqzxfjkdfgddkjwer08448534gdfgbdfg'; /* Used for activation code generation */
	
	$username = secureData($_POST['username']);
	$rulername = secureData($_POST['rulername']);
	$planetname = secureData($_POST['planetname']);
	$password = secureData($_POST['password']);
	$password2 = secureData($_POST['password2']);
	$email = secureData($_POST['email']);
	$email2 = secureData($_POST['email2']);

	if (getIdByUsername($username)) { $msg = '<font color=red>That username is already taken.</font>'; }
	elseif (getIdByRulername($rulername)) { $msg = '<font color=red>That rulername is already taken.</font>'; }
	elseif (getIdByPlanetname($planetname)) { $msg = '<font color=red>That planetname is already taken.</font>'; }
	elseif (getIdByEmail($email)) { $msg = '<font color=red>The email address you\'re trying to use, is already taken.</font>'; }
	elseif ($password != $password2) { $msg = '<font color=red>The passwords don\'t match!</font>'; }
	elseif ($email != $email2) { $msg = '<font color=red>The e-mails don\'t match!</font>'; }
	elseif (!$username || !$planetname || !$password || !$password2 || !$email) { $msg = '<font color=red>Empty fields are not allowed.</font>'; }
	else {
		$activation_code = md5($username.time().$email.$secretcode); /* Generate a unique md5 has by using the username, current time, email address and a private code. */
		$galaxy_id = getRandomGalaxyId();
		if (getFreeGalaxySpot($galaxy_id)) { $galaxy_spot = getFreeGalaxySpot($galaxy_id); }
		else { $msg = 'Registration failed. Your data was inserted correctly, but the galaxy spot is not right. Contact the crew'; }
		$password = md5($password);
		$sql_newplayer = "INSERT INTO `$table[players]` (`username` , `password` , `email` , `activated` , `activation_code` , `rulername`,`planetname` , `galaxy_id` , `galaxy_spot`)
							VALUES ('$username', '$password', '$email', '0', '$activation_code', '$rulername','$planetname', '$galaxy_id', '$galaxy_spot')";
		mysql_query($sql_newplayer) or die(mysql_error());

		$msg = '<font color=green>Thank you for registration. You will be redirected to the login page in 3 seconds.<br>You can not login yet, please check your email inbox for the activation e-mail.</font>';

		$mail_subject = 'Welcome to Imperial-Battle!';
		$mail_body = '<p><font face="Verdana">Hello '.$username.'!</font></p>
						<p><font face="Verdana">Thank you for signup on this new great game!</font></p>
						<p><font face="Verdana">Your username is: '.$username.'<br>
						Your password is: '.$password2.'<br>
						<br>
						To activate your account, follow this link: 
						<a href="http://game.imperial-battle.com/game/activation.php?code='.$activation_code.'">http://game.imperial-battle.com/game/activation.php?code='.$activation_code.'</a></font></p>
						<p><font face="Verdana">After activating your account, you can login at:
						<a href="http://www.imperial-battle.com/">
						http://www.imperial-battle.com/</a></font></p>
						<p><font face="Verdana">Good luck!<br>
						<br>
						The Imperial-Battle team.</font></p>';
		$mail_headers = "";
		//$mail_headers .= "X-Sender:  noreply@imperial-battle.com <noreply@imperial-battle.com >\n"; //
		$mail_headers .= "From: Imperial-Battle registrations  <registrations@imperial-battle.com >\n";
		//$mail_headers .= "Reply-To: noreply@imperial-battle.com  <noreply@imperial-battle.com >\n";
		$mail_headers .= "Date: ".date("r")."\n";
		$mail_headers .= "Message-ID: <".date("YmdHis")."unknown@".$_SERVER['SERVER_NAME'].">\n";
		//$mail_headers .= "Subject: $mail_subject\n"; // subject write here
		//$mail_headers .= "Return-Path: noreply@imperial-battle.com  <noreply@imperial-battle.com >\n";
		$mail_headers .= "Delivered-to: $email <$email>\n";
		$mail_headers .= "MIME-Version: 1.0\n";
		$mail_headers .= "Content-type: text/html;charset=ISO-8859-9\n";
		$mail_headers .= "X-Priority: 1\n";
		//$mail_headers .= "Importance: High\n";
		//$mail_headers .= "X-MSMail-Priority: High\n";
		$mail_headers .= "X-Mailer: Imperial-Battle registration mailer\n";

		mail($email, $mail_subject, $mail_body, $mail_headers);
		echo '<META HTTP-EQUIV="Refresh" CONTENT="3.0;URL=index.php">';
	}

}
?>


<html>
<head>
<meta http-equiv="Content-Language" content="nl">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Imperial-Battle | Account registration</title>
<link href="css/default.css" rel="stylesheet" type="text/css">
<base target="_self">
</head>

<body bgcolor="#223137" background="img/main_bg.gif" style="background-repeat: no-repeat">
<center>
<? if ($msg) { echo $msg; } ?>
<br><br><br>
Welcome to Imperial-Battle.<br>
You can register here.<br>Please remember to use a valid email address, so you can activate your account!<br><br>
<form method="POST" action="register.php">
<table border="0" width="800">
	<tr>
		<td width="400" align="right">Username:</td>
		<td width="400"><input type="text" name="username" size="20" value="<?echo $username;?>"> (used for login)</td>
	</tr>	
	<tr>
		<td align="right">Rulername:</td>
		<td><input type="text" name="rulername" size="20" value="<?echo $planetname;?>"> (used for ingame display)</td>
	</tr>
	<tr>
		<td align="right">Planetname:</td>
		<td><input type="text" name="planetname" size="20" value="<?echo $planetname;?>"> (used for ingame display)</td>
	</tr>	
	<tr>
		<td align="right">Password:</td>
		<td><input type="password" name="password" size="20"></td>
	</tr>	
	<tr>
		<td align="right">Confirm password:</td>
		<td><input type="password" name="password2" size="20"></td>
	</tr>
	<tr>
		<td align="right">Valid email address:</td>
		<td><input type="text" name="email" size="20" value="<?echo $email;?>"></td>
	</tr>
	<tr>
		<td align="right">Confirm email address:</td>
		<td><input type="text" name="email2" size="20" value="<?echo $email2;?>"></td>
	</tr>
	<tr height="15">
		<td colspan="2" align="center">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" value="  Register my account!  " name="submit"></td>
	</tr>
</table>

</form>
</center>		
</body>
</html>