<?php
/*
 * example.php - DT IRC Bot
 * This contains an example bot for DTBot.
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
 
require_once("./dtbot.php");
$bot = new IRCBot();
$bot->registerScript("./example-lib.php");
$bot->setBot("DBot_", "DBot__", "DBot2", "DBot PHP Test");
$bot->setNetwork("irc.onlinegamesnet.net");
echo $bot->start();
unset($bot);

?>