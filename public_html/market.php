<?
if($_POST['buy2']){

	$cost_steel = secureData($_POST['cost_steel']);
	$cost_crystal = secureData($_POST['cost_crystal']);
	$cost_erbium = secureData($_POST['cost_erbium']);
	$cost_titanium = secureData($_POST['cost_titanium']);
	$order_id = secureData($_POST['order_id']);
	$seller_id = secureData($_POST['seller_id']);

	if (checkSteelResource($playerdata['id'], $cost_steel) && checkCrystalResource($playerdata['id'], $cost_crystal) && checkErbiumResource($playerdata['id'], $cost_erbium) && checkTitaniumResource($playerdata['id'], $cost_titanium)) {

		$sql_updres = "UPDATE $table[players] SET `res_steel` = `res_steel` - '$cost_steel', `res_crystal` = `res_crystal` - '$cost_crystal', `res_erbium` = `res_erbium` - '$cost_erbium', `res_titanium` = `res_titanium` - '$cost_titanium' WHERE `id` = '$playerdata[id]'";
		mysql_query($sql_updres) or die(mysql_error());

		$sql_updres2 = "UPDATE $table[players] SET `res_steel` = `res_steel` + '$cost_steel', `res_crystal` = `res_crystal` + '$cost_crystal', `res_erbium` = `res_erbium` + '$cost_erbium', `res_titanium` = `res_titanium` + '$cost_titanium' WHERE `id` = '$seller_id'";
		mysql_query($sql_updres2) or die(mysql_error());

		$sql_getorderships = "SELECT `order_id`, `ship_id`, `amount` FROM $table[market_ships] WHERE `order_id` = '$order_id'";
		$rec_getorderships = mysql_query($sql_getorderships);

		while ($res_getordership = mysql_fetch_array($rec_getorderships)) {
			$baseships = getBaseShips($playerdata['id'], $res_getordership['ship_id']);
			if ($baseships == 0){
				$sql_insertship = "INSERT INTO `$table[playerunit]` (`player_id`, `type_id`, `unit_id`, `amount`) VALUES ('$playerdata[id]', '3', '$res_getordership[ship_id]', '$res_getordership[amount]')";
				mysql_query($sql_insertship) or die(mysql_error());
			}else{
				$sql_updships = "UPDATE $table[playerunit] SET `amount` = `amount` + '$res_getordership[amount]' WHERE `player_id` = '$playerdata[id]' AND `unit_id` = '$res_getordership[ship_id]'";
				mysql_query($sql_updships) or die(mysql_error());
			}
			$sql_delships = "DELETE FROM $table[market_ships] WHERE `order_id` = '$order_id' AND `ship_id` = '$res_getordership[ship_id]'";
			mysql_query($sql_delships) or die(mysql_error());

		}

		$sql_delorderships = "DELETE FROM $table[market] WHERE `id` = '$order_id'";
		mysql_query($sql_delorderships) or die(mysql_error());

	} else {
		$error = 102;

	}
}

switch($error) {
	case 102:
	$msg = "You have not enough resources to buy this product.";

}


