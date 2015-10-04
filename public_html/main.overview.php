<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}
?>

<table border="0" width="800" id="table1">
	<tr>
		<td width="500" valign="top">
			<table border="0" width="100%">
			<tr>
				<td valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
							<td width="30%">Alliance information</td>
							<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
							<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
						</tr>
						<tr>
							<td width="2%" background="img/border/L.gif">&nbsp;</td>
							<td width="95%" height="100%" valign="top" colspan="2">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td valign="top">
											<table width="100%" border="0">
											<?
											if ($playerdata['alliance_id'] > 0) {
												$sql_allimsg = "SELECT `message` FROM $table[alliance] WHERE `id` = '$playerdata[alliance_id]'";
												$res_allimsg = mysql_query($sql_allimsg);
												$num_allimsg = @mysql_num_rows($res_allimsg);
												if ($num_allimsg > 0) {
													$rec_allimsg = mysql_fetch_assoc($res_allimsg);
												}
											?>
											<tr>
												<td align="center" background="img/bg_balk.jpg"><b>Message from the alliance commanders</b></td>
											</tr>
											<tr>
												<td align="left"><?=nl2br(parseBBcode(stripslashes($rec_allimsg['message']), 1, 1, 1, 1));?></td>
											</tr>
											<tr>
												<td align="left">&nbsp;</td>
											</tr>
											<?
											if (checkItem($playerdata['id'], $ALLIANCE_COMMUNICATION)) {
												$sql_inc = "SELECT $table[playerfleet].player_id, $table[playerfleet].target_id,
                                                        					$table[playerfleet].action, $table[playerfleet].action_start
                                                        			FROM $table[playerfleet]
                                                        			INNER JOIN $table[players] ON $table[players].id = $table[playerfleet].target_id
                                                        			WHERE $table[players].alliance_id = '$playerdata[alliance_id]'
                                                        				AND ($table[playerfleet].action = 'defend' OR $table[playerfleet].action = 'attack')
                                                        ";
												$res_inc = mysql_query($sql_inc) or die(mysql_error());
												$num_inc = @mysql_num_rows($res_inc);
											?>
											<tr>
												<td align="center" background="img/bg_balk.jpg"><b>Incoming fleets for the alliance.</b></td>
											</tr>
											<tr>
												<td align="left">
												<?
												if ($num_inc > 0) {
													while($rec_inc = mysql_fetch_assoc($res_inc)) {
														$from_xyz = getXYZ($rec_inc['player_id']);
														$to_xyz = getXYZ($rec_inc['target_id']);
														$eta = $rec_inc['action_start'] - getCurrentTick();
														if ($eta < 0) { $eta = 0; }
														if ($rec_inc['action'] == 'attack') { $tdclass = 'class="hostile"'; }
														if ($rec_inc['action'] == 'defend') { $tdclass = 'class="friendly"'; }
												?>
													
													<a <?if ($tdclass) echo $tdclass;?>><?echo $from_xyz[0].':'.$from_xyz[1].':'.$from_xyz[2];?> - <b><?=getRulernameById($rec_inc['player_id']);?> of <?=getPlanetnameById($rec_inc['player_id']);?></b> -> 
                                                	<?echo $to_xyz[0].':'.$to_xyz[1].':'.$to_xyz[2];?> - <b><?=getRulernameById($rec_inc['target_id']);?> of <?=getPlanetnameById($rec_inc['target_id']);?></b></a><br/>
												<?
													}
												}
												else {
													echo 'No incomings for your alliance.';
												}
												?>
												</td>
											</tr>
											<?
											}
											?>
											<?
											} else {
											?>
											<tr>
												<td align="center">You are not a member of an alliance.</td>
											</tr>
											<?
											}
											?>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<td width="3%" background="img/border/R.gif">&nbsp;</td>
						</tr>
						<tr>
							<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
							<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
							<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
							<td width="30%">Message of the day</td>
							<td width="65%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
							<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
						</tr>
						<tr>
							<td width="2%" background="img/border/L.gif">&nbsp;</td>
							<td width="95%" height="100%" valign="top" colspan="2">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td valign="top">
											<table border="0" width="100%">
											<?
											$sql_news = "SELECT * FROM $table[news] ORDER by `timestamp` DESC LIMIT 5";
											$res_news = mysql_query($sql_news);
											if (@mysql_num_rows($res_news) > 0) {
												while ($rec_news = mysql_fetch_array($res_news)) {
											?>
												<tr>
													<td width="60%" background="img/bg_balk.jpg"><b><? echo $rec_news['title']; ?></b></td>
													<td width="40%" background="img/bg_balk.jpg">Posted at <b><?echo date("H:i d-m-Y", $rec_news['timestamp']);?></b></td>
												</tr>
												<tr>
													<td colspan="2"><?echo stripslashes(nl2br($rec_news['content']));?></td>
												</tr>
												<tr>
													<td colspan="2" height="15">&nbsp;</td>
												</tr>
											<?
												}
											} else {
											?>
												<tr>
													<td colspan="2">There's currently no game information available!</td>
												</tr>
											<?
											}
											?>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<td width="3%" background="img/border/R.gif">&nbsp;</td>
						</tr>
						<tr>
							<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
							<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
							<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
						</tr>
					</table>				
				</td>
			</tr>
		</table>
	</td>
	<td width="300" valign="top">
		<table border="0" width="100%">
			<tr>
				<td valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
							<td width="40%">Fleet information</td>
							<td width="55%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
							<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
						</tr>
						<tr>
							<td width="2%" background="img/border/L.gif">&nbsp;</td>
							<td width="95%" height="100%" valign="top" colspan="2">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td valign="top">
											<?
											$incoming = "";
											$sql_hostile = "SELECT `player_id`, `action_start` FROM $table[playerfleet] WHERE `target_id` = '$playerdata[id]' AND `action` = 'attack'";
											$res_hostile = mysql_query($sql_hostile);
											$num_hostile = @mysql_num_rows($res_hostile);
											if ($num_hostile > 0) {
												$incoming .= "Incomings:";
												while($rec_hostile = mysql_fetch_assoc($res_hostile)) {
													$xyz = getXYZ($rec_hostile['player_id']);
													if (($rec_hostile['action_start'] - getCurrentTick()) < 1) { $eta = 0; }
													else { $eta = $rec_hostile['action_start'] - getCurrentTick(); }
													$incoming .= '<font color="red"><li>Hostile from '.$xyz[0].':'.$xyz[1].':'.$xyz[2].' - ETA: '.$eta.'</font>';
												}
											}
											$sql_friendly = "SELECT `player_id`, `action_start` FROM $table[playerfleet] WHERE `target_id` = '$playerdata[id]' AND `action` = 'defend'";
											$res_friendly = mysql_query($sql_friendly);
											$num_friendly = @mysql_num_rows($res_friendly);
											if ($num_friendly > 0) {
												if (strlen($incoming) < 1) { $incoming = "Incomings:"; }
												while($rec_friendly = mysql_fetch_assoc($res_friendly)) {
													$xyz = getXYZ($rec_friendly['player_id']);
													if (($rec_friendly['action_start'] - getCurrentTick()) < 1) { $eta = 0; }
													else { $eta = $rec_friendly['action_start'] - getCurrentTick(); }
													$incoming .= '<font color="green"><li>Friendly from '.$xyz[0].':'.$xyz[1].':'.$xyz[2].' - ETA: '.$eta.'</font>';
												}
											}
											if (strlen($incoming) > 0) {
												echo $incoming;
												echo '<br>';
											}

											$outgoing = "";
											$sql_outhostile = "SELECT `target_id`, `action_start` FROM $table[playerfleet] WHERE `player_id` = '$playerdata[id]' AND `action` = 'attack'";
											$res_outhostile = mysql_query($sql_outhostile);
											$num_outhostile = @mysql_num_rows($res_outhostile);
											if ($num_outhostile > 0) {
												$outgoing .= "Outgoings:";
												while ($rec_outhostile = mysql_fetch_assoc($res_outhostile)) {
													$xyz = getXYZ($rec_outhostile['target_id']);
													if (($rec_outhostile['action_start'] - getCurrentTick()) < 1) { $eta = 0; }
													else { $eta = $rec_outhostile['action_start'] - getCurrentTick(); }
													$outgoing .= '<font color="red"><li>Hostile to '.$xyz[0].':'.$xyz[1].':'.$xyz[2].' - ETA: '.$eta.'</font>';
												}
											}
											$sql_outfriendly = "SELECT `target_id`, `action_start` FROM $table[playerfleet] WHERE `player_id` = '$playerdata[id]' AND `action` = 'defend'";
											$res_outfriendly = mysql_query($sql_outfriendly);
											$num_outfriendly = @mysql_num_rows($res_outfriendly);
											if ($num_outfriendly > 0) {
												if (strlen($outgoing) < 1) { $outgoing = "Incomings:"; }
												while ($rec_outfriendly = mysql_fetch_assoc($res_outfriendly)) {
													$xyz = getXYZ($rec_outfriendly['target_id']);
													if (($rec_outfriendly['action_start'] - getCurrentTick()) < 1) { $eta = 0; }
													else { $eta = $rec_outfriendly['action_start'] - getCurrentTick(); }
													$outgoing .= '<font color="green"><li>Friendly to '.$xyz[0].':'.$xyz[1].':'.$xyz[2].' - ETA: '.$eta.'</font>';
												}
											}
											if (strlen($outgoing) > 0) {
												if (strlen($incoming) > 0) { echo '<br>'; }
												echo $outgoing;
												echo '<br>';
											}
											if ((strlen($outgoing) < 1) && (strlen($incoming) < 1)) {
												echo 'No fleet movements around your planet.';
											}
											?>
										</td>
									</tr>
								</table>
							</td>
							<td width="3%" background="img/border/R.gif">&nbsp;</td>
						</tr>
						<tr>
							<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
							<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
							<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
							<td width="38%">Production info</td>
							<td width="57%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
							<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
						</tr>
						<tr>
							<td width="2%" background="img/border/L.gif">&nbsp;</td>
							<td width="95%" height="100%" valign="top" colspan="2">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td valign="top">
											<?
											$sql_getprod = "SELECT * FROM $table[productions] WHERE `player_id` = '$playerdata[id]' ORDER BY `ready_tick`";
											$rec_getprod = mysql_query($sql_getprod);
											if (@mysql_num_rows($rec_getprod) > 0) {
												$tick = getCurrentTick();
												while ($res_getprod = mysql_fetch_array($rec_getprod)) {
													if ($res_getprod['type_id'] == 3) { $sql_getitem = "SELECT `name` FROM $table[ships] WHERE `id`	= '$res_getprod[item_id]'"; }
													elseif ($res_getprod['type_id'] == 4) { $sql_getitem = "SELECT `name` FROM $table[defense] WHERE `id` = '$res_getprod[item_id]'"; }
													else { $sql_getitem = "SELECT `name` FROM $table[items] WHERE `id`	= '$res_getprod[item_id]'"; }
													$res_getitem = mysql_fetch_array(mysql_query($sql_getitem));
													$eta = $res_getprod[ready_tick] - $tick;
													if ($res_getprod['amount'] > 1) {
														$prod_line = '<li>'.$res_getprod['amount'].' '.$res_getitem['name'].'s - ETA '.$eta.'<br>';
													} else {
														$prod_line = '<li>'.$res_getitem['name'].' - ETA '.$eta.'<br>';
													}
													echo $prod_line;
												}
											} else {
												echo 'No productions at the moment.';
											}

											?>
										</td>
									</tr>
								</table>
							</td>
							<td width="3%" background="img/border/R.gif">&nbsp;</td>
						</tr>
						<tr>
							<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
							<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
							<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
							<td width="38%">Asteroid info</td>
							<td width="57%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
							<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
						</tr>
						<tr>
							<td width="2%" background="img/border/L.gif">&nbsp;</td>
							<td width="95%" height="100%" valign="top" colspan="2">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td valign="top">
											<table width="100%" border="0">
												<tr>
													<td width="65%" background="img/bg_balk.jpg"><b>Type</b></td>
													<td width="35%" background="img/bg_balk.jpg"><b>Amount</b></td>
												</tr>
											<?
											$steel_roids = $playerdata['roid_steel'];
											$crystal_roids = $playerdata['roid_crystal'];
											$erbium_roids = $playerdata['roid_erbium'];
											$unused_roids = $playerdata['roid_unused'];
											$total_roids = ($steel_roids + $crystal_roids + $erbium_roids + $unused_roids);
											?>
													<tr>
														<td>Steel:</td>
														<td><?=parseInteger($steel_roids);?></td>
													</tr>
													<tr>
														<td>Crystal:</td>
														<td><?=parseInteger($crystal_roids);?></td>
													</tr>
													<tr>
														<td>Erbium:</td>
														<td><?=parseInteger($erbium_roids);?></td>
													</tr>
													<tr>
														<td>Unused:</td>
														<td><?=parseInteger($unused_roids);?></td>
													</tr>
													<tr>
														<td>Total:</td>
														<td><?=parseInteger($total_roids);?></td>
													</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<td width="3%" background="img/border/R.gif">&nbsp;</td>
						</tr>
						<tr>
							<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
							<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
							<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
						</tr>
					</table>
				<td>
			</tr>
			<tr>
				<td valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
							<td width="38%">Ship info</td>
							<td width="57%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
							<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
						</tr>
						<tr>
							<td width="2%" background="img/border/L.gif">&nbsp;</td>
							<td width="95%" height="100%" valign="top" colspan="2">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td valign="top">
											<table width="100%" border="0">
												<tr>
													<td width="65%" background="img/bg_balk.jpg"><b>Type</b></td>
													<td width="35%" background="img/bg_balk.jpg"><b>Amount</b></td>
												</tr>
											<?
											$sql_ships = "
									SELECT $table[playerunit].type_id, $table[playerunit].unit_id, $table[playerunit].amount, $table[ships].name
									FROM $table[playerunit] INNER JOIN $table[ships]
									ON $table[playerunit].unit_id = $table[ships].id
									WHERE $table[playerunit].player_id = '$playerdata[id]'
									ORDER BY $table[ships].id";
											$res_ships = mysql_query($sql_ships);
											$num_ships = @mysql_num_rows($res_ships);
											if ($num_ships > 0) {
												while ($rec_ships = mysql_fetch_assoc($res_ships)) {
													if ($rec_ships['amount'] > 0) {
											?>
													<tr>
														<td><?=$rec_ships['name'];?></td>
														<td><?=parseInteger($rec_ships['amount']);?></td>
													</tr>
											<?
													}
												}
											} else {
											?>
													<tr>
														<td colspan="2" align="center">You do not have ships.</td>
													</tr>
											<?
											}
											?>
											</table>
										</td>
								</tr>
							</table>
						</td>
						<td width="3%" background="img/border/R.gif">&nbsp;</td>
					</tr>
					<tr>
						<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
						<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
						<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
					</tr>
				</table>
				<td>
			</tr>
			<?
			if (false) {
			?>
			<tr>
				<td valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="2%" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
							<td width="23%">TPDS</td>
							<td width="72%" background="img/border/B.gif"><img border="0" src="img/border/B.gif" width="16" height="15"></td>
							<td width="3%" valign="bottom"><img border="0" src="img/border/R_B.gif" width="20" height="15"></td>
						</tr>
						<tr>
							<td width="2%" background="img/border/L.gif">&nbsp;</td>
							<td width="95%" height="100%" valign="top" colspan="2">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td valign="top" align="center">
											No TPDS units available
										</td>
									</tr>
								</table>
							</td>
							<td width="3%" background="img/border/R.gif">&nbsp;</td>
						</tr>
						<tr>
							<td width="2%" valign="top"><img border="0" src="img/border/L_O.gif" width="20" height="15"></td>
							<td width="100%" background="img/border/O.gif" colspan="2">&nbsp;</td>
							<td width="3%" valign="top"><img border="0" src="img/border/R_O.gif" width="20" height="15"></td>
						</tr>
					</table>
				</td>
			</tr>	
			<?
			}
			?>			
		</table>
	</td>
	</tr>
</table>