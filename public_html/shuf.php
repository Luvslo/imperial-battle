<?
include_once('global.inc.php');

if ($_GET['haha'] == 'zrozro') {
	$sql = "
	SELECT $table[players].id, $table[players].username,
			$table[players].galaxy_id, $table[galaxy].private
	FROM 	$table[players]
		INNER JOIN $table[galaxy] ON $table[players].galaxy_id = $table[galaxy].id
	WHERE 	$table[galaxy].private = 0
	";
	echo $sql;
} else { die(); }
?>