<?
if (!isset($do)) { $do = secureData($_GET['do']); }
if (!isset($do)) { $do = secureData($_POST['do']); }
if (!isset($order_id)) { $order_id = secureData($_GET['order_id']); }
if (!isset($order_id)) { $order_id = secureData($_POST['order_id']); }

if (!isset($ship_id)) { $ship_id = secureData($_GET['ship_id']); }
if (!isset($ship_id)) { $ship_id = secureData($_POST['ship_id']); }

if ($do == 'createorder') {
		$sql_createorder = "INSERT INTO `$table[market]` (`player_id`, `status`) VALUES ('$playerdata[id]', '0')";
		mysql_query($sql_createorder) or die(mysql_error());
		$do = 'editorder';
		$ship_id = 0;
		$order_id = mysql_insert_id();
}
if ($_POST['add']){
	$baseships = getBaseShips($playerdata['id'], $ship_id);
	if ($baseships > 0){
		$amount = secureData($_POST['amount']); 
		$sql_egetoships = "SELECT `order_id`, `ship_id`, `amount` FROM $table[market_ships] WHERE `order_id` = '$order_id' AND `ship_id` = '$ship_id'";
		$rec_egetoships = mysql_query($sql_egetoships);
		$num_egetoships = mysql_num_rows($rec_egetoships);
		
		$sql_updships = "UPDATE $table[playerunit] SET `amount` = `amount` - '$amount' WHERE `player_id` = '$playerdata[id]' AND `unit_id` = '$ship_id'";
		mysql_query($sql_updships) or die(mysql_error());
		if ($num_egetoships != 0) {
			$res_egetoships = mysql_fetch_array($rec_egetoships);
			$fleetship_id = $res_egetoships['ship_id'];
			$amountfleet = $amount + $res_egetoships['amount'];
			$sql_updordership = "UPDATE $table[market_ships] SET `amount` = '$amountfleet' WHERE `order_id` = '$order_id' AND `ship_id` = '$ship_id'";
			mysql_query($sql_updordership) or die(mysql_error());
		
		}else{
			$sql_insertship = "INSERT INTO `$table[market_ships]` (`order_id`, `ship_id`, `amount`) VALUES ('$order_id', '$ship_id', '$amount')";
			mysql_query($sql_insertship) or die(mysql_error());
		}
	}else{
		$error = 101;
	}
}
if ($_POST['addp']){
	$e = 0;
	$sql_orderships = "SELECT `order_id`, `ship_id`, `amount` FROM `$table[market_ships]` WHERE `order_id` = '$order_id'";
	$rec_orderships = mysql_query($sql_orderships);
	$num_orderships = mysql_num_rows($rec_orderships);

	while ($res_orderships = mysql_fetch_assoc($rec_orderships)) {
		$cost_steel = $cost_steel + ($res_orderships['amount'] * getShipProperty($res_orderships['ship_id'], 'cost_steel'));
		$cost_crystal = $cost_crystal + ($res_orderships['amount'] * getShipProperty($res_orderships['ship_id'], 'cost_crystal'));
		$cost_erbium = $cost_erbium + ($res_orderships['amount'] * getShipProperty($res_orderships['ship_id'], 'cost_erbium'));
		$cost_titanium = $cost_titanium + ($res_orderships['amount'] * getShipProperty($res_orderships['ship_id'], 'cost_titanium'));

	}
	
	$cost_steel = $cost_steel * ($MARKET_MINIMUM_COST_PERCENTAGE / 100);
	$cost_crystal = $cost_crystal * ($MARKET_MINIMUM_COST_PERCENTAGE / 100);
	$cost_erbium = $cost_erbium * ($MARKET_MINIMUM_COST_PERCENTAGE / 100);
	$cost_titanium = $cost_titanium * ($MARKET_MINIMUM_COST_PERCENTAGE / 100);
	
	if ($steel < $cost_steel){$error = 102;$e = 1;}
	if ($crystal < $cost_crystal){$error = 102;$e = 1;}
	if ($erbium < $cost_erbium){$error = 102;$e = 1;}
	if ($titanium < $cost_titanium){$error = 102;$e = 1;}
	$steel = secureData($_POST['steel']); 
	$crystal = secureData($_POST['crystal']); 
	$erbium = secureData($_POST['erbium']); 
	$titanium = secureData($_POST['titanium']); 
	if ($e != 1){
		$sql_updorder = "UPDATE $table[market] SET `steel` = '$steel', `crystal` = '$crystal', `erbium` = '$erbium', `titanium` = '$titanium', `status` = '1' WHERE `id` = '$order_id'";
		mysql_query($sql_updorder) or die(mysql_error());
	}

}

