<?
$path = '/home/imperial/domains/game.imperial-battle.com/private_html/02';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once('includes/global.inc.php');

debug('Running daily check. Time now is: '.date("H:i d-m-Y", time()));
debug('');
debug('- Checking for alliances with 0 or less(?) members.');
$sql_findemptyalliances = "SELECT
								$table[alliance].id as alliance_id,
								COUNT($table[players].id) AS num
								FROM $table[alliance]
								LEFT JOIN $table[players] ON $table[alliance].id = $table[players].alliance_id
								GROUP BY alliance_id
								ORDER BY alliance_id ASC";
$res_findemptyalliances = mysql_query($sql_findemptyalliances) or die(mysql_error());
$num_findemptyalliances = @mysql_num_rows($res_findemptyalliances);
if ($num_findemptyalliances > 0) {
	while ($rec = mysql_fetch_assoc($res_findemptyalliances)) {
		if ($rec['num'] <= 0) {
			$sql_deletealliance = "DELETE FROM $table[alliance] WHERE `id` = '$rec[alliance_id]'";
			mysql_query($sql_deletealliance) or die(mysql_error());
			debug('- - Deleting alliance with id '.$rec['alliance_id'].' because they have zero members');
		}
	}
}

debug('- Checking for playernews older then 4 days (345600 seconds).');
$time = time();
$oldtime = $time - 345600;
$sql_delplayernews = "DELETE FROM $table[playernews] WHERE `date` < '$oldtime'";
mysql_query($sql_delplayernews);
debug('- - The affected news messages amount is: '.mysql_affected_rows());
?>