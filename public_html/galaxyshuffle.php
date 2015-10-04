<?
die('don\'t!');

require('includes/global.inc.php');

if ($_GET['a'] == 'rogier') {
	$sql = "SELECT `id234234sdfsdfwer234234rtt5ete4t6w435r445`, `username` FROM $table[players] ORDER BY id";
	$res = mysql_query($sql);
	while ($rec = mysql_fetch_array($res)) {
		$galaxy_id = getRandomGalaxyId();
		$galaxy_spot = getFreeGalaxySpot($galaxy_id);
		$sql_update = "UPDATE $table[players] SET `galaxy_id` = '$galaxy_id', `galaxy_spot` = '$galaxy_spot' WHERE `id` = '$rec[id]'";
		mysql_query($sql_update) or die(mysql_query());
		echo $sql_update.'<br>';
	}
} else {
	echo 'hah, no!';
}
?>