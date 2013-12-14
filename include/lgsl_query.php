<?php
/* Part of lgsl! */
function query_live($type, $ip, $c_port, $q_port, $s_port, $request)
{
	$server = array(
	"b" => array("type" => $type, "ip" => $ip, "c_port" => $c_port, "q_port" => $q_port, "s_port" => $s_port, "status" => 1),
	"s" => array("game" => "", "name" => "", "map" => "", "players" => 0, "playersmax" => 0, "password" => ""),
	"e" => array(),
	"p" => array());
	
	$response = query_direct($server, $request, "query_hl1", "udp");
	
	if (!$response)
	{
		$server['b']['status'] = 0;
	}
	else
	{
		// FILL IN EMPTY VALUES
		if (empty($server['s']['game'])) { $server['s']['game'] = "-"; }
		if (empty($server['s']['map']))  { $server['s']['map']  = "-"; }
		
		// PLAYER COUNT AND PASSWORD STATUS SHOULD BE NUMERIC
		$server['s']['players']    = intval($server['s']['players']);
		$server['s']['playersmax'] = intval($server['s']['playersmax']);
		
		// REMOVE EMPTY AND UN-REQUESTED ARRAYS
		if (strpos($request, "p") === FALSE && empty($server['p']) && $server['s']['players'] != 0) { unset($server['p']); }
		if (strpos($request, "t") === FALSE && empty($server['t'])) { unset($server['t']); }
		if (strpos($request, "e") === FALSE && empty($server['e'])) { unset($server['e']); }
		if (strpos($request, "s") === FALSE && empty($server['s']['name'])) { unset($server['s']); }
	}
	return $server;
}

//------------------------------------------------------------------------------------------------------------+

function query_direct(&$server, $request, $lgsl_function, $scheme)
{
	$lgsl_fp = @fsockopen("{$scheme}://{$server['b']['ip']}", $server['b']['q_port'], $errno, $errstr, 1);
	
	if (!$lgsl_fp) { return FALSE; }
	
	//---------------------------------------------------------+
	
	if(!defined('LGSL_TIMEOUT')) { define('LGSL_TIMEOUT', 5); }
	
	stream_set_timeout($lgsl_fp, LGSL_TIMEOUT, LGSL_TIMEOUT ? 0 : 500000);
	stream_set_blocking($lgsl_fp, TRUE);
	
	$lgsl_need	= array();
	$lgsl_need['s']	= strpos($request, "s") !== FALSE ? TRUE : FALSE;
	$lgsl_need['e']	= strpos($request, "e") !== FALSE ? TRUE : FALSE;
	$lgsl_need['p']	= strpos($request, "p") !== FALSE ? TRUE : FALSE;
	
	if ($lgsl_need['e'] && !$lgsl_need['s']) { $lgsl_need['s'] = TRUE; }
	
	do
	{
		$lgsl_need_check = $lgsl_need;
		$response = call_user_func_array($lgsl_function, array(&$server, &$lgsl_need, &$lgsl_fp));
		if (!$response) { break; }
		if ($lgsl_need_check == $lgsl_need) { break; }
		
		// OPTIMIZATION THAT SKIPS REQUEST FOR PLAYER DETAILS WHEN THE SERVER IS KNOWN TO BE EMPTY
		if ($lgsl_need['p'] && $server['s']['players'] == "0") { $lgsl_need['p'] = FALSE; }
	}
	while ($lgsl_need['s'] == TRUE || $lgsl_need['e'] == TRUE || $lgsl_need['p'] == TRUE);
	
	//---------------------------------------------------------+
	
	@fclose($lgsl_fp);
	return $response;
}

//------------------------------------------------------------------------------------------------------------+

