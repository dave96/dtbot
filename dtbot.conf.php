<?php
/*
 * dtbot.conf.php - DT IRC Bot
 * This contains the default config for DTBot.
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
 
/* Some definitions. This WILL work in the default package, but you
 * may need to change it if you are editing the code */

define('LIBPATH', 'libs/');
define('DEBUG_MODE', false);
define('LOGFILE', 'log.txt');

// The bot's default nick and username

define('DEFAULT_USERNAME', 'dtbot');
define('DEFAULT_NICK', 'DTBot');
define('DEFAULT_REALNAME', 'DTBot PHP Bot');

// Connection default port

define('DEFAULT_PORT', 6667);

// Connection timeout when connecting an IRC Network

define('NETWORK_TIMEOUT', 30);
?>