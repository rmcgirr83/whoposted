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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->helper = $helper;
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
			'core.viewforum_modify_topics_data'	=> 'add_lang',
			'core.viewforum_modify_topicrow'	=> 'modify_replies',
		);
	}

	// only need this event to add our lang vars within viewforum
	public function add_lang($event)
	{
		if (!$this->user->data['is_bot'])
		{
			$this->user->add_lang_ext('rmcgirr83/whoposted', 'whoposted');
		}
	}

	public function modify_replies($event)
	{
		if (!$this->user->data['is_bot'])
		{
			$topic_row = $event['topic_row'];

			$topic_id = $topic_row['TOPIC_ID'];
			$forum_id = $topic_row['FORUM_ID'];

			$whoposted_url = $this->helper->route('rmcgirr83_whoposted_core_whoposted', array('forum_id' => $forum_id, 'topic_id' => $topic_id));

			$topic_row['REPLIES'] =  '<a href="' . $whoposted_url . '" data-ajax="who_posted.display" >' . $topic_row['REPLIES'] . '</a>';

			$event['topic_row'] = $topic_row;
		}
	}
}
