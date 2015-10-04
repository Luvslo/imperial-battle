<?
$INC_PATH = "/home/imperial/domains/game.imperial-battle.com/private_html/includes_01/";

require($INC_PATH.'classes/class.database.php');
require($INC_PATH.'classes/class.user.php');
require($INC_PATH.'classes/class.battleenginecollection.php');
session_start();

require($INC_PATH.'config.inc.php');
require($INC_PATH.'functions.inc.php');

/* New database object */
$db = new Database;
$db->setIP($dbconf['ip']);
$db->setPort($dbconf['port']);
$db->setUsername($dbconf['username']);
$db->setPassword($dbconf['password']);
$db->setDatabase($dbconf['database']);
$db->connect();
$db->selectDatabase();

/* New user object */
if ((!$user) && (!$_SESSION['user'])) {
	$user = new User($table['players'], 'id', 'username', 'password', $db);
	$_SESSION['user'] = $user;
}
if (($_SESSION['user']) && (!$user)) {
	$user = $_SESSION['user'];
}
?>