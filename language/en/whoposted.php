<?php
/**
*
* @package Who posted
* @copyright (c) 2016 RMcGirr83
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'WHOPOSTED_TITLE'	=> 'Who posted?',
	'WHOPOSTED_EXP'		=> 'This is a list of all members who posted in this topic',
	'WHOPOSTED_SHOW'	=> 'Show topic',
	'AND_MORE_USERS'				=> array(
		1 => 'and %s more user',
		2 => 'and %s more users',
	),
));
