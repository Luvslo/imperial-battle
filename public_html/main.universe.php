<?
if (!$user->checklogin()) {
	include('goto.login.php');
	die();
}
?>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Universe - Players</td>
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
                                                                <td width="50" background="img/bg_balk.jpg"><b>Rank</b></td>
                                                                <td width="75" background="img/bg_balk.jpg" align="center"><b>Coordinates</b></td>
                                                                <td width="75" background="img/bg_balk.jpg" align="center"><b>Alliance</b></td>
                                                                <td width="450" background="img/bg_balk.jpg" align="center"><b>Ruler- & planetname</b></td>
                                                                <td width="100" background="img/bg_balk.jpg"><b>Score</b></td>
                                                                <td width="50" background="img/bg_balk.jpg"><b>Asteroids</b></td>
                                                        </tr>
                                                        <?
                                                        $sql_getuni = "SELECT
                                                        					$table[universe].id, $table[universe].player_id, $table[universe].tag, $table[universe].score, $table[universe].asteroids,
                                                        					$table[players].rulername, $table[players].planetname
                                                        				FROM $table[universe]
                                                        				INNER JOIN $table[players] ON $table[universe].player_id = $table[players].id
                                                        				ORDER BY $table[universe].id";
                                                        $res_getuni = mysql_query($sql_getuni);
                                                        while ($rec_getuni = mysql_fetch_array($res_getuni)) {
                                                        ?>
                                                        <tr>
                                                                <td><?echo $rec_getuni['id']; ?></td>
                                                                <td align="center"><? $xyz = getXYZ(getIdByRulername($rec_getuni['rulername']));?><a href="main.php?mod=galaxy&act=view&x=<?=$xyz[0]?>&y=<?=$xyz[1]?>"><? echo $xyz[0].':'.$xyz[1].':'.$xyz[2]; ?></a></td>
                                                                <td align="center"><?echo stripslashes($rec_getuni['tag']); ?></td>
                                                                <td align="center"><a href="main.php?mod=main&act=mail&do=compose&x=<?=$xyz[0];?>&y=<?=$xyz[1];?>&z=<?=$xyz[2];?>"><?=stripslashes($rec_getuni['rulername']); ?> of <?=stripslashes($rec_getuni['planetname']);?></a></td>
                                                                <td><?echo parseInteger($rec_getuni['score']); ?></td>
                                                                <td><?echo parseInteger($rec_getuni['asteroids']); ?></td>
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
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Universe - Galaxies</td>
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
                                                                <td width="50" background="img/bg_balk.jpg"><b>Rank</b></td>
                                                                <td width="75" background="img/bg_balk.jpg" align="center"><b>Coordinates</b></td>
                                                                <td width="500" background="img/bg_balk.jpg"><b>Galaxy topic</b></td>
                                                                <td width="100" background="img/bg_balk.jpg"><b>Score</b></td>
                                                                <td width="75" background="img/bg_balk.jpg"><b>Asteroids</b></td>
                                                        </tr>
                                                        <?
                                                        $sql_galaxies = "SELECT $table[universe_galaxy].id AS id, $table[universe_galaxy].total_members AS total_members, $table[universe_galaxy].score AS score, $table[universe_galaxy].asteroids AS asteroids,
                                                        				$table[galaxy].x AS x, $table[galaxy].y AS y, $table[galaxy].topic FROM $table[universe_galaxy] INNER JOIN $table[galaxy]
                                                        				WHERE $table[galaxy].id = $table[universe_galaxy].galaxy_id ORDER BY $table[universe_galaxy].id";
                                                        $res_galaxies = mysql_query($sql_galaxies);
                                                        while ($rec_galaxies = mysql_fetch_array($res_galaxies)) {
                                                        ?>
                                                        <tr>
                                                                <td><?echo $rec_galaxies['id'];?></td>
                                                                <td align="center"><a href="main.php?mod=galaxy&act=view&x=<?echo $rec_galaxies['x'];?>&y=<?echo $rec_galaxies['y'];?>"><?echo $rec_galaxies['x'].':'.$rec_galaxies['y'];?></a></td>
                                                                <td><a href="main.php?mod=galaxy&act=view&x=<?echo $rec_galaxies['x'];?>&y=<?echo $rec_galaxies['y'];?>"><?echo stripslashes($rec_galaxies['topic']);?></a></td>
                                                                <td><?echo parseInteger($rec_galaxies['score']);?></td>
                                                                <td><?echo parseInteger($rec_galaxies['asteroids']);?></td>
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
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr>
                <td width="4" valign="bottom"><img border="0" src="img/border/L_B.gif" width="20" height="15"></td>
                <td width="180">Universe - Alliances</td>
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
                                                                <td width="50" background="img/bg_balk.jpg"><b>Rank</b></td>
                                                                <td width="100" background="img/bg_balk.jpg" align="center"><b>Tag</b></td>
                                                                <td width="225" background="img/bg_balk.jpg"><b>Full name</b></td>
                                                                <td width="100" background="img/bg_balk.jpg"><b>Total members</b></td>
                                                                <td width="125" background="img/bg_balk.jpg"><b>Average score</b></td>
                                                                <td width="125" background="img/bg_balk.jpg"><b>Score</b></td>
                                                                <td width="75" background="img/bg_balk.jpg"><b>Asteroids</b></td>
                                                        </tr>
                                                        <?
                                                        $sql_unialliance = "SELECT id, tag, name, total_members, score, asteroids FROM $table[universe_alliance]";
                                                        $res_unialliance = mysql_query($sql_unialliance);
                                                        while ($rec_unialliance = mysql_fetch_array($res_unialliance)) {
                                                        ?>
                                                        <tr>
                                                                <td><?echo $rec_unialliance['id'];?></td>
                                                                <td align="center"><?echo $rec_unialliance['tag'];?></td>
                                                                <td><?echo $rec_unialliance['name'];?></td>
                                                                <td><?echo $rec_unialliance['total_members'];?></td>
                                                                <td><?echo parseInteger(round($rec_unialliance['score'] / $rec_unialliance['total_members']));?></td>
                                                                <td><?echo parseInteger($rec_unialliance['score']);?></td>
                                                                <td><?echo parseInteger($rec_unialliance['asteroids']);?></td>
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