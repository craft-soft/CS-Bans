<?php

// ************************************************************************
//PHPrcon - PHP script collection to remotely administrate and configure Halflife and HalflifeMod Servers through a webinterface
//Copyright (C) 2002  Henrik Beige
//
//This library is free software; you can redistribute it and/or
//modify it under the terms of the GNU Lesser General Public
//License as published by the Free Software Foundation; either
//version 2.1 of the License, or (at your option) any later version.
//
//This library is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
//Lesser General Public License for more details.
//
//You should have received a copy of the GNU Lesser General Public
//License along with this library; if not, write to the Free Software
//Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
// ************************************************************************
//
// 2009 by |PJ|ShOrTy
//		fixed protocol since HL1 Update 2008
//		fixed multible packet handling
//		added special public functions to communicate with amxbans plugin
//
//
class Rcon
{
  var $connected = false;
  var $challenge_number;
  var $server_ip;
  var $server_password;
  var $server_port;
  var $socket;


  //Constructor
  public function Rcon()
  {
    $this->challenge_number = 0;
    $this->connected = false;
    $this->server_ip = "";
    $this->server_port = "";
    $this->server_password = "";
  }


  //Open socket to gameserver
  public function Connect($server_ip, $server_port, $server_password = "")
  {
    //store server data
    $this->server_ip = gethostbyname($server_ip);
    $this->server_port = $server_port;
    $this->server_password = $server_password;

    //open connection to gameserver
    $fp = fsockopen("udp://" . $this->server_ip, $this->server_port, $errno, $errstr, 1);
    stream_set_timeout($fp, 1);

    if($fp)
      $this->connected = true;
    else
    {
      $this->connected = false;
      return false;
    }

    //store socket
    $this->socket = $fp;

    //return success
    return true;

  } //public function Connect($server_ip, $server_port, $server_password = "")


  //Close socket to gameserver
  public function Disconnect()
  {
    //close socket
    @fclose($this->socket);
    $connected = false;

  } //public function Disconnect()


  //Is there an open connection
  public function IsConnected()
  {
    return $this->connected;
  } //public function IsConnected()

  private function get_challenge() {
	  if($this->challenge_number == "")
		{
		  //send request of challenge number
		  $challenge = "\xff\xff\xff\xffchallenge rcon\n";
		  $buffer = $this->Communicate($challenge);

		  //If no connection is open
		  if(trim($buffer) == "")
		  {
			$this->connected = false;
			return false;
		  }
		  //get challenge number
		  $this->challenge_number = trim(substr($buffer,15));
		}
  }
  //Get detailed player info via rcon
  public function ServerInfo()
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;

    //get server information
    $status = $this->RconCommand("status");

    //If there is no open connection return false
    //If there is bad rcon password return "Bad rcon_password."
    if(!$status || trim($status) == "Bad rcon_password.")
      return $status;

   //format global server info
    $line = explode("\n", $status);
    $map = substr($line[3], strpos($line[3], ":") + 1);
    $players = trim(substr($line[4], strpos($line[4], ":") + 1));
    $active = explode(" ", $players);
	$result = array();
    $result["ip"] = trim(substr($line[2], strpos($line[2], ":") + 1));
    $result["name"] = trim(substr($line[0], strpos($line[0], ":") + 1));
    $result["map"] = trim(substr($map, 0, strpos($map, "at:")));
    $result["mod"] = "Counterstrike " . trim(substr($line[1], strpos($line[1], ":") + 1));
    $result["game"] = "Halflife";
    $result["activeplayers"] = $active[0];
    $result["maxplayers"] = substr($active[2], 1);