function query_hl1(&$server, &$lgsl_need, &$lgsl_fp)
{
	//---------------------------------------------------------+
	//  REFERENCE: http://developer.valvesoftware.com/wiki/Server_Queries
	if ($server['b']['type'] == "halflifewon")
	{
		if	($lgsl_need['s']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFFdetails\x00"); }
		elseif	($lgsl_need['p']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFFplayers\x00"); }
		elseif	($lgsl_need['e']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFFrules\x00");   }
	}
	else
	{
		$challenge_code = isset($lgsl_need['challenge']) ? $lgsl_need['challenge'] : "\x00\x00\x00\x00";
		
		if	($lgsl_need['s']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFF\x54Source Engine Query\x00"); }
		elseif	($lgsl_need['p']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFF\x55{$challenge_code}"); }
		elseif	($lgsl_need['e']) { fwrite($lgsl_fp, "\xFF\xFF\xFF\xFF\x56{$challenge_code}"); }
	}
	
	$packet_temp  = array();
	$packet_type  = 0;
	$packet_count = 0;
	$packet_total = 4;
	
	do
	{
		$packet = fread($lgsl_fp, 4096); if (!$packet) { return FALSE; }
		
		//---------------------------------------------------------------------------------------------------------------------------------+
		// NEWER HL1 SERVERS REPLY TO A2S_INFO WITH 3 PACKETS ( HL1 FORMAT INFO, SOURCE FORMAT INFO, PLAYERS )
		// THIS DISCARDS UN-EXPECTED PACKET FORMATS ON THE GO ( AS READING IN ADVANCE CAUSES TIMEOUT DELAYS FOR OTHER SERVER VERSIONS )
		// ITS NOT PERFECT AS [s] CAN FLIP BETWEEN HL1 AND SOURCE FORMATS DEPENDING ON ARRIVAL ORDER ( MAYBE FIX WITH RETURN ON HL1 APPID )
		if     ($lgsl_need['s']) { if ($packet[4] == "D")						{ continue; } }
		elseif ($lgsl_need['p']) { if ($packet[4] == "m" || $packet[4] == "I")				{ continue; } }
		elseif ($lgsl_need['e']) { if ($packet[4] == "m" || $packet[4] == "I" || $packet[4] == "D")	{ continue; } }
		//---------------------------------------------------------------------------------------------------------------------------------+
		
		if	(substr($packet, 0,  5) == "\xFF\xFF\xFF\xFF\x41")	{ $lgsl_need['challenge'] = substr($packet, 5,  4); return TRUE; } // REPEAT WITH GIVEN CHALLENGE CODE
		elseif	(substr($packet, 0,  4) == "\xFF\xFF\xFF\xFF")		{ $packet_total = 1;                     $packet_type = 1;       } // SINGLE PACKET - HL1 OR HL2
		elseif	(substr($packet, 9,  4) == "\xFF\xFF\xFF\xFF")		{ $packet_total = ord($packet[8]) & 0xF; $packet_type = 2;       } // MULTI PACKET  - HL1 ( TOTAL IS LOWER NIBBLE OF BYTE )
		elseif	(substr($packet, 12, 4) == "\xFF\xFF\xFF\xFF")		{ $packet_total = ord($packet[8]);       $packet_type = 3;       } // MULTI PACKET  - HL2
		elseif	(substr($packet, 18, 2) == "BZ")			{ $packet_total = ord($packet[8]);       $packet_type = 4;       } // BZIP PACKET   - HL2
		
		$packet_count ++;
		$packet_temp[] = $packet;
	}
	while ($packet && $packet_count < $packet_total);
	
	if ($packet_type == 0) { return $server['s'] ? TRUE : FALSE; } // UNKNOWN RESPONSE ( SOME SERVERS ONLY SEND [s] )
	
	//---------------------------------------------------------+
	//  WITH THE TYPE WE CAN NOW SORT AND JOIN THE PACKETS IN THE CORRECT ORDER
	//  REMOVING ANY EXTRA HEADERS IN THE PROCESS
	
	$buffer = array();
	
	foreach ($packet_temp as $packet)
	{
		if	($packet_type == 1) { $packet_order = 0; }
		elseif	($packet_type == 2) { $packet_order = ord($packet[8]) >> 4; $packet = substr($packet, 9);  } // ( INDEX IS UPPER NIBBLE OF BYTE )
		elseif	($packet_type == 3) { $packet_order = ord($packet[9]);      $packet = substr($packet, 12); }
		elseif	($packet_type == 4) { $packet_order = ord($packet[9]);      $packet = substr($packet, 18); }
		
		$buffer[$packet_order] = $packet;
	}
	
	ksort($buffer);
	
	$buffer = implode("", $buffer);
	
	//---------------------------------------------------------+
	//  WITH THE PACKETS JOINED WE CAN NOW DECOMPRESS BZIP PACKETS
	//  THEN REMOVE THE STANDARD HEADER AND CHECK ITS CORRECT
	
	if ($packet_type == 4)
	{
		if (!function_exists("bzdecompress")) // REQUIRES http://php.net/bzip2
		{
			$server['e']['bzip2'] = "unavailable"; $lgsl_need['e'] = FALSE;
			return TRUE;
		}
		$buffer = bzdecompress($buffer);
	}
	
	$header = lgsl_cut_byte($buffer, 4);
	
	if ($header != "\xFF\xFF\xFF\xFF") { return FALSE; } // SOMETHING WENT WRONG
	
	//---------------------------------------------------------+
	
	$response_type = lgsl_cut_byte($buffer, 1);
	
	if ($response_type == "I") // SOURCE INFO ( HALF-LIFE 2 )
	{
		$server['e']['netcode']			= ord(lgsl_cut_byte($buffer, 1));
		$server['s']['name']			= lgsl_cut_string($buffer);
		$server['s']['map']			= lgsl_cut_string($buffer);
		$server['s']['game']			= lgsl_cut_string($buffer);
		$server['e']['description']		= lgsl_cut_string($buffer);
		$server['e']['appid']			= lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
		$server['s']['players']			= ord(lgsl_cut_byte($buffer, 1));
		$server['s']['playersmax']		= ord(lgsl_cut_byte($buffer, 1));
		$server['e']['bots']			= ord(lgsl_cut_byte($buffer, 1));
		$server['e']['dedicated']		= lgsl_cut_byte($buffer, 1);
		$server['e']['os']			= lgsl_cut_byte($buffer, 1);
		$server['s']['password']		= ord(lgsl_cut_byte($buffer, 1));
		$server['e']['anticheat']		= ord(lgsl_cut_byte($buffer, 1));
		$server['e']['version']			= lgsl_cut_string($buffer);
	}
	elseif ($response_type == "m") // HALF-LIFE 1 INFO
	{
		$server_ip				= lgsl_cut_string($buffer);
		$server['s']['name']			= lgsl_cut_string($buffer);
		$server['s']['map']			= lgsl_cut_string($buffer);
		$server['s']['game']			= lgsl_cut_string($buffer);
		$server['e']['description']		= lgsl_cut_string($buffer);
		$server['s']['players']			= ord(lgsl_cut_byte($buffer, 1));
		$server['s']['playersmax']		= ord(lgsl_cut_byte($buffer, 1));
		$server['e']['netcode']			= ord(lgsl_cut_byte($buffer, 1));
		$server['e']['dedicated']		= lgsl_cut_byte($buffer, 1);
		$server['e']['os']			= lgsl_cut_byte($buffer, 1);
		$server['s']['password']		= ord(lgsl_cut_byte($buffer, 1));
		
		if (ord(lgsl_cut_byte($buffer, 1))) // MOD FIELDS ( OFF FOR SOME HALFLIFEWON-VALVE SERVERS )
		{
			$server['e']['mod_url_info']	= lgsl_cut_string($buffer);
			$server['e']['mod_url_download']= lgsl_cut_string($buffer);
			$buffer = substr($buffer, 1);
			$server['e']['mod_version']	= lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
			$server['e']['mod_size']	= lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
			$server['e']['mod_server_side']	= ord(lgsl_cut_byte($buffer, 1));
			$server['e']['mod_custom_dll']	= ord(lgsl_cut_byte($buffer, 1));
		}
		
		$server['e']['anticheat']		= ord(lgsl_cut_byte($buffer, 1));
		$server['e']['bots']			= ord(lgsl_cut_byte($buffer, 1));
	}
	elseif ($response_type == "D") // SOURCE AND HALF-LIFE 1 PLAYERS
	{
		$returned = ord(lgsl_cut_byte($buffer, 1));
		$player_key = 0;
		
		while ($buffer)
		{
			$server['p'][$player_key]['pid']	= ord(lgsl_cut_byte($buffer, 1));
			$server['p'][$player_key]['name']	= lgsl_cut_string($buffer);
			$server['p'][$player_key]['score']	= lgsl_unpack(lgsl_cut_byte($buffer, 4), "l");
			$server['p'][$player_key]['time']	= lgsl_time(lgsl_unpack(lgsl_cut_byte($buffer, 4), "f"));
			
			$player_key ++;
		}
	}
	elseif ($response_type == "E") // SOURCE AND HALF-LIFE 1 RULES
	{
		$returned = lgsl_unpack(lgsl_cut_byte($buffer, 2), "S");
		
		while ($buffer)
		{
			$item_key			= strtolower(lgsl_cut_string($buffer));
			$item_value			= lgsl_cut_string($buffer);
			
			$server['e'][$item_key] = $item_value;
		}
	}
	
	//---------------------------------------------------------+
	
	// IF ONLY [s] WAS REQUESTED THEN REMOVE INCOMPLETE [e]
	if ($lgsl_need['s'] && !$lgsl_need['e']) { $server['e'] = array(); }
	
	if     ($lgsl_need['s']) { $lgsl_need['s'] = FALSE; }
	elseif ($lgsl_need['p']) { $lgsl_need['p'] = FALSE; }
	elseif ($lgsl_need['e']) { $lgsl_need['e'] = FALSE; }
	
	return TRUE;
}

function lgsl_cut_byte(&$buffer, $length)
{
	$string = substr($buffer, 0, $length);
	$buffer = substr($buffer, $length);
	return $string;
}

function lgsl_cut_string(&$buffer, $start_byte = 0, $end_marker = "\x00")
{
	$buffer = substr($buffer, $start_byte);
	$length = strpos($buffer, $end_marker);
	
	if ($length === FALSE) { $length = strlen($buffer); }
	
	$string = substr($buffer, 0, $length);
	$buffer = substr($buffer, $length + strlen($end_marker));
	return $string;
}

function lgsl_unpack($string, $format)
{
	list(,$string) = @unpack($format, $string);
	return $string;
}

function lgsl_sort_fields($server, $fields_show, $fields_hide, $fields_other)
{
	$fields_list = array();
	
	if (!is_array($server['p'])) { return $fields_list; }
	
	foreach ($server['p'] as $player)
	{
		foreach ($player as $field => $value)
		{
			if ($value === "") { continue; }
			if (in_array($field, $fields_list)) { continue; }
			if (in_array($field, $fields_hide)) { continue; }
			$fields_list[] = $field;
		}
	}
	
	$fields_show = array_intersect($fields_show, $fields_list);
	
	if ($fields_other == FALSE) { return $fields_show; }
	
	$fields_list = array_diff($fields_list, $fields_show);
	return array_merge($fields_show, $fields_list);
}

function lgsl_sort_players($server)
{
	global $lgsl_config;
	
	if (!is_array($server['p'])) { return $server; }
	
	if	($lgsl_config['sort']['players'] == "name")  { usort($server['p'], "lgsl_sort_players_by_name");  }
	elseif	($lgsl_config['sort']['players'] == "score") { usort($server['p'], "lgsl_sort_players_by_score"); }
	return $server;
}

function lgsl_time($seconds)
{
	if ($seconds === "") { return ""; }
	
	$n = $seconds < 0 ? "-" : "";
	
	$seconds = abs($seconds);
	
	$h = intval($seconds / 3600);
	$m = intval($seconds / 60  ) % 60;
	$s = intval($seconds       ) % 60;
	
	$h = str_pad($h, "2", "0", STR_PAD_LEFT);
	$m = str_pad($m, "2", "0", STR_PAD_LEFT);
	$s = str_pad($s, "2", "0", STR_PAD_LEFT);
	return "{$n}{$h}:{$m}:{$s}";
}
?>
