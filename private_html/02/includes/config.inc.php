<?
/* Database configuration */
$dbconf['ip']					= 'localhost';
$dbconf['port']					= '3306';
$dbconf['username']				= 'imperial_game02';
$dbconf['password']				= '';
$dbconf['database']				= 'imperial_game02';

/* Table names */
$table['prefix'] 				= 'g02_';

$table['adminlog']				= $table['prefix'] . 'adminlog';
$table['alliance']				= $table['prefix'] . 'alliance';
$table['allianceforum_posts']	= $table['prefix'] . 'allianceforum_posts';
$table['allianceforum_threads']	= $table['prefix'] . 'allianceforum_threads';
$table['alliancenews']			= $table['prefix'] . 'alliancenews';

$table['defense']				= $table['prefix'] . 'defense';

$table['galaxy']				= $table['prefix'] . 'galaxy';
$table['galaxyforum_posts']		= $table['prefix'] . 'galaxyforum_posts';
$table['galaxyforum_threads']	= $table['prefix'] . 'galaxyforum_threads';

$table['items']					= $table['prefix'] . 'items';
$table['itemtypes']				= $table['prefix'] . 'itemtypes';

$table['mail']					= $table['prefix'] . 'mail';
$table['market']				= $table['prefix'] . 'market';
$table['market_ships']			= $table['prefix'] . 'market_ships';

$table['news']					= $table['prefix'] . 'news';

$table['playerfleet']			= $table['prefix'] . 'playerfleet';
$table['playerfleet_ships']		= $table['prefix'] . 'playerfleet_ships';
$table['playeritem']			= $table['prefix'] . 'playeritem';
$table['playerlog']				= $table['prefix'] . 'playerlog';
$table['playernews']			= $table['prefix'] . 'playernews';
$table['players']				= $table['prefix'] . 'players';
$table['playerunit']			= $table['prefix'] . 'playerunit';
$table['politics']				= $table['prefix'] . 'politics';
$table['productions']			= $table['prefix'] . 'productions';

$table['rules']					= $table['prefix'] . 'rules';
$table['rulescat']				= $table['prefix'] . 'rulescat';

$table['ships']					= $table['prefix'] . 'ships';

$table['tick']					= $table['prefix'] . 'tick';
$table['titanium_factory']		= $table['prefix'] . 'titanium_factory';

$table['universe']				= $table['prefix'] . 'universe';
$table['universe_galaxy']		= $table['prefix'] . 'universe_galaxy';
$table['universe_alliance']		= $table['prefix'] . 'universe_alliance';


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

/* Asteroid pirate ships */
$ASTEROID_PIRATE		= 4;
$NG_ASTEROID_PIRATE		= 10;

/* Market setting */
$MARKET_MINIMUM_COST_PERCENTAGE = 70; 

/* Corresponding database id's */
$STEEL_REFINERY			= 2;
$CRYSTAL_REFINERY 		= 3;
$ERBIUM_REFINERY		= 4;

$STEEL_ADV_REF			= 6;
$CRYSTAL_ADV_REF		= 7;
$ERBIUM_ADV_REF			= 8;

$TITANIUM_FACTORY		= 10;

$BASIC_INTELLIGENCE		= 12;
$ADVANCED_INTELLIGENCE	= 14;
$NG_INTELLIGENCE		= 16;
$UNI_INTELLIGENCE		= 18;

$GALACTIC_INTELLIGENCE	= 19;
$ALLIANCE_COMMUNICATION	= 20;

$BASIC_FLEETCONTROL		= 28;
$ADV_FLEETCONTROL		= 30;
$NG_FLEETCONTROL		= 32;
?>
