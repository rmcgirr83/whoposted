<?php
/**
*
* @package Who posted
* @copyright (c) 2016 RMcGirr83
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*
*/

namespace rmcgirr83\whoposted\core;

/*
* @ignore
*/
use phpbb\auth\auth;
use phpbb\content_visibility;
use phpbb\db\driver\driver_interface as db;
use phpbb\language\language;
use phpbb\request\request;

use Symfony\Component\HttpFoundation\JsonResponse;
use phpbb\exception\http_exception;

class whoposted
{
	/** @var auth $auth */
	protected $auth;

	/** @var content_visibility $content_visibility */
	protected $content_visibility;

	/** @var db $db */
	protected $db;

	/** @var language $language */
	protected $language;

	/** @var request $request */
	protected $request;

	/** @var string root_path */
	protected $root_path;

	/** @var string php_ext */
	protected $php_ext;

	public function __construct(
			auth $auth,
			content_visibility $content_visibility,
			db $db,
			language $language,
			request $request,
			string $root_path,
			string $php_ext)
	{
		$this->auth = $auth;
		$this->content_visibility = $content_visibility;
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

	}

	/**
	* display the modal
	*
	* @param 	int	forum_id		The forum id
	* @param 	int	topic_id		The topic id
	* @return 	json response
	* @access 	public
	*/
	public function whoposted($forum_id, $topic_id)
	{
		if ($this->request->is_ajax())
		{
			if (!$this->auth->acl_gets('f_list', 'f_read', $forum_id))
			{
				throw new http_exception(404, 'SORRY_AUTH_READ');
			}

			$forum_id = (int) $forum_id;
			$topic_id = (int) $topic_id;

			// make sure the topic exists
			$sql = 'SELECT t.topic_id, t.topic_posts_approved, t.topic_posts_unapproved, t.topic_posts_softdeleted, t.topic_title
				FROM ' . TOPICS_TABLE . ' t
				WHERE t.topic_id = ' . (int) $topic_id . '
					AND ' . $this->content_visibility->get_visibility_sql('topic', $forum_id, 't.') . '
					AND t.forum_id = ' . (int) $forum_id;
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if (!$row['topic_id'])
			{
				throw new http_exception(404, 'NO_TOPIC');
			}

			//number of replies
			$replies = $this->content_visibility->get_count('topic_posts', $row, $forum_id) - 1;

			$topic_title = $row['topic_title'];

			// main query: select all the data for users and posts
			$sql_ary = [
				'SELECT'	=> 'COUNT(DISTINCT p.post_id) as posts, p.post_username, u.username, u.user_id, u.user_colour',
				'FROM'		=> [POSTS_TABLE	=> 'p'],
				'LEFT_JOIN'	=> [
					[
						'FROM'	=> [USERS_TABLE => 'u'],
						'ON'	=> 'p.poster_id = u.user_id',
					],
				],
				'WHERE'		=> "p.topic_id = $topic_id",
				'GROUP_BY'	=> 'u.user_id, p.post_username',
				'ORDER_BY'	=> 'posts DESC, p.post_username ASC, u.username_clean ASC',
			];

			// hide unapproved posts for users without approve permission
			if (!$this->auth->acl_get('m_approve', $forum_id))
			{
				$sql_ary['WHERE'] .= ' AND ' . $this->content_visibility->get_forums_visibility_sql('post', [$forum_id], 'p.');
			}

			$result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_ary));
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			$data = [];
			$data[] = [
				'message_title' => $this->language->lang('WHOPOSTED_TITLE') . ' - ' . $topic_title . ' - ' . $this->language->lang('WHOPOSTED_REPLIES', $replies),
			];

			$count = 0;
			$max_users_display = 40;

			if (!function_exists('get_username_string'))
			{
				include($this->root_path . 'includes/functions_content.' . $this->php_ext);
			}

			foreach ($rows as $userrow)
			{
				$username = ($this->auth->acl_get('u_viewprofile') && $userrow['user_id'] != ANONYMOUS) ? get_username_string('full', $userrow['user_id'], $userrow['username'], $userrow['user_colour']) : get_username_string('no_profile', $userrow['user_id'], $userrow['username'], $userrow['user_colour'], $userrow['post_username']);

				// Fix profile link root path by replacing relative paths with absolute board URL
				$username = $this->fix_url_path($username);

				$posts_display = ($this->auth->acl_get('u_search') && $userrow['user_id'] != ANONYMOUS) ? '<a href="' . append_sid("{$this->root_path}search.$this->php_ext", 'author_id=' . (int) $userrow['user_id'] . '&amp;t=' . (int) $topic_id) . '">' . $userrow['posts'] . '</a>' : $userrow['posts'];

				// Fix search path by replacing relative paths with absolute board URL
				$posts_display = $this->fix_url_path($posts_display);

				++$count;
				// limit the display to $max_users_display
				if ($count <= $max_users_display)
				{
					$data[] = [
						'username'	=> $username,
						'posts'		=> $posts_display,
					];
				}
			}

			$topic_link = '<a href="' . append_sid("{$this->root_path}viewtopic.$this->php_ext", "t=$topic_id" . ($forum_id ? "&amp;f=$forum_id" : '')) . '">' . $this->language->lang('WHOPOSTED_SHOW') . '</a>';
			$topic_link = $this->fix_url_path($topic_link);

			if ($count > $max_users_display)
			{
				$data[] = [
					'username'	=> $this->language->lang('AND_MORE_USERS', (int) $count - $max_users_display),
					'posts'		=> $topic_link,
				];
			}
			else
			{
				$data[] = [
					'username'	=> '',
					'posts'		=> $topic_link,
				];
			}
			$json = new JsonResponse($data);

			return $json;
		}
		throw new http_exception(405, 'NEED_JS_ENABLED');
	}

	/**
	* fix user url path
	*
	* @param 	string	url		The url link
	* @return 	string
	* @access 	public
	*/
	private function fix_url_path($url)
	{
		$board_url = generate_board_url() . '/';
		return preg_replace('#(?<=href=")[\./]+?/(?=\w)#', $board_url, $url);
	}
}
