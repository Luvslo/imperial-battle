<?

function getForumPath($forumtype, $pathtype = 1, $thread_id = 0, $forum_id = 0) {
	global $table;
	$returnstr = '';
	$str_seperator = ' > ';
	$str_index = '<a href="main.php?mod='.$forumtype.'&act=forum"><u>'.strtoupper(substr($forumtype, 0 , 1)).substr($forumtype, 1, (strlen($forumtype) - 1)).' forum</u></a>';
	$str_thread = '<a href="main.php?mod='.$forumtype.'&act=forum&subact=viewthread&thread_id='.$thread_id.'"><u>'.getThreadTopic($forumtype, $thread_id).'</u></a>';
	switch ($pathtype) {
		case 1:
		$returnstr = $str_index;
		break;
		case 2:
		$returnstr = $str_index.$str_seperator.$str_thread;
		break;
	}
	return $returnstr;
}
function getThreadTopic($forumtype, $thread_id) {
	global $table;
	switch ($forumtype) {
		case 'galaxy':
		$thread_tbl = $table['galaxyforum_threads'];
		break;
		case 'alliance':
		$thread_tbl = $table['allianceforum_threads'];
		break;
	}
	$sql = "SELECT `subject` FROM $thread_tbl WHERE `id` = '$thread_id'";
	$res = mysql_query($sql);
	$num = mysql_num_rows($res);
	if ($num > 0) {
		$rec = mysql_fetch_assoc($res);
		return $rec['subject'];
	}
	return '';
}

function parseBBcode($string = null, $link = null, $italic = null, $bold = null, $underline = null, $quote = null)
{
	if (isset($string))
	{
		if (isset($link))
		{
			preg_match_all("|\[URL\](.*?)\[/URL\]|", $string, $out, PREG_PATTERN_ORDER);
			$count = count($out[0]);
			for ($ip = 0; $ip != $count; $ip++){
				$url = '<a class="aforum" target="_blank" href="' . $out[1][$ip] . '">' . $out[1][$ip] . '</a>';
				$string = str_replace($out[1][$ip],$url,$string);
				$string = str_replace("[URL]","",$string);
				$string = str_replace("[/URL]","",$string);
			}
			preg_match_all("|\[url\](.*?)\[/url\]|", $string, $out, PREG_PATTERN_ORDER);
			$count = count($out[0]);
			for ($ip = 0; $ip != $count; $ip++){
				$url = '<a class="aforum" target="_blank" href="' . $out[1][$ip] . '">' . $out[1][$ip] . '</a>';
				$string = str_replace($out[1][$ip],$url,$string);
				$string = str_replace("[url]","",$string);
				$string = str_replace("[/url]","",$string);
			}
		}

		if (isset($italic))
		{
			preg_match_all("|\[I\](.*?)\[/I\]|", $string, $out, PREG_PATTERN_ORDER);
			$count = count($out[0]);
			for ($it = 0; $it != $count; $it++){
				$italic = '<I>' . $out[1][$it] . '</I>';
				$string = str_replace('[I]'.$out[1][$it].'[/I]',$italic,$string);
			}
			preg_match_all("|\[i\](.*?)\[/i\]|", $string, $out, PREG_PATTERN_ORDER);
			$count = count($out[0]);
			for ($it = 0; $it != $count; $it++){
				$italic = '<i>' . $out[1][$it] . '</i>';
				$string = str_replace('[i]'.$out[1][$it].'[/i]',$italic,$string);
			}
		}

		if (isset($bold))
		{
			preg_match_all("|\[B\](.*?)\[/B\]|", $string, $out, PREG_PATTERN_ORDER);
			$count = count($out[0]);
			for ($b = 0; $b != $count; $b++){
				$bold = '<B>' . $out[1][$b] . '</B>';
				$string = str_replace('[B]'.$out[1][$b].'[/B]',$bold,$string);
			}
			preg_match_all("|\[b\](.*?)\[/b\]|", $string, $out, PREG_PATTERN_ORDER);
			$count = count($out[0]);
			for ($b = 0; $b != $count; $b++){
				$bold = '<b>' . $out[1][$b] . '</b>';
				$string = str_replace('[b]'.$out[1][$b].'[/b]',$bold,$string);
			}
		}

		if (isset($underline))
		{
			preg_match_all("|\[u\](.*?)\[/u\]|", $string, $out, PREG_PATTERN_ORDER);
			$count = count($out[0]);
			for ($u = 0; $u != $count; $u++){
				$underline = '<u>' . $out[1][$u] . '</u>';
				$string = str_replace('[u]'.$out[1][$u].'[/u]',$underline,$string);
			}
			preg_match_all("|\[U\](.*?)\[/U\]|", $string, $out, PREG_PATTERN_ORDER);
			$count = count($out[0]);
			for ($u = 0; $u != $count; $u++){
				$underline = '<U>' . $out[1][$u] . '</U>';
				$string = str_replace('[U]'.$out[1][$u].'[/U]',$underline,$string);
			}

			if (isset($quote))
			{
				preg_match_all("|\[QUOTE\](.*?)\[/QUOTE\]|", $string, $out, PREG_PATTERN_ORDER);
				$count = count($out[0]);
				for ($q = 0; $q != $count; $q++){
					$td = '<center><table class="Forum_TDQUOTE">
			  						<tr>
			  							<td>Quote:<br><br>' . $out[1][$q] . '</td>
			  						</tr>
			  					</table></center>';
					$string = str_replace('[QUOTE]'.$out[1][$q].'[/QUOTE]',$td,$string);
				}
				preg_match_all("|\[quote\](.*?)\[/quote\]|", $string, $out, PREG_PATTERN_ORDER);
				$count = count($out[0]);
				for ($q = 0; $q != $count; $q++){
					$td = '<center><table class="forum_quote">
			  						<tr>
			  							<td><b>Quote:</b><br><br>' . $out[1][$q] . '</td>
			  						</tr>
			  					</table></center>';
					$string = str_replace('[quote]'.$out[1][$q].'[/quote]',$td,$string);
				}
			}
		}
	}
	return $string;
}
function stripBBCode($string = null)
{
	if (isset($string))
	{
		$string = str_replace("[b]", "", $string);
		$string = str_replace("[/b]", "", $string);
		$string = str_replace("[i]", "", $string);
		$string = str_replace("[/i]", "", $string);
		$string = str_replace("[u]", "", $string);
		$string = str_replace("[/u]", "", $string);
		$string = str_replace("[url]", "", $string);
		$string = str_replace("[/url]", "", $string);
		$string = str_replace("[quote]", "", $string);
		$string = str_replace("[/quote]", "", $string);
		
		$string = str_replace("[B]", "", $string);
		$string = str_replace("[/B]", "", $string);
		$string = str_replace("[I]", "", $string);
		$string = str_replace("[/I]", "", $string);
		$string = str_replace("[U]", "", $string);
		$string = str_replace("[/U]", "", $string);
		$string = str_replace("[URL]", "", $string);
		$string = str_replace("[/URL]", "", $string);
		$string = str_replace("[QUOTE]", "", $string);
		$string = str_replace("[/QUOTE]", "", $string);
	}
	return $string;
}
?>