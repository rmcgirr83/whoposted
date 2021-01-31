<?php
/**
*
* @package Who posted
* @copyright (c) 2016 RMcGirr83
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*
*/

namespace rmcgirr83\whoposted\event;

/**
* @ignore
*/
use phpbb\controller\helper;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var helper $helper */
	protected $helper;

	/** @var language $language */
	protected $language;

	/** @var template $template */
	protected $template;

	/** @var user $user */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param  helper			$helper			Helper object
	 * @param  language			$language		Language object
	 * @param  template			$template		Template object
	 * @param  user				$user			User object
	 * @return void
	 * @access public
	 */
	public function __construct(helper $helper, language $language, template $template, user $user)
	{
		$this->helper = $helper;
		$this->language = $language;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_extensions_run_action_after'	=>	'acp_extensions_run_action_after',
			'core.user_setup_after'						=> 'add_lang',
			'core.viewforum_topic_row_after'			=> 'modify_replies',
			'core.search_modify_tpl_ary'				=> 'modify_search_replies',
			//for recent topics extension
			'paybas.recenttopics.modify_tpl_ary'		=> 'modify_replies_recenttopics',

		);
	}

	/* Display additional metdate in extension details
	*
	* @param $event			event object
	* @param return null
	* @access public
	*/
	public function acp_extensions_run_action_after($event)
	{
		if ($event['ext_name'] == 'rmcgirr83/whoposted' && $event['action'] == 'details')
		{
			$this->language->add_lang('whoposted', $event['ext_name']);
			$this->template->assign_var('S_BUY_ME_A_BEER_WHOPOSTED', true);
		}
	}

	/**
	* @event core.user_setup
	*
	* @param \phpbb\event\data		$event		The event object
	* @return 			void
	* @access public
	*/
	public function add_lang($event)
	{
		if (!$this->user->data['is_bot'])
		{
			$this->language->add_lang('whoposted', 'rmcgirr83/whoposted');
		}
	}

	/**
	 * @event core.viewforum_topic_row_after
	 *
	 * @param event		$event		The event object
	 * @return void
	 * @access public
	 */
	public function modify_replies($event)
	{
		// alter replies in topic_row event
		// using different event due to https://github.com/phpbb/phpbb/pull/5291
		$topic_row = $event['topic_row'];

		if (!empty($topic_row['REPLIES']))
		{
			$topic_id = $topic_row['TOPIC_ID'];
			$forum_id = $topic_row['FORUM_ID'];

			$topic_row['REPLIES'] =  $this->whoposted_url($forum_id, $topic_id, $topic_row['REPLIES']);

			$this->template->alter_block_array('topicrow', $topic_row, true, $mode = 'change');
		}
	}

	/**
	 * @event 'core.search_modify_tpl_ary'
	 *
	 * @param \phpbb\event\data		$event		The event object
	 * @return void
	 * @access public
	 */
	public function modify_search_replies($event)
	{
		if (!$this->user->data['is_bot'] && $event['tpl_ary']['TOPIC_REPLIES'])
		{
			$topic_row = $event['tpl_ary'];

			$topic_id = $topic_row['TOPIC_ID'];
			$forum_id = $topic_row['FORUM_ID'];

			$topic_row['TOPIC_REPLIES'] =  $this->whoposted_url($forum_id, $topic_id, $topic_row['TOPIC_REPLIES']);

			$event['tpl_ary'] = $topic_row;
		}
	}

	/**
	 * @event 'paybas.recenttopics.modify_tpl_ary'
	 *
	 * @param \phpbb\event\data		$event		The event object
	 * @return void
	 * @access public
	 */
	public function modify_replies_recenttopics($event)
	{
		if (!$this->user->data['is_bot'] && $event['tpl_ary']['REPLIES'])
		{
			$topic_row = $event['tpl_ary'];

			$topic_id = $topic_row['TOPIC_ID'];
			$forum_id = $topic_row['FORUM_ID'];

			$topic_row['REPLIES'] =  $this->whoposted_url($forum_id, $topic_id, $topic_row['REPLIES']);

			$event['tpl_ary'] = $topic_row;
		}
	}

	/**
	 * Generate a url from the params
	 *
	 * @param	int		forum_id		The forum id
	 * @param	int		topic_id		The topic id
	 * @param	int		replies			The number of replies
	 * @access private
	 * @return string
	 */
	private function whoposted_url($forum_id = 0, $topic_id = 0, $replies = 0)
	{
		$whoposted_url = $this->helper->route('rmcgirr83_whoposted_core_whoposted', ['forum_id' => $forum_id, 'topic_id' => $topic_id]);

		return '<a href="' . $whoposted_url . '" title="' . $this->language->lang('VIEW_WHOPOSTED') . '" data-ajax="who_posted">' . $replies . '</a>';

	}
}
