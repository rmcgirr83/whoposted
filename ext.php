<?php
/**
*
* @package Who posted
* @copyright (c) 2016 RMcGirr83
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*
*/

namespace rmcgirr83\whoposted;

/**
* Extension class for custom enable/disable/purge actions
*/
class ext extends \phpbb\extension\base
{
	/** @var string Require phpBB 3.2.0 */
	const PHPBB_MIN_VERSION = '3.2.0';
	/**
	 * Enable extension if phpBB version requirement is met
	 *
	 * @return bool
	 * @access public
	 */
	public function is_enableable()
	{
		$config = $this->container->get('config');

		$enableable = (phpbb_version_compare($config['version'], self::PHPBB_MIN_VERSION, '>='));
		if (!$enableable)
		{
			$language = $this->container->get('language');
			$language->add_lang('whoposted', 'rmcgirr83/whoposted');

			trigger_error($language->lang('EXTENSION_REQUIREMENTS', self::PHPBB_MIN_VERSION), E_USER_WARNING);
		}

		return $enableable;
	}
}