if ($_POST['delete']){
	$ordernr = secureData($_POST['ordernr']); 
    $sql_getorderships = "SELECT `order_id`, `ship_id`, `amount` FROM $table[market_ships] WHERE `order_id` = $ordernr";
    $rec_getorderships = mysql_query($sql_getorderships);

    while ($res_getordership = mysql_fetch_array($rec_getorderships)) {
    	
    	$sql_updships = "UPDATE $table[playerunit] SET `amount` = `amount` + '$res_getordership[amount]' WHERE `player_id` = '$playerdata[id]' AND `unit_id` = '$res_getordership[ship_id]'";
		mysql_query($sql_updships) or die(mysql_error());
		$sql_delships = "DELETE FROM $table[market_ships] WHERE `order_id` = '$ordernr' AND `ship_id` = '$res_getordership[ship_id]'";
		mysql_query($sql_delships) or die(mysql_error());
		
    }

	$sql_delorderships = "DELETE FROM $table[market] WHERE `id` = '$ordernr' AND `player_id` = '$playerdata[id]'";
	mysql_query($sql_delorderships) or die(mysql_error());
	$do = 'createorder';
} 


?>
<script>
function submitEditFleet() {
	document.order_form.submit();
}
</script>
<?
switch($error) {

	case 101:
	$msg = "You do not have that amount of ships at base.";
	break;
	
	case 102:
	$msg = "The price you entered is to low. Make sure the price is ".$MARKET_MINIMUM_COST_PERCENTAGE."% of the factory costs.<br> The minimum prices are<br> Steel: ".$cost_steel." Crystal: ".$cost_crystal." Erbium: ".$cost_erbium." Titanium: ".$cost_titanium."";
	break;
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
?>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Order overview</td>
                <td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
                <td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
        </tr>
        <tr>
                <td width="4" background="img/border/L.gif">&nbsp;</td>
                <td width="696" height="100%" valign="top" colspan="2">
                        <table border="0" cellpadding="0" cellspacing="0" width="800">
                                <tr>
                                        <td valign="top">
                                                <table border="0" width="800">
                                                        <tr>
                                                                <td width="50" background="img/bg_balk.jpg"><b>Order #</b></td>
                                                                <td  colspan="4" background="img/bg_balk.jpg"><b>Price</b></td>
                                                                <td  background="img/bg_balk.jpg"></td>
                                                                <td width="200" background="img/bg_balk.jpg" align="center"><b>Actions</b></td>
                                                        </tr>
                                                        <?
                                                        $sql_getorders = "SELECT `id`, `player_id`, `steel`, `crystal`, `erbium`, `titanium` FROM $table[market] WHERE `player_id` = $playerdata[id] ORDER BY `id`";
                                                        $rec_getorders = mysql_query($sql_getorders);
                                                        if (mysql_num_rows($rec_getorders) < 1) {
                                                        ?>
                                                        <tr>
                                                        	<td colspan="7" align="center">You do not have any active order</td>
                                                        </tr>
                                                        <? 
                                                        } else {
                                                        	while ($res_getorders = mysql_fetch_array($rec_getorders)) {
                                                        		$total_ships = 0;
                                                        		$sql_totalships = "SELECT `amount` FROM $table[market_ships] WHERE `order_id` = '$res_getorders[id]'";
                                                        		$rec_totalships = mysql_query($sql_totalships);
                                                        		while ($res_totalships = mysql_fetch_array($rec_totalships)) {
                                                        			$total_ships += $res_totalships['amount'];
                                                        		}
                                                        	

                                                        ?>
                                                        <form method="POST" action="main.php?mod=market&act=sell&do=editorder&order_id=<?=$res_getorders['id'];?>">
                                                        <tr>
                                                                <td>#<?=$res_getorders['id'];?></td>
                                                                <td width="100"><b>Steel:</b> <?=$res_getorders['steel'];?></td>
                                                                <td width="100"><b>Crystal:</b> <?=$res_getorders['crystal'];?></td>
                                                                <td width="100"><b>Ebrium:</b> <?=$res_getorders['erbium'];?> </td>
                                                                <td width="100"><b>Titanum:</b> <?=$res_getorders['titanium'];?></td>
                                                                <td></td>
                                                                <td align="center"><input type="submit" name="edit" value="Edit"> <input type="submit" name="delete" value="Delete"><input type="hidden" name="ordernr" value="<?=$res_getorders['id'];?>"></td>
                                                        </tr>
                                                        </form>
                                                        <?
                                                        	}
                                                        }
														?>
                                                </table>
                                        </td>
                                </tr>
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
if ($do == 'editorder') {
?>
	<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Order</td>
                <td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
                <td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
        </tr>
        <tr>
                <td width="4" background="img/border/L.gif">&nbsp;</td>
                <td width="696" height="100%" valign="top" colspan="2" align="left">
                
                	
                   <?

            		$sql_shipdata = "SELECT `id`, `name`, `depends`, `eta`, `primary_target`, `secondary_target` FROM `$table[ships]`";
            		$rec_shipdata = mysql_query($sql_shipdata);
            		$shipdata = array();
            		$i = 0;
            		while ($res_shipdata = mysql_fetch_assoc($rec_shipdata)) {
            			$shipdata[$i] = $res_shipdata;
            			$i++;
            		}

                    ?>
				 <table border="0" width="800" height="10">
                
                    		<tr>
                        		<td width="62" background="img/bg_balk.jpg"><b>Order #</b></td>
                        		<td  background="img/bg_balk.jpg" align="left" width="684" colspan="10"><b>Price</b></td>
                        	</tr>
                        	 <tr>
                        	 <form method="POST" action="main.php?mod=market&act=sell&do=editorder&order_id=<?=$order_id;?>">
                        		<td><?= $order_id?></td>
                        		<td width="62"></td>
                        		<td width="35">Metal</td>
                        		<td width="62"><input type="text" name="steel" size="10" value="0"></td>
                        		<td width="43">Crystal</td>
                        		<td width="61"><input type="text" name="crystal" size="10" value="0"></td>
                        		<td width="42">Erbium</td>
                        		<td width="58"><input type="text" name="erbium" size="10" value="0"></td>
                        		<td width="56">Titanium</td>
                        		<td width="69"><input type="text" name="titanium" size="10" value="0"></td>
                        		<td width="160"><input type="submit" value="add price" name="addp"></td>
                        		</form>
                        	</tr>	
   
                        			
                       </table>
                <br>
					<table border="0" width="753">
                        	<tr>
                        		<td width="100"  background="img/bg_balk.jpg"><b>Ship name</b></td>
                        		<td  background="img/bg_balk.jpg" align="left"><b>Total amount</b></td>

                        	</tr>

     						<?

                            $sql_orderships = "SELECT `order_id`, `ship_id`, `amount` FROM `$table[market_ships]` WHERE `order_id` = '$order_id'";
                            $rec_orderships = mysql_query($sql_orderships);
                            $num_orderships = mysql_num_rows($rec_orderships);

                            if ($num_orderships > 0) {
                            	while ($res_orderships = mysql_fetch_assoc($rec_orderships)) {
  

                            ?>     
                        	 <tr>
                        		<td><? echo  getShipProperty($res_orderships['ship_id'], 'name');?></td>
                        		<td><? echo $res_orderships['amount'];?></td>
                        	</tr>
                                                       
          
                            <?
                            	}
                            } else {
                            ?>
                            <tr>
                        	    <td width="800" colspan="5" align="center">There are no ships in this order.</td>
                            </tr>
                            <?
                            }
                            ?>
                        	</form>
						</table>
						<br>

                Add ships to the order list
               	 <table border="0" width="753">
                <?
                $baseships = getBaseShips($playerdata['id'], $ship_id);
                $fleetships = getFleetShips($playerdata['id'], $fleet_id, $ship_id);
                ?>
               	<form name="order_form" method="POST" action="main.php?mod=market&act=sell&do=editorder&order_id=<?=$order_id;?>">
                        <tr>
                            <td  background="img/bg_balk.jpg"><b>Ship name</b></td>
                            <td  background="img/bg_balk.jpg" align="center"><b>Amount at base</b></td>
                            <td width="177" background="img/bg_balk.jpg" align="center"></td>
                            <td width="103" background="img/bg_balk.jpg" align="center"><b>Amount to sell</b></td>
                            <td width="95" background="img/bg_balk.jpg" align="center"></td>
                        </tr>
                        <tr>
                        	<td>
                    		<select name="ship_id" onChange="submitEditFleet()">
                    			<option value="0"<?if ($ship_id == 0) { echo 'selected'; }?>>-</option>
                    			<?
                    			
                    			for ($j = 0; $j < count($shipdata); $j++) {
                    			
                    				if (checkItem($playerdata['id'], $shipdata[$j]['depends']) && (!inFleet($fleet_id, $shipdata[$j]['id']) || ($edit_submit && inFleetShipCheck($fleet_id, $shipdata[$j]['id'], $ship_id)))) {
                    			?>
                    					<option value="<?=$shipdata[$j]['id'];?>"<?if ($ship_id == $shipdata[$j]['id']) { echo 'selected'; }?>><?=$shipdata[$j]['name'];?></option>
                    			<?
                    				}
                    			}
                    			?>
                    		</select>
                        	</td>
                        	<td align="center"><?if (!$baseships) { echo 0; } else { echo $baseships; }?></td>
                        	<td align="center"></td>
                            <td align="center"><input type="text" name="amount" size="10" value="0"></td>
 							<td align="center">
							<input type="submit" value="add" name="add">
	
							
							</td>
                        </tr>
                        </form>
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
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Create order</td>
                <td width="612" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
                <td width="4" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
        </tr>
        <tr>
                <td width="4" background="img/border/L.gif">&nbsp;</td>
                <td width="696" height="100%" valign="top" colspan="2">
                        <table border="0" cellpadding="0" cellspacing="0" width="800">
                                <tr>
                                        <td valign="top">
                                                <form method="POST" action="main.php?mod=market&act=sell&do=createorder">
                                                <table border="0" width="800">
                                                        <tr>
                                                                <td width="800" align="center"><b>&nbsp;</b></td>
                                                        </tr>
                                                        <tr>
                                                                <td width="800" align="center"><input type="submit" name="createorder" value="  Click here to create a new order  "></td>
                                                        </tr>
                                                </table>
                                                </form>
                                        </td>
                                </tr>
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