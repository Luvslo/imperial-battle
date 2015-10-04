<?
require_once('global.inc.php');

if (!isset($mod)) { $mod = secureData($_GET['mod']); }
if (!isset($mod)) { $mod = secureData($_POST['mod']); }

if (!isset($act)) { $act = secureData($_GET['act']); }
if (!isset($act)) { $act = secureData($_POST['act']); }
?>
<html>
<head>
<meta http-equiv="Content-Language" content="nl">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link href="css/default.css" rel="stylesheet" type="text/css">
<title>Imperial Battle</title>
</head>

<body bgcolor="#223137" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bottommargin="0">
<?
if (!$user->checkLogin()) {
	//header('Location: login.php');
	include('login.php');
?>
	<!-- <META HTTP-EQUIV="Refresh" CONTENT="0;URL=http://www.imperial-battle.com/"> -->
<?
}
else {
?>
<META HTTP-EQUIV="Refresh" CONTENT="0.1;URL=index2.php">
<?
}
?>
</body>
</html>