    //format player info
    for($i = 1; $i <= $result["activeplayers"]; $i++)
    {
      //get possible player line
      $tmp = $line[$i + 6];

      //break if no more players are left
      if(substr_count($tmp, "#") <= 0)
        break;

      //name
      $begin = strpos($tmp, "\"") + 1;
      $end = strrpos($tmp, "\"");
      $result[$i]["name"] = substr($tmp, $begin, $end - $begin);
      $tmp = trim(substr($tmp, $end + 1));

      //ID
      $end = strpos($tmp, " ");
      $result[$i]["id"] = substr($tmp, 0, $end);
      $tmp = trim(substr($tmp, $end));

      //WonID
      $end = strpos($tmp, " ");
      $result[$i]["wonid"] = substr($tmp, 0, $end);
      $tmp = trim(substr($tmp, $end));

      //Frag
      $end = strpos($tmp, " ");
      $result[$i]["frag"] = substr($tmp, 0, $end);
      $tmp = trim(substr($tmp, $end));

      //Time
      $end = strpos($tmp, " ");
      $result[$i]["time"] = substr($tmp, 0, $end);
      $tmp = trim(substr($tmp, $end));

      //Ping
      $end = strpos($tmp, " ");
      $result[$i]["ping"] = substr($tmp, 0, $end);
      $tmp = trim(substr($tmp, $end));

      //Loss
      $tmp = trim(substr($tmp, $end));

      //Adress
      $result[$i]["adress"] = $tmp;

    } //for($i = 1; $i < $result["activeplayers"]; $i++)