if ($msg) {
	if ($error > 99) { $error_color = 'red'; }
	else { $error_color = 'green'; }

?>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
		<td valign="top"><p align="center"><font color="<?echo $error_color;?>"><?echo $msg;?></font></td>
	</tr>
	<tr height="15">
		<td valign="top"></td>
	</tr>	
</table>
<?
}
if ($_POST['buy']){
?>
<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
    	<td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
        <td width="180">Status</td>
        <td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
        <td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
	</tr>
    <tr>
    	<td width="4" background="img/border/L.gif">&nbsp;</td>
        <td width="696" height="100%" valign="top" colspan="2">
        	<table border="0" cellpadding="0" cellspacing="0" width="800">
            	<tr>
                	<td valign="top">
                		<form method="POST" action="main.php?mod=market&act=market&order_id=<?=$order_id;?>">
                    	<table border="0" width="800">
                        	<tr>
                            	<td align="center" colspan="2">Are you sure you want to buy this product?</td>
                            </tr>
	                        <tr>
	                        	<td background="img/bg_balk.jpg"><b>Product</b></td>
	                            <td width="200" background="img/bg_balk.jpg" ><b>Price</b></td>
							</tr>
							<tr bgcolor="#24485a">
								<input type="hidden" value="<?=$order_id;?>" name="order_id">
								<input type="hidden" value="<?=$seller_id;?>" name="seller_id">
								<input type="hidden" value="<?=$cost_steel;?>" name="cost_steel">
								<input type="hidden" value="<?=$cost_crystal;?>" name="cost_crystal">
								<input type="hidden" value="<?=$cost_erbium;?>" name="cost_erbium">
								<input type="hidden" value="<?=$cost_titanium;?>" name="cost_titanium">
								<td>
									<?
												$sql_getorderships = "SELECT `order_id`, `ship_id`, `amount` FROM $table[market_ships] WHERE `order_id` = $order_id";
												$rec_getorderships = mysql_query($sql_getorderships);

												while ($res_getordership = mysql_fetch_array($rec_getorderships)) {
													echo '&nbsp;<b>' .getShipProperty($res_getordership['ship_id'], 'name').':</b> '.$res_getordership['amount'].'<br>';
												}
									?>
								</td>
								<td width="200">
									&nbsp;<b>Steel:</b><?=$cost_steel;?><br>
									&nbsp;<b>Crystal:</b><?=$cost_crystal;?><br>
									&nbsp;<b>Erbium:</b> <?=$cost_erbium;?><br>
									&nbsp;<b>Titanium:</b> <?=$cost_titanium;?><br>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="2"><input type="submit" value="Yes" name="buy2" width="75px"></td>
							</tr>
						</table>
						</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
      	<td width="4" background="img/border/R.gif">&nbsp;</td>
	</tr>
    <tr>
    	<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
    	<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
    	<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
	</tr>
</table>	
<?

}else{
?>
	<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
            <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
            <td width="180">Status</td>
            <td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
        	<td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
        </tr>
        <tr>
            <td width="4" background="img/border/L.gif">&nbsp;</td>
            <td width="696" height="100%" valign="top" colspan="2">
            	<table border="0" cellpadding="2" cellspacing="1" width="800">
	            	<tr>           
	            		<td background="img/bg_balk.jpg"><b>Offer</b></td>
	            		<td width="200" background="img/bg_balk.jpg" ><b>Price</b></td>
	            		<td width="75" background="img/bg_balk.jpg" ><b>Player</b></td>
	            		<td width="75" background="img/bg_balk.jpg" align="center">&nbsp;</td>
	           		</tr>                                                
					<?

					$sql_orderdata = "SELECT `id`, `player_id`, `steel`, `crystal`, `erbium`, `titanium` FROM `$table[market]` WHERE status = 1 ORDER by id";
					$rec_orderdata = mysql_query($sql_orderdata);

					$i = 0;
					while ($res_orderdata = mysql_fetch_assoc($rec_orderdata)) {
						$player = getPlayerdata($res_orderdata['player_id']);
					?>
					<form method="POST" action="main.php?mod=market&act=market&order_id=<?=$res_orderdata['id'];?>">
					<tr bgcolor="#24485a">
						<input type="hidden" value="<?=$res_orderdata['id'];?>" name="order_id">
						<input type="hidden" value="<?=$res_orderdata['player_id'];?>" name="seller_id">
						<input type="hidden" value="<?=$res_orderdata['steel'];?>" name="cost_steel">
						<input type="hidden" value="<?=$res_orderdata['crystal'];?>" name="cost_crystal">
						<input type="hidden" value="<?=$res_orderdata['erbium'];?>" name="cost_erbium">
						<input type="hidden" value="<?=$res_orderdata['titanium'];?>" name="cost_titanium">
						<td valign="top">
							<?
							$sql_getorderships = "SELECT `order_id`, `ship_id`, `amount` FROM $table[market_ships] WHERE `order_id` = $res_orderdata[id]";
							$rec_getorderships = mysql_query($sql_getorderships);

							while ($res_getordership = mysql_fetch_array($rec_getorderships)) {
								echo '<b>' .getShipProperty($res_getordership['ship_id'], 'name').':</b> '.parseInteger($res_getordership['amount']).'<br>';
							}
							?>
						</td>
						<td width="200">
							<b>Steel:</b>&nbsp;<?=parseInteger($res_orderdata['steel']);?><br>
							<b>Crystal:</b>&nbsp;<?=parseInteger($res_orderdata['crystal']);?><br>
							<b>Erbium:</b>&nbsp;<?=parseInteger($res_orderdata['erbium']);?><br>
							<b>Titanium:</b>&nbsp;<?=parseInteger($res_orderdata['titanium']);?><br>
						</td>
						<td width="75" valign="middle"><?=$player['rulername'];?></td>
						<td width="75" align="center" valign="middle"><input type="submit" value="Buy" name="buy"></td>
					</tr>
					</form>
					<?
					}

					?>
                </table>
            </td>
            <td width="4" background="img/border/R.gif">&nbsp;</td>
        </tr>
        <tr>
                <td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
                <td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
                <td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
        </tr>
	</table>	
<?
}
?>