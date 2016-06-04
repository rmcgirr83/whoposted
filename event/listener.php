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
	/** @var \phpbb\content_visibility */
	protected $content_visibility;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\user $user, \phpbb\template\template $template
	*/
	public function __construct(\phpbb\content_visibility $content_visibility, \phpbb\controller\helper $helper, \phpbb\user $user)
	{
		$this->content_visibility = $content_visibility;
		$this->helper = $helper;
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
			'core.viewforum_modify_topicrow'	=> 'modify_replies',
		);
	}
	/**
	 * Changes the regex replacement for second pass
	 *
	 * @param object $event
	 * @return null
	 * @access public
	 */
	public function modify_replies($event)
	{
		if (!$this->user->data['is_bot'])
		{
			$topic_row = $event['topic_row'];

			$topic_id = $topic_row['TOPIC_ID'];
			$forum_id = $topic_row['FORUM_ID'];

			$whoposted_url = $this->helper->route('rmcgirr83_whoposted_core_whoposted', array('forum_id' => $forum_id, 'topic_id' => $topic_id));
			$topic_row['REPLIES'] =  '<a href=' . $whoposted_url . ' onclick=\'window.open(this.href,"","statusbar=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,width=725,height=300"); return false;\'>' . $topic_row['REPLIES'] . '</a>';
			$event['topic_row'] = $topic_row;
		}
	}
}
