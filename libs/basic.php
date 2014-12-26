<?php
/*
 * libs/basic.php - DT IRC Bot
 * This contains the basic function library.
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
 
function jtrim($string) {
	$search = array('\n', '\r');
	$replace = array('', '');
	return str_replace($search, $replace, $string);
}

// This is from the nice guys at php.net ^^. This is only for non-POSIX systems.

if (!function_exists('fnmatch')) { 
        define('FNM_PATHNAME', 1); 
        define('FNM_NOESCAPE', 2); 
        define('FNM_PERIOD', 4); 
        define('FNM_CASEFOLD', 16); 
        
        function fnmatch($pattern, $string, $flags = 0) { 
            return pcre_fnmatch($pattern, $string, $flags); 
        } 
} 
    
function pcre_fnmatch($pattern, $string, $flags = 0) { 
        $modifiers = null; 
        $transforms = array( 
            '\*'    => '.*', 
            '\?'    => '.', 
            '\[\!'    => '[^', 
            '\['    => '[', 
            '\]'    => ']', 
            '\.'    => '\.', 
            '\\'    => '\\\\' 
        ); 
        
        // Forward slash in string must be in pattern: 
        if ($flags & FNM_PATHNAME) { 
            $transforms['\*'] = '[^/]*'; 
        } 
        
        // Back slash should not be escaped: 
        if ($flags & FNM_NOESCAPE) { 
            unset($transforms['\\']); 
        } 
        
        // Perform case insensitive match: 
        if ($flags & FNM_CASEFOLD) { 
            $modifiers .= 'i'; 
        } 
        
        // Period at start must be the same as pattern: 
        if ($flags & FNM_PERIOD) { 
            if (strpos($string, '.') === 0 && strpos($pattern, '.') !== 0) return false; 
        } 
        
        $pattern = '#^' 
            . strtr(preg_quote($pattern, '#'), $transforms) 
            . '$#' 
            . $modifiers; 
        
        return (boolean)preg_match($pattern, $string); 
} 