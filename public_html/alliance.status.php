<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}
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
                                                <table border="0" width="800">
                                                <?
                                                if (checkItem($playerdata['id'], $ALLIANCE_COMMUNICATION)) {
                                                ?>
                                                        <tr>
                                                        	<td height="15" align="left" colspan="3">Incoming fleets</td>
                                                        </tr>
                                                        <tr>
                                                        	<td background="img/bg_balk.jpg" width="50"><b>Type</b></td>
                                                        	<td background="img/bg_balk.jpg" width="350"><b>From</b></td>
                                                        	<td background="img/bg_balk.jpg" width="350"><b>To</b></td>
                                                        	<td background="img/bg_balk.jpg" width="50"><b>ETA</b></td>
                                                        </tr>
                                                        <?
                                                        $sql_inc = "SELECT $table[playerfleet].player_id, $table[playerfleet].target_id,
                                                        					$table[playerfleet].action, $table[playerfleet].action_start
                                                        			FROM $table[playerfleet]
                                                        			INNER JOIN $table[players] ON $table[players].id = $table[playerfleet].target_id
                                                        			WHERE $table[players].alliance_id = '$playerdata[alliance_id]'
                                                        				AND ($table[playerfleet].action = 'defend' OR $table[playerfleet].action = 'attack')
                                                        			ORDER BY `action_start`
                                                        ";
                                                        $res_inc = mysql_query($sql_inc) or die(mysql_error());
                                                        $num_inc = @mysql_num_rows($res_inc);
                                                        if ($num_inc > 0) {
                                                        	while($rec_inc = mysql_fetch_assoc($res_inc)) {
                                                        		$from_xyz = getXYZ($rec_inc['player_id']);
                                                        		$to_xyz = getXYZ($rec_inc['target_id']);
                                                        		$eta = $rec_inc['action_start'] - getCurrentTick();
                                                        		if ($eta < 0) { $eta = 0; }
                                                        		if ($rec_inc['action'] == 'attack') { $tdclass = 'class="hostile"'; }
                                                        		if ($rec_inc['action'] == 'defend') { $tdclass = 'class="friendly"'; }
                                                        ?>
                                                        <tr>
                                                        	<td <?if ($tdclass) echo $tdclass;?>><?if ($rec_inc['action'] == 'attack') echo 'Hostile'; elseif ($rec_inc['action'] == 'defend') echo 'Friendly'; ?></td>
                                                        	<td <?if ($tdclass) echo $tdclass;?>><a href="main.php?mod=galaxy&act=view&x=<?=$from_xyz[0]?>&y=<?=$from_xyz[1]?>"><?echo $from_xyz[0].':'.$from_xyz[1].':'.$from_xyz[2];?></a> (<a href="main.php?mod=main&act=mail&do=compose&x=<?=$from_xyz[0];?>&y=<?=$from_xyz[1];?>&z=<?=$from_xyz[2];?>"><?=getRulernameById($rec_inc['player_id']);?> of <?=getPlanetnameById($rec_inc['player_id']);?></a>)</td>
                                                        	<td <?if ($tdclass) echo $tdclass;?>><a href="main.php?mod=galaxy&act=view&x=<?=$to_xyz[0]?>&y=<?=$to_xyz[1]?>"><?echo $to_xyz[0].':'.$to_xyz[1].':'.$to_xyz[2];?></a> (<a href="main.php?mod=main&act=mail&do=compose&x=<?=$to_xyz[0];?>&y=<?=$to_xyz[1];?>&z=<?=$to_xyz[2];?>"><?=getRulernameById($rec_inc['target_id']);?> of <?=getPlanetnameById($rec_inc['target_id']);?></a>)</td>
                                                        	<td <?if ($tdclass) echo $tdclass;?>><?=$eta;?></td>
                                                        </tr>
                                                        <?
                                                        	}
                                                        } else {
                                                        ?>
                                                        <tr>
                                                        	<td align="center" colspan="4">There are no incoming fleets for this alliance.</td>
                                                        </tr>
                                                        <?
                                                        }
                                                        ?>
                                                        <tr><td colspan="3">&nbsp;</td></tr>
                                                        <tr>
                                                        	<td height="15" align="left" colspan="3">Outgoing fleets</td>
                                                        </tr>
                                                        <tr>
                                                        	<td background="img/bg_balk.jpg" width="50"><b>Type</b></td>
                                                        	<td background="img/bg_balk.jpg" width="350"><b>From</b></td>
                                                        	<td background="img/bg_balk.jpg" width="350"><b>To</b></td>
                                                        	<td background="img/bg_balk.jpg" width="50"><b>ETA</b></td>
                                                        </tr>
                                                        <?
                                                        $sql_out = "SELECT $table[playerfleet].player_id, $table[playerfleet].target_id,
                                                        					$table[playerfleet].action, $table[playerfleet].action_start
                                                        			FROM $table[playerfleet]
                                                        			INNER JOIN $table[players] ON $table[players].id = $table[playerfleet].player_id
                                                        			WHERE $table[players].alliance_id = '$playerdata[alliance_id]'
                                                        				AND ($table[playerfleet].action = 'defend' OR $table[playerfleet].action = 'attack')
                                                        			ORDER BY `action_start`
                                                        ";
                                                        $res_out = mysql_query($sql_out) or die(mysql_error());
                                                        $num_out = @mysql_num_rows($res_out);
                                                        if ($num_out > 0) {
                                                        	while($rec_out = mysql_fetch_assoc($res_out)) {
                                                        		$from_xyz = getXYZ($rec_out['player_id']);
                                                        		$to_xyz = getXYZ($rec_out['target_id']);
                                                        		$eta = $rec_out['action_start'] - getCurrentTick();
                                                        		if ($eta < 0) { $eta = 0; }
                                                        		if ($rec_out['action'] == 'attack') { $tdclass = 'class="hostile"'; }
                                                        		if ($rec_out['action'] == 'defend') { $tdclass = 'class="friendly"'; }
                                                        ?>
                                                        <tr>
                                                        	<td <?if ($tdclass) echo $tdclass;?>><?if ($rec_out['action'] == 'attack') echo 'Hostile'; elseif ($rec_out['action'] == 'defend') echo 'Friendly'; ?></td>
                                                        	<td <?if ($tdclass) echo $tdclass;?>><a href="main.php?mod=galaxy&act=view&x=<?=$from_xyz[0]?>&y=<?=$from_xyz[1]?>"><?echo $from_xyz[0].':'.$from_xyz[1].':'.$from_xyz[2];?></a> (<a href="main.php?mod=main&act=mail&do=compose&x=<?=$from_xyz[0];?>&y=<?=$from_xyz[1];?>&z=<?=$from_xyz[2];?>"><?=getRulernameById($rec_out['player_id']);?> of <?=getPlanetnameById($rec_out['player_id']);?></a>)</td>
                                                        	<td <?if ($tdclass) echo $tdclass;?>><a href="main.php?mod=galaxy&act=view&x=<?=$to_xyz[0]?>&y=<?=$to_xyz[1]?>"><?echo $to_xyz[0].':'.$to_xyz[1].':'.$to_xyz[2];?></a> (<a href="main.php?mod=main&act=mail&do=compose&x=<?=$to_xyz[0];?>&y=<?=$to_xyz[1];?>&z=<?=$to_xyz[2];?>"><?=getRulernameById($rec_out['target_id']);?> of <?=getPlanetnameById($rec_out['target_id']);?></a>)</td>
                                                        	<td <?if ($tdclass) echo $tdclass;?>><?=$eta;?></td>
                                                        </tr>
                                                        <?
                                                        	}
                                                        } else {
                                                        ?>
                                                        <tr>
                                                        	<td align="center" colspan="4">There are no outgoing fleets for this alliance.</td>
                                                        </tr> 
                                                        <?
                                                        }
                                                } else {
                                                        ?>
                                          			<tr>
                                                   		<td align="center">You need to research Alliance Communication first.</td>
	                                              	</tr>
                                                       <?
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