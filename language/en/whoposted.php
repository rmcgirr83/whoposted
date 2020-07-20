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
	$lang = [];
}

$lang = array_merge($lang, [
	'WHOPOSTED_TITLE'	=> 'Who posted',
	'WHOPOSTED_USERNAME'	=> 'User Name',
	'WHOPOSTED_REPLIES'	=> [
		1 => '%d reply',
		2 => '%d replies',
	],
	'WHOPOSTED_SHOW'	=> 'Show topic',
	'AND_MORE_USERS'	=> [
		1 => 'and %s more user',
		2 => 'and %s more users',
	],
	'NEED_JS_ENABLED'	=> 'You must have javascript enabled in your browser in order to see the list.',
	'VIEW_WHOPOSTED'	=> 'View who posted in this topic',
	'EXTENSION_REQUIREMENTS'	=> 'This extension requires phpBB version %1$s.<br>Please check which version you have and update accordingly to use this extension.',
]);