    //return formatted result
    return $result;

  } //public function ServerInfo()


  //Get all maps in all directories
  public function ServerMaps($pagenumber = 0)
  {
	  $result = array();
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;

    //Get list of maps
    $maps = $this->RconCommand("maps *", $pagenumber);

    //If there is no open connection return false
    //If there is bad rcon password return "Bad rcon_password."
    if(!$maps || trim($maps) == "Bad rcon_password.")
      return $maps;

    //Split Maplist in rows
    $line = explode("\n", $maps);
    $count = sizeof($line) - 4;

    //format maps
    for($i = 0; $i <= $count; $i++)
    {
      $text = $line[$i];

      //at directory output sorted map list
      if(strstr($text, "Dir:"))
      {
        //reset counter
        $mapcount = 0;

        //parse directory name
        $directory = strstr($text, " ");

      } //if(strstr($text, "Dir:"))

      else if(strstr($text, "(fs)"))
      {
        //parse mappath
        $mappath = strstr($text, " ");

        //parse mapname
        //if no "/" is included in the "maps * " result
        if(!($tmpmap = strrchr($mappath, "/")))
          $tmpmap = $mappath;

        //parse mapname without suffix (.bsp)
        $result[$directory][$i] = substr($tmpmap, 1, strpos($tmpmap, ".") - 1);

      } //else if(strstr($text, "(fs)"))

    } //for($i = 1; $i <= $count; $i++)


    //return formatted result
    return $result;

  } //public function ServerMaps()

  //Get server info via info protocoll
  public function Info()
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;

    //send info command
    $command = "\xff\xff\xff\xffTSource Engine Query\x00";
    $buffer = $this->Communicate($command);

    //If no connection is open
    if(trim($buffer) == "")
    {
      $this->connected = false;
      return false;
    }

    //build info array
	$pos=0;
	$result = array();
    $result["type"] = $this->parse_buffer($buffer,$pos,"bytestr");

	if ($result["type"] == 'I')
	{
		$result["version"] = $this->parse_buffer($buffer,$pos,"byte");
		$result["name"] = $this->parse_buffer($buffer,$pos,"string");
		$result["map"] = $this->parse_buffer($buffer,$pos,"string");
		$result["mod"] = $this->parse_buffer($buffer,$pos,"string");
		$result["game"] = $this->parse_buffer($buffer,$pos,"string");
		$result["appid"] = $this->parse_buffer($buffer,$pos,"short");
		$result["activeplayers"] = $this->parse_buffer($buffer,$pos,"byte");
		$result["maxplayers"] = $this->parse_buffer($buffer,$pos,"byte");
		$result["botplayers"] = $this->parse_buffer($buffer,$pos,"byte");
		$result["dedicated"] = $this->parse_buffer($buffer,$pos,"bytestr");
		$result["os"] = $this->parse_buffer($buffer,$pos,"bytestr");
		$result["password"] = $this->parse_buffer($buffer,$pos,"byte");
		$result["secure"] = $this->parse_buffer($buffer,$pos,"byte");
		$result["sversion"] = $this->parse_buffer($buffer,$pos,"string");
		$result["edf"] = $this->parse_buffer($buffer,$pos,"byte");
		switch ($result["edf"]) {
			case '\x80': // The server's game port # is included
				$result["port"]= $this->parse_buffer($buffer,$pos,"short");
				break;
			case '\x40': // The spectator port # and then the spectator server name are included
				$result["specport"]= $this->parse_buffer($buffer,$pos,"short");
				$result["specservername"] = $this->parse_buffer($buffer,$pos,"string");
				break;
			case '\x20': // The game tag data string for the server is included [future use]
				$result["gametagdata"] = $this->parse_buffer($buffer,$pos,"string");
		}
	}
	else
	{
		$result['adress'] = $this->parse_buffer($buffer,$pos,"string");
		$result['name'] = $this->parse_buffer($buffer,$pos,"string");
		$result['map'] = $this->parse_buffer($buffer,$pos,"string");
		$result['mod'] = $this->parse_buffer($buffer,$pos,"string");
		$result['game'] = $this->parse_buffer($buffer,$pos,"string");
		$result['activeplayers'] = $this->parse_buffer($buffer,$pos,"byte");
		$result['maxplayers'] = $this->parse_buffer($buffer,$pos,"byte");
		$result['protocol'] = $this->parse_buffer($buffer,$pos,"byte");
		$result['dedicated'] = $this->parse_buffer($buffer,$pos,"bytestr");
		$result['os'] = $this->parse_buffer($buffer,$pos,"bytestr");
		$result['password'] = $this->parse_buffer($buffer,$pos,"byte");
		$result['modrunning'] = $this->parse_buffer($buffer,$pos,"byte");
		$result['modurl'] = $this->parse_buffer($buffer,$pos,"string");
		$this->parse_buffer($buffer,$pos,"byte");
		$this->parse_buffer($buffer,$pos,"byte");
		$this->parse_buffer($buffer,$pos,"byte");
		$this->parse_buffer($buffer,$pos,"byte");
		$this->parse_buffer($buffer,$pos,"byte");
		$this->parse_buffer($buffer,$pos,"byte");
		$this->parse_buffer($buffer,$pos,"byte");
		$this->parse_buffer($buffer,$pos,"byte");
		$this->parse_buffer($buffer,$pos,"byte");
		$this->parse_buffer($buffer,$pos,"byte");
		$result["secure"] = $this->parse_buffer($buffer,$pos,"byte");
		$result["botplayers"] = $this->parse_buffer($buffer,$pos,"byte");
	}
	//$this->Communicate("");
    //return formatted result
    return $result;

  } //public function Info()

	public function parse_buffer($buffer,&$pos,$type) {
		$result = '';
		switch ($type) {
			case 'string':
					while ( substr($buffer, $pos,1) !== "\x00" )
					{
						$result .= substr($buffer, $pos,1);
						$pos++;
					}
					break;
			case 'short':
					$result = ord(substr($buffer, $pos,1)) +
							(ord(substr($buffer, $pos+1,1)) << 8);
					$pos++;
					break;
			case 'long':
					 $result = ord($buffer[$pos]) +
                            (ord($buffer[$pos + 1]) << 8) +
                            (ord($buffer[$pos + 2]) << 16) +
                            (ord($buffer[$pos + 3]) << 24);
					$pos+=3;
					break;
			case 'byte':
					$result = ord(substr($buffer, $pos,1));
					break;
			case 'bytestr':
					$result = substr($buffer, $pos,1);
					break;
			case 'float':
					$tmptime = @unpack('ftime', substr($buffer, $pos, 4));
					$result = date('H:i:s', round($tmptime['time'], 0) + 82800);
					$pos+=3;
					break;
		}
		$pos++;
		return $result;
	}

  //Get players via info protocoll
  public function Players()
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;
	//get challenge number
    if($this->challenge_number == "")
    {
      //send request of challenge number
      $challenge = "\xff\xff\xff\xff\x55\xff\xff\xff\xff";
      $buffer = $this->Communicate($challenge);

      //If no connection is open
      if(trim($buffer) == "")
      {
        $this->connected = false;
        return false;
      }

      //get challenge number
      $this->challenge_number = substr($buffer,1,4);
    }
    //send players command
    $command = "\xff\xff\xff\xff\x55$this->challenge_number";
    $buffer = $this->Communicate($command);

    //If no connection is open
    if(trim($buffer) == "")
    {
      $this->connected = false;
      return false;
    }
    //get number of online players
    #$buffer = substr($buffer, 1);
	$pos=0;
	$header = $this->parse_buffer($buffer,$pos,"bytestr");
	$numpl = $this->parse_buffer($buffer,$pos,"byte");

	//build players array
	if($header!='D') return;
	$result=array();
	for($i = 0; $i < $numpl; $i++)
	{
		$result[$i]["index"] = $this->parse_buffer($buffer,$pos,"byte");
		$result[$i]["name"] = $this->parse_buffer($buffer,$pos,"string");
		$result[$i]["frag"] = $this->parse_buffer($buffer,$pos,"long");
		$result[$i]["time"] = $this->parse_buffer($buffer,$pos,"float");
	}

    //return formatted result
    return $result;

  } //public function Players()


  //Get server rules via info protocoll
  public function ServerRules()
  {
	  $result = array();
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;


	//$this->Communicate("");


	if($this->challenge_number == "")
    {
      //send request of challenge number
      $challenge = "\xff\xff\xff\xff\x56\xff\xff\xff\xff";
      $buffer = $this->Communicate($challenge);

      //If no connection is open
      if(trim($buffer) == "")
      {
        $this->connected = false;
        return false;
      }

      //get challenge number
      $this->challenge_number = substr($buffer,1,4);
    }
    //build info command
    $command = "\xff\xff\xff\xff\x56$this->challenge_number\x00";
    $buffer = $this->Communicate($command);

    //If no connection is open
    if(trim($buffer) == "")
    {
      $this->connected = false;
      return false;
    }

    //seperate rules
    $buffer = substr($buffer, 2);
    $buffer = explode("\x00", $buffer);
    $buffer_count = floor(sizeof($buffer) / 2);

    //build rules array
    for($i = 0; $i < $buffer_count; $i++)
    {
      $result[$buffer[2 * $i]] = $buffer[2 * $i + 1];

    }
    //sort rules
    #asort($result);

    //return formatted result
    return $result;

  } //public function ServerRules()


  //Execute rcon command on open socket $fp
  public function RconCommand($command, $pagenumber = 0, $single = true)
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;

    //get challenge number
	$this->get_challenge();

    $command = "\xff\xff\xff\xffrcon $this->challenge_number \"$this->server_password\" $command\n";

    //get specified page
    $result = "";
    $buffer = "";
    while($pagenumber >= 0)
    {
      //send rcon command
      $buffer .= substr($this->Communicate($command),1);

      //get only one package
      if($single == true)
        $result = $buffer;

      //get more then one package and put them together
      else
        $result .= $buffer;

      //clear command for higher iterations
      $command = "";

      $pagenumber--;

    } //while($pagenumber >= 0)

	//to get more than 1 page from rcon

	//write command on socket
    // // // // if($command != "")
      // // // // fputs($this->socket, $command, strlen($command));

    // // // // //get results from server
    // // // // $buffer = fread ($this->socket, 1);
    // // // // $status = socket_get_status($this->socket);

    // // // // // Sander's fix:
    // // // // while ($status["unread_bytes"] > 0 && $timeout < 10) {
		// // // // //get results from server
    	// // // // $buffer .= fread($this->socket, $status["unread_bytes"]);
		// // // // $result .= substr($buffer,5);
		// // // // //echo "######".substr($buffer,20)."#########<br>";
		// // // // $buffer = fread ($this->socket, 1);
		// // // // $status = socket_get_status($this->socket);
		// // // // //echo $status["unread_bytes"];
		// // // // $timeout++;
		// // // // //echo $timeout;

    // // // // }
	//echo $buffer;

    //return unformatted result
    return trim($result);

  } //public function RconCommand ($command)

  //AMXBans public function to get the online players with more infos
  public function ServerPlayers()
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;

    //get challenge number
	$this->get_challenge();

    //get specified page
    $result = "";
    $buffer = "";

	//write command on socket
	$command="\xff\xff\xff\xffrcon $this->challenge_number \"$this->server_password\" amx_list\n";
    fputs($this->socket, $command, strlen($command));

    //get first results from server
    $buffer = fread ($this->socket, 1);
    $status = socket_get_status($this->socket);

	$max=0;
	//try to get more results
    while ($status["unread_bytes"] > 0 && $max <= 2) {
		//get results from server
		$end="\xfb\xfb\xfb\xfb";
    	$buffer .= fread($this->socket, $status["unread_bytes"]);
		$result .= substr($buffer,5);

		//search for the last packet from plugin
		if(stristr($buffer,$end)!==false) {
			$result=str_replace($end,"",$result);
			break;
		}

		//get new socket status
		$buffer = fread ($this->socket, 1);
		$status = socket_get_status($this->socket);
		$max++;
    }

    //return unformatted result
    return trim($result);

  } //public function RconCommand ($command)

  //Communication between PHPrcon and the Gameserver
  public function Communicate($command)
  {
    //If there is no open connection return false
    if(!$this->connected)
      return $this->connected;

	// read all pending packets before sending a request
	do {
		$rfds = array($this->socket);
		$wfds = NULL;
		$efds = NULL;
		$num_changed_sockets = @stream_select($rfds, $wfds, $efds, 0);

		if ($num_changed_sockets === false) {
			break;
		} else if ($num_changed_sockets === 0) {
			break;
		} else {
			$buffer = stream_socket_recvfrom($this->socket, 65536);
		}
	} while (true);


    //write command on socket
    if($command != "")
      fputs($this->socket, $command, strlen($command));

    //get results from server
    $buffer = fread ($this->socket, 1);
    $status = socket_get_status($this->socket);

    // Sander's fix:
    if ($status["unread_bytes"] > 0) {
    	$buffer .= fread($this->socket, $status["unread_bytes"]);
    }

    //If there is another package waiting
    if(substr($buffer, 0, 4) == "\xfe\xff\xff\xff")
    {
		//get requestid from split packages
		$requestid=substr($buffer,4,4);

		//get number of packages
		$po=ord(substr($buffer,8,1));
		$panum=($po & 1) + ($po & 2) + ($po & 4) + ($po & 8);

		//get number from current package
		$po=$po >> 4;
		$pacur=($po & 1) + ($po & 2) + ($po & 4) + ($po & 8);

		//add the first package to the array
		if($pacur==($panum-1)) {
			$splitbuffer[$pacur]=substr($buffer,9);
		} else {
			$splitbuffer[$pacur]=substr($buffer,14);
		}

		//get all missing packages, the fist one we have, so start with 1
		for ($i=1;$i<$panum;$i++) {
			//get next package
			$buffer2 = fread ($this->socket, 1);
			$status = socket_get_status($this->socket);
			$buffer2 .= fread($this->socket, $status["unread_bytes"]);

			//get number from current package
			$requestid2=substr($buffer,4,4);
			$po=ord(substr($buffer2,8,1));
			$po=$po >> 4;
			$pacur=($po & 1) + ($po & 2) + ($po & 4) + ($po & 8);

			//check the requestid from every package and add to array
			if($requestid==$requestid2) {
				if($pacur==($panum-1)) {
					$splitbuffer[$pacur]=substr($buffer2,9);
				} else {
					$splitbuffer[$pacur]=substr($buffer2,14);
				}
			}
		}
		//add to main packet, the array is ordered by package num
		$bufferret = '';
		for($i=0;$i<$panum;$i++) $bufferret.=$splitbuffer[$i];
    }

    //In case there is only one package
    else
      $bufferret = substr($buffer, 4);

    //return complete package including the type byte
    return $bufferret;

  } //public function Communicate($buffer)

}

?>
