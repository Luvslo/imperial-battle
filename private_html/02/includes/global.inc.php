<?
require('includes/classes/class.database.php');
require('includes/classes/class.user.php');
require('includes/classes/class.battleenginecollection.php');
session_start();

require('includes/config.inc.php');
require('includes/functions.inc.php');
require('includes/functions.forum.inc.php');
require('includes/functions.fleet.inc.php');

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

define('IB_TICK_CURRENT', getCurrentTick());
define('IB_TICK_LAST', getLastTick());
?>