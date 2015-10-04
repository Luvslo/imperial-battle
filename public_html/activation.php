<?
require_once('global.inc.php');

$code = secureData($_GET['code']);
$sql_doactivate = "UPDATE `$table[players]` SET `activated` = '1' WHERE `activation_code` = '$code'";
mysql_query($sql_doactivate) or die(mysql_error());
?>
<html>

<head>
<meta http-equiv="Content-Language" content="nl">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link href="css/default.css" rel="stylesheet" type="text/css">
<title>Imperial-Battle | Account activation</title>
</head>

<body bgcolor="#223137">
<p align="center">Your account has been activated.</p>
<p align="center">Click <a href="index.php" target="_top"><b>here</b></a> to login.</p>
</body>

</html>