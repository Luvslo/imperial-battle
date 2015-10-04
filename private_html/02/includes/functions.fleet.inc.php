<?
/* All functions related to fleets.. */

function getMaxFleets($player_id) {
	global $table, $BASIC_FLEETCONTROL, $ADV_FLEETCONTROL, $NG_FLEETCONTROL;
	if (checkItem($player_id, $NG_FLEETCONTROL)) { return 3; }
	elseif (checkItem($player_id, $ADV_FLEETCONTROL)) { return 2; }
	elseif (checkItem($player_id, $BASIC_FLEETCONTROL)) { return 1; }
	else { return 0; }
	return 0;
}

?>