<?
$sql_allships = "SELECT `id`, `name`, `firepower`, `armor`, `accurate` FROM `$table[ships]` ORDER BY `initiative`";
$res_allships = mysql_query($sql_allships);

$shipdata = array();
while ($rec_allships = mysql_fetch_assoc($res_allships)) {
	array_push($shipdata, $rec_allships);
}

?>