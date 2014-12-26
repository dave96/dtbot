<?php
/*
 * ircnetwork.php - DT IRC Bot
 * This contains the lib class IRCNetwork().
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
 
 // Class IRCNetwork(address, network name, port, username, password, nick, altnick, ssl<bool>)
 
 class IRCNetwork
 {
	protected $network = array('addr' => null, 'name' => null, 'port' => DEFAULT_PORT, 'ssl' => false);
	protected $user = array('username' => DEFAULT_USERNAME, 'pass' => "NOPASS", 'nick' => DEFAULT_NICK, 'altnick' => DEFAULT_NICK."-", 'realname' => DEFAULT_REALNAME);
	protected $socket;
	
	public function __construct ($addr = null, $name = null, $port = null, $username = null, $pass = null, $nick = null, $altnick = null, $ssl = null) {
		// Boring variable set stuff
		if (isset($addr)) $this->network['addr'] = $addr;
		if (isset($name)) $this->network['name'] = $name;
		if (isset($port)) $this->network['port'] = $port;
		if (isset($username)) $this->user['name'] = $username;
		if (isset($pass)) $this->user['pass'] = $pass;
		if (isset($nick)) $this->user['nick'] = $nick;
		if (isset($altnick)) $this->user['altnick'] = $altnick;
		if (isset($ssl) and is_bool($ssl)) $this->network['ssl'] = $ssl;
	}
	
	public function setNetwork($attr, $value) {
		// Doesn't really matter if attr is not a valid key of the array, as we will simply not use it.
		$this->network[$attr] = $value;
		return true;
	}
	
	public function setUser($attr, $value) {
		// Doesn't really matter if attr is not a valid key of the array, as we will simply not use it.
		$this->user[$attr] = $value;
		return true;
	}
	
	public function getNetwork($attr) {
		if (isset($this->network[$attr])) return $this->network[$attr];
		else return false;
	}
	
	public function getUser($attr) {
		if (isset($this->user[$attr])) return $this->user[$attr];
		else return false;
	}
	
	public function getNetworkAll() {
		return $this->network;
	}
	
	public function getUserAll() {
		return $this->user;
	}
	
	protected function closeSocket() {
		if(isset($this->socket)) {
			fclose($this->socket);
			$this->socket = null;
			return true;
		} else {
			return false;
		}
	}
		
	protected function openSocket() {
		if(isset($this->socket)) $this->closeSocket();
		if(!isset($this->network['addr'])) return false;
		if(!$this->network['ssl']) {
			$sock = fsockopen($this->network['addr'], $this->network['port'], $errno, $errstr, NETWORK_TIMEOUT);
		} else {
			$sock = fsockopen("ssl://".$this->network['addr'], $this->network['port'], $errno, $errstr, NETWORK_TIMEOUT);
		}
		if (!$sock) {
			echo $errno." -> ".$errstr."\n";
			return false;
		}
		$this->socket = $sock;
		return true;
	}
	
	// This goes through flood protection.
	public function putServer ($wstring) {
		return $this->putServerNow($wstring);
	}
	// This doesn't
	protected function putServerNow ($wstring) {
		if(is_resource($this->socket)) {
			if(fputs($this->socket, jtrim($wstring)."\n") !== FALSE) return true;
		}
		return false;
	}
	
	/*
	 * connect() tries to connect to the IRC server.
	*/
	public function connect() {
		if(!$this->openSocket()) return false;
		// The connection to the IRC server is up. Now let's authenticate!
		if (!$this->putServerNow("PASS ".$this->user['pass'])) return false;
		if (!$this->putServerNow("NICK ".$this->user['altnick'])) return false;
		if (!$this->putServerNow("NICK ".$this->user['nick'])) return false;
		if (!$this->putServerNow("USER ".$this->user['username']." - - :".$this->user['realname'])) return false;
		return true;
		// We sent the info with no conn errors, rest is job of the bot.
	}
	
	public function quit($quitmsg) {
		if (!$this->putServerNow("QUIT :".$quitmsg)) return false;
		else {
			while(!feof($this->socket)) { }
			return $this->closeSocket();
		}
	}
	
	public function getLine() {
		if (is_resource($this->socket)) return fgets($this->socket);
		else return false;
	}
}

?>