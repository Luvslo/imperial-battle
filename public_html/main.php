<?
require_once('global.inc.php');

if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}

$player_id = $_SESSION['uid'];
$playerdata = getPlayerdata($player_id);
updatePlayerLoginData($player_id, $_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_HOST']);

define('IB_TICK_CURRENT', getCurrentTick());
define('IB_TICK_LAST', getLastTick());
define('IB_PLAYER_ID', $playerdata['id']);

if (!isset($mod)) { $mod = secureData($_GET['mod']); }
if (!isset($mod)) { $mod = secureData($_POST['mod']); }
if (!isset($act)) { $act = secureData($_GET['act']); }
if (!isset($act)) { $act = secureData($_POST['act']); }

if (!isset($mod)) { $mod = 'main'; $act = 'overview'; }

if ($playerdata['rules_accepted'] == 0) { $mod = 'rules'; }

switch($mod) {
	case 'rules':
		$page = 'Imperial-Battle rules';
		break;
	case 'main':
		$page = 'Main - '.$act;
		break;
	case 'alliance':
		if ($act == 'hq') { $page_add = 'headquarters'; }
		elseif ($act == 'admin') { $page_add = 'administration'; }
		else { $page_add = $act; }
		$page = 'Alliance - '.$page_add;
		break;
	case 'galaxy':
		$page = 'Galaxy - '.$act;
		break;
	case 'production':
		if ($_GET['type'] == 1) { $page_add = "constructions"; }
		if ($_GET['type'] == 2) { $page_add = "researches"; }
		if ($act == 'factory') { $page_add = "war factory"; }
		$page = 'Production - '.$page_add;
		break;
	case 'office':
		$page = 'Office - '.$act;
		break;
	case 'other':
		if ($act == 'myaccount') { $page = 'My account'; }
		break;
	case 'logout':
		$page = 'Logout';
		break;
	case 'market':
		$page = 'Market - '.$act;
		break;
}

require_once('includes/header.inc.php');
?>
<table border="0" cellpadding="0" cellspacing="0" width="800" id="table26" height="19">
	<tr>
		<td background="img/bg_balk.jpg" style="border: 1px solid #3C5762">&nbsp;
		</td>
	</tr>
	<tr>
		<td>
			<br><br>
			<?
			if ($mod == 'logout') {
				$user->logoutUser();
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=goto.login.php\">";
			}
			if ($mod == 'rules') {
				include('rules.php');
			}
			if ($mod == 'main') {
				if ($act == 'overview') { include('main.overview.php'); }
				if ($act == 'universe') { include('main.universe.php'); }
				if ($act == 'mail') { include('main.mail.php'); }
				if ($act == 'news') { include('main.news.php'); }
			}
			if ($mod == 'alliance') {
				if ($act == 'hq') { include('alliance.hq.php'); }
				if ($act == 'status') { include('alliance.status.php'); }
				if ($act == 'memberlist') { include('alliance.memberlist.php'); }
				if ($act == 'forum') {
					if (!isset($subact)) { $subact = secureData($_GET['subact']); }
					if ($subact == 'viewthread') { include('alliance.forum.viewthread.php'); }
					else { include('alliance.forum.php'); }
				 }
				 if ($act == 'admin') { include('alliance.admin.php'); }
			}
			if ($mod == 'production') {
				if ($act == 'item') { include('production.item.php'); }
				if ($act == 'factory') { include('production.factory.php'); }
			}
			if ($mod == 'galaxy') {
				if ($act == 'view') { include('galaxy.view.php'); }
				if ($act == 'status') { include('galaxy.status.php'); }
				if ($act == 'forum') {
					if (!isset($subact)) { $subact = secureData($_GET['subact']); }
					if ($subact == 'viewthread') { include('galaxy.forum.viewthread.php'); }
					else { include('galaxy.forum.php'); }
				 }
				if ($act == 'politics') { include('galaxy.politics.php'); }
			}
			if ($mod == 'office') {
				if ($act == 'resources') { include('office.resources.php'); }
				if ($act == 'fleet') { include('office.fleet.php'); }
				
				if ($act == 'intelligence') { include('office.intelligence.php'); }
			}
			if ($mod == 'other') {
				if ($act == 'myaccount') { include('other.myaccount.php'); }
			}
			if ($mod == 'market') {
				if ($act == 'market') { include('market.php'); }
				if ($act == 'sell') { include('market.sell.php'); }
			}
		?>
		</td>
	</tr>
</table>
<? include('includes/footer.inc.php'); ?>
</center>
<?

?>
<!-- Javascript functions -->
<script language="javascript">
updateResources('<?=parseInteger($playerdata['res_steel']);?>','<?=parseInteger($playerdata['res_crystal']);?>','<?=parseInteger($playerdata['res_erbium']);?>','<?=parseInteger($playerdata['res_titanium']);?>')
</script>		
</body>

</html>