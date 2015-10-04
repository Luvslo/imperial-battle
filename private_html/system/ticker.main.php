<?
function getmicrotime() {
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}
$time_start = getmicrotime();

$path = '/home/imperial/domains/game.imperial-battle.com/private_html/02/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once('includes/global.inc.php');

/* Main ticker file */

$current = getCurrentTick();
$last = getLastTick();
$current++;

if (($current <= $last) && (time() >= getTickStartTime())) {
	//echo 'Processing '.$current.'<br>';

	putCurrentTick($current);

	$sql_playerids = "SELECT `id` FROM $table[players] ORDER BY `id`";
	$rec_playerids = mysql_query($sql_playerids);

	include('ticker.prefleetcheck.php');

	while ($p = mysql_fetch_array($rec_playerids)) {
		$time_end = getmicrotime();
		$time = $time_end - $time_start;

		$id = $p['id'];
		$sql_playerdata = "SELECT `id`, `username`, `password`, `email`, `activated`, `activation_code`, `rulername`, `planetname`, `lastlogin`, `galaxy_id`, `galaxy_spot`, `alliance_id`, `res_steel`, `res_crystal`, `res_erbium`, `res_titanium`, `roid_steel`, `roid_crystal`, `roid_erbium`, `roid_unused`, `score` FROM $table[players] WHERE `id` = '$id'";
		$res_playerdata = mysql_query($sql_playerdata);
		$new = mysql_fetch_array($res_playerdata);
		unset($p);
		$p = array();
		$p = $new;

		checkProductions($id, $current);
		if ($p['activated'] == 1) {
			$factory_id = getTitaniumFactoryId($p['id']);
			$sql_factorydata = "SELECT `steel_investment`, `crystal_investment`, `erbium_investment` FROM $table[titanium_factory] WHERE `id` = '$factory_id'";
			$res_factorydata = mysql_query($sql_factorydata);
			$num_factorydata = mysql_num_rows($res_factorydata);
			if ($num_factorydata > 0) {
				$rec_factorydata = mysql_fetch_array($res_factorydata);
				$steel_investment = $rec_factorydata['steel_investment'];
				$crystal_investment = $rec_factorydata['crystal_investment'];
				$erbium_investment = $rec_factorydata['erbium_investment'];
			} else {
				$steel_investment = 0;
				$crystal_investment = 0;
				$erbium_investment = 0;
			}

			$steel_res['planet'] = 0;
			$steel_res['factory_prod'] = 0;
			$steel_res['asteroids'] = 0;
			if (checkItem($id, $STEEL_ADV_REF)) {
				$steel_res['planet'] = $RES_FROM_ADV_REF;
				$steel_res['asteroids'] = $p['roid_steel'] * $RES_FROM_STEEL_ROID;
			} elseif (checkItem($id, $STEEL_REFINERY)) {
				$steel_res['planet'] = $RES_FROM_REFINERY;
			}

			$crystal_res['planet'] = 0;
			$crystal_res['factory_prod'] = 0;
			$crystal_res['asteroids'] = 0;
			if (checkItem($id, $CRYSTAL_ADV_REF)) {
				$crystal_res['planet'] = $RES_FROM_ADV_REF;
				$crystal_res['asteroids'] = $p['roid_crystal'] * $RES_FROM_CRYSTAL_ROID;
			} elseif (checkItem($id, $CRYSTAL_REFINERY)) {
				$crystal_res['planet'] = $RES_FROM_REFINERY;
			}

			$erbium_res['planet'] = 0;
			$erbium_res['factory_prod'] = 0;
			$erbium_res['asteroids'] = 0;
			if (checkItem($id, $ERBIUM_ADV_REF)) {
				$erbium_res['planet'] = $RES_FROM_ADV_REF;
				$erbium_res['asteroids'] = $p['roid_erbium'] * $RES_FROM_ERBIUM_ROID;
			} elseif (checkItem($id, $ERBIUM_REFINERY)) {
				$erbium_res['planet'] = $RES_FROM_REFINERY;
			}

			if ($factory_id > 0) {
				if (($steel_investment == 0) || ($crystal_investment == 0) || ($erbium_investment == 0)) {
					$steel_res['factory_prod'] = 0;
					$crystal_res['factory_prod'] = 0;
					$erbium_res['factory_prod'] = 0;
					$titanium_production = 0;
				} else {
					$steel_res['factory_prod'] = (($steel_res['asteroids'] + $steel_res['planet'])/100)*$steel_investment;
					$crystal_res['factory_prod'] = (($crystal_res['asteroids'] + $crystal_res['planet'])/100)*$crystal_investment;
					$erbium_res['factory_prod'] = (($erbium_res['asteroids'] + $erbium_res['planet'])/100)*$erbium_investment;
					$total_cost = $steel_res['factory_prod'] + $crystal_res['factory_prod'] + $erbium_res['factory_prod'];
					$titanium_production = ($total_cost / 3.14879);
				}
			} else {
				$titanium_production = 0;
			}
			$new['res_steel'] += ($steel_res['asteroids'] + $steel_res['planet']) - $steel_res['factory_prod'];
			$new['res_crystal'] += ($crystal_res['asteroids'] + $crystal_res['planet']) - $crystal_res['factory_prod'];
			$new['res_erbium'] += ($erbium_res['asteroids'] + $erbium_res['planet']) - $erbium_res['factory_prod'];
			$new['res_titanium'] += ceil($titanium_production);

		}
		$stock_resource = $p['res_steel'] + $p['res_crystal'] + $p['res_erbium'] + $p['res_titanium'];
		$item_resource = 0;
		$unit_resource = 0;

		$sql_resitems = "SELECT `id`, `player_id`, `type_id`, `item_id`, `amount` FROM $table[playeritem] WHERE `player_id` = '$id'";
		$res_resitems = mysql_query($sql_resitems);
		while ($rec_resitems = mysql_fetch_array($res_resitems)) {
			$sql_itemdata = "SELECT `cost_steel`, `cost_crystal`, `cost_erbium`, `cost_titanium` FROM $table[items] WHERE `id` = '$rec_resitems[item_id]'";
			$rec_itemdata = mysql_fetch_array(mysql_query($sql_itemdata));
			$item_resource += $rec_itemdata['cost_steel'] + $rec_itemdata['cost_crystal'] + $rec_itemdata['cost_erbium'] + $rec_itemdata['cost_titanium'];
		}
		$sql_resunits = "SELECT * FROM $table[playerunit] WHERE `player_id` = '$id'";
		$res_resunits = mysql_query($sql_resunits);
		while ($rec_resunits = mysql_fetch_array($res_resunits)) {
			$sql_unitdata = "SELECT `cost_steel`, `cost_crystal`, `cost_erbium`, `cost_titanium` FROM $table[ships] WHERE `id` = '$rec_resunits[unit_id]'";
			$rec_unitdata = mysql_fetch_array(mysql_query($sql_unitdata));
			$unit_resource += ($rec_unitdata['cost_steel'] + $rec_unitdata['cost_crystal'] + $rec_unitdata['cost_erbium'] + $rec_unitdata['cost_titanium']) * $rec_resunits['amount'];
		}

		$total_asteroids = $p['roid_steel'] + $p['roid_crystal'] + $p['roid_erbium'] + $p['roid_unused'];
		$total_usedroids = $p['roid_steel'] + $p['roid_crystal'] + $p['roid_erbium'];
		$asteroid_initcost = round(($total_usedroids*$RES_FROM_STEEL_ROID*0.25)+((pow($total_usedroids, 1.55))*25));
		$asteroid_score = ((($asteroid_initcost/2)* $total_usedroids)/50);

		if ($asteroid_score > 1000000000) { $asteroid_score = 1000000000; }
		$new['score'] = ($stock_resource / 100) + ($item_resource / 30) + ($unit_resource / 10) + $asteroid_score;

		include('ticker.battle-engine.php');

		updatePlayerData($id, $new);
	}
	include('ticker.fleetcheck.php');
	//putCurrentTick($current);
	include('ticker.universe.php');
} else {
	//putCurrentTick(0);
}

$time_end = getmicrotime();
$time = $time_end - $time_start;
$num = rand();

//echo 'The ticker ran for '.$time.' seconds.';
?>
