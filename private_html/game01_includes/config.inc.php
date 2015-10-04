<?
$dbconf['ip']					= 'localhost';
$dbconf['port']					= '3306';
$dbconf['username']				= 'imperial_game01';
$dbconf['password']				= '';
$dbconf['database']				= 'imperial_game01';

$table['prefix'] 				= 'g01_';
$table['players']				= $table['prefix'] . 'players';
$table['galaxy']				= $table['prefix'] . 'galaxy';
$table['items']					= $table['prefix'] . 'items';
$table['itemtypes']				= $table['prefix'] . 'itemtypes';
$table['playeritem']			= $table['prefix'] . 'playeritem';
$table['productions']			= $table['prefix'] . 'productions';
$table['tick']					= $table['prefix'] . 'tick';
$table['universe']				= $table['prefix'] . 'universe';
$table['universe_galaxy']		= $table['prefix'] . 'universe_galaxy';
$table['universe_alliance']		= $table['prefix'] . 'universe_alliance';
$table['ships']					= $table['prefix'] . 'ships';
$table['defense']				= $table['prefix'] . 'defense';
$table['news']					= $table['prefix'] . 'news';
$table['politics']				= $table['prefix'] . 'politics';
$table['playerunit']			= $table['prefix'] . 'playerunit';
$table['fleet']					= $table['prefix'] . 'fleet';
$table['mail']					= $table['prefix'] . 'mail';
$table['playernews']			= $table['prefix'] . 'playernews';
$table['galaxyforum_threads']	= $table['prefix'] . 'galaxyforum_threads';
$table['galaxyforum_posts']		= $table['prefix'] . 'galaxyforum_posts';
$table['alliance']				= $table['prefix'] . 'alliance';
$table['alliancenews']			= $table['prefix'] . 'alliancenews';
$table['allianceforum_threads']	= $table['prefix'] . 'allianceforum_threads';
$table['allianceforum_posts']	= $table['prefix'] . 'allianceforum_posts';
$table['titanium_factory']		= $table['prefix'] . 'titanium_factory';
$table['playerfleet']			= $table['prefix'] . 'playerfleet';
$table['playerfleet_ships']		= $table['prefix'] . 'playerfleet_ships';
$table['adminlog']				= $table['prefix'] . 'adminlog';
$table['playerlog']				= $table['prefix'] . 'playerlog';


$PRIVATEKEY 			= 'IBkdlfjdrjf48o489573495drkhdfhg249084285724357kdghgh19834y1q8293qqqnwjfjgjgg****q3*34*34234*2*324';

/* Just some numbers. */
$MAX_CLUSTER			= 10;			/* Defines max galaxies in 1 cluster. */
$MAX_PLAYERS 			= 10;			/* Defines max players in 1 galaxy. */

$TICKER_INTERVAL		= 60;

$RES_FROM_REFINERY	 	= 250;
$RES_FROM_ADV_REF		= 1000;
$RES_FROM_STEEL_ROID	= 500;
$RES_FROM_CRYSTAL_ROID	= 400;
$RES_FROM_ERBIUM_ROID	= 250;
$ASTEROID_ARMOR			= 100;

/* Corresponding database id's */
$STEEL_REFINERY			= 2;
$CRYSTAL_REFINERY 		= 3;
$ERBIUM_REFINERY		= 4;
$TITANIUM_FACTORY		= 10;

$STEEL_ADV_REF			= 6;
$CRYSTAL_ADV_REF		= 7;
$ERBIUM_ADV_REF			= 8;

$BASIC_INTELLIGENCE		= 12;
$ADVANCED_INTELLIGENCE	= 14;
$NG_INTELLIGENCE		= 16;
$GALACTIC_INTELLIGENCE	= 17;
$ALLIANCE_COMMUNICATION	= 18;

$ASTEROID_PIRATE		= 4;
$NG_ASTEROID_PIRATE		= 10;
?>
