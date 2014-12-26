<?php
/*
 * dtbot.php - DT IRC Bot
 * This contains the main class IRCBot().
 * Copyright (C) 2014-2015 dtecno (dtecno.com)
 * Main developer: dave96
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
/*
 * Feel free to contact me as dave96 @ #Natasha in OnlineGamesNet.
 * Remember however I don't have any obligation to help you, as
 * this comes with NO WARRANTY at all, so please be nice with me :)
 */

// Tell PHP to report all the errors.

error_reporting (E_ALL);

// Set some values to prevent PHP from stopping the bot after a given time. It also disabes ob, to prevent ping timeouts.

ini_set('output_buffering', 0);	
ini_set('max_execution_time', 0);
set_time_limit(0);

require_once("./dtbot.conf.php");
require_once("./".LIBPATH."basic.php");
require_once("./ircnetwork.php");

// Main class, this is where the magic happens.

class IRCBot
{
	protected $continue = true;
	public $botnick, $bothost;
	protected $network;
	protected $chans = array();
	protected $users = array();
	protected $binds = array('msg' => array(), 'pub' => array(), 'notice' => array(), 'part' => array(), 'join' => array(), 'evnt' => array(), 'raw' => array());
	protected $scripts = array();

	public function __construct () {
		$this->network = new IRCNetwork();
	}
	
	protected function searchBind($kind_array, $patt, $call_func) {
		foreach ($kind_array as $key => $value) {
			if($value['patt'] == $patt && $value['func'] == $call_func) return $key;
		}
		return null;
	}
	
	public function bind($kind, $patt, $call_func) {
		$found = $this->searchBind($this->binds[$kind], $patt, $call_func);
		if (!isset($found)) {
			// That's not an already-set bind.
			$this->binds[$kind][] = array('patt' => $patt, 'func' => $call_func);
		}
	}
	
	public function registerScript($path) {
		$scripts[] = $path;
	}
	
	public function setBot($nick, $altnick, $username, $realname, $password = "NOPASS") {
		$this->network->setUser("nick", $nick);
		$this->botnick = $nick;
		$this->network->setUser("altnick", $altnick);
		$this->network->setUser("username", $username);
		$this->network->setUser("pass", $password);
		$this->network->setUser("realname", $realname);
	}
	
	public function rehash() {
		foreach ($scripts as $script) {
			include($script);
		}
	}
	
	public function setNetwork($netwaddr, $netwport = DEFAULT_PORT, $ssl = false) {
		$this->network->setNetwork("addr", $netwaddr);
		$this->network->setNetwork("port", $netwport);
		if (is_bool($ssl)) $this->network->setNetwork("ssl", $ssl);
	}
	
	protected function callBinds($kind, $string, $args) {
		// This calls all the binds with the given array of args
		if ($kind == 'msg' || $kind == 'notice' || $kind == 'pub') {
			$command = explode(" ", $string);
			$command = $command[0];
			foreach ($binds[$kind] as $act) if($act['patt'] == $command || fnmatch($act['patt'], $string)) call_user_func_array($act['func'], $args);
		} elseif ($kind == 'evnt') {
			$string = trim($string);
			foreach ($binds[$kind] as $act) if($act['patt'] == $string) call_user_func_array($act['func'], $args);
		} elseif ($kind == 'raw') {
			foreach ($binds[$kind] as $act) if($act['patt'] == $string) call_user_func_array($act['func'], $args);
		} else {
			foreach ($binds[$kind] as $act) if(fnmatch($act['patt'], $string)) call_user_func_array($act['func'], $args);
		}
	}
	
	protected function getAttrFromSource($source) {
		$source = str_replace(":", "", $source);
		return explode("!", $source);
	}
	
	public function quit($reason) {
		return $this->network->quit($reason);
	}
	
	public function putServ($string) {
		return $this->network->putServer(jtrim($string));
	}
	
	/* start() is the main loop.
	 * Returning 1 = connection error.
	 * Returning 2 = Server rejected connection.
	*/
	
	public function start() {
		//First, load the scripts.
		$this->rehash();
		$this->callBinds('evnt', 'rehash', array('rehash'));
		if(!$this->network->connect()) return 1;
		// We are connected. Hurray! Lets call the binded functions.
		$this->callBinds('evnt', 'connect-server', array('connect-server'));
		$this->continue = true;
		while ($this->continue) {
			while($line = $this->network->getLine()) {
				$line = jtrim($line);
				$args = explode(" ", $line);
				// Ping pong
				if ($args[0] == "PING") { 
					$this->network->putServerQuick("PONG ".$args[1]);
					$this->callBinds('raw', 'PING', array("PING", $args[1]));
				}
				else {
					//Not ping pong
					switch ($args[1]) {
						case 001:
							$this->botnick = $args[2];
							break;
						case 396:
							$this->bothost = $args[3];
							break;
						case "PRIVMSG":
							// First we build the array of arguments to callback.
							$attribs = $this->getAttrFromSource($args[0]);
							if (ischannel($args[2])) { 
								$attribs[2] = $args[2]; $attribs[3] = array_slice(implode(" ", array_slice($args, 3)), 1);
								$this->callBinds('pub', $attribs[3], $attribs);
							} else {
								$attribs[2] = array_slice(implode(" ", array_slice($args, 3)), 1);
								$this->callBinds('msg', $attribs[2], $attribs);
							}
							break;
					}
					if (is_numeric($args[1])) $this->callBinds('raw', $args[1], array($args[1], implode(" ", array_slice($args, 3))));
				}
			}
		}
		
		return 0;
	}
	
}

?>