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
	'WHOPOSTED_TITLE'	=> 'Ответы',
	'WHOPOSTED_EXP'		=> 'Список пользователей, комментировавших в этой теме.',
	'WHOPOSTED_SHOW'	=> 'Перейти в тему',
	'AND_MORE_USERS'				=> array(
		1 => 'и ещё %s пользователь',
		2 => 'и ещё %s пользователей',
	),
));
