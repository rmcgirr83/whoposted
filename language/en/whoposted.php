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
	//Donation
	'PAYPAL_IMAGE_URL'          => 'https://www.paypalobjects.com/webstatic/en_US/i/btn/png/silver-pill-paypal-26px.png',
	'PAYPAL_ALT'                => 'Donate using PayPal',
	'BUY_ME_A_BEER_URL'         => 'https://paypal.me/RMcGirr83',
	'BUY_ME_A_BEER'				=> 'Buy me a beer for creating this extension',
	'BUY ME A BEER_SHORT'		=> 'Make a donation for this extension',
	'BUY ME A BEER_EXPLAIN'		=> 'This extension is completely free. It is a project that I spend my time on for the enjoyment and use of the phpBB community. If you enjoy using this extension, or if it has benefited your forum, please consider %1$sbuying me a beer%2$s. It would be greatly appreciated. <i class="fa fa-smile-o" style="color:green;font-size:1.5em;" aria-hidden="true"></i>',
]);
