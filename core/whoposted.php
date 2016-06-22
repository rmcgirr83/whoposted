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

use Symfony\Component\HttpFoundation\JsonResponse;
use phpbb\exception\http_exception;

class whoposted
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\content_visibility */
	protected $content_visibility;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string PHP extension */
	protected $php_ext;

	public function __construct(
			\phpbb\auth\auth $auth,
			\phpbb\content_visibility $content_visibility,
			\phpbb\db\driver\driver_interface $db,
			\phpbb\request\request $request,
			\phpbb\template\template $template,
			\phpbb\user $user,
			$phpbb_root_path,
			$php_ext)
	{
		$this->auth = $auth;
		$this->content_visibility = $content_visibility;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;

		$this->user->add_lang_ext('rmcgirr83/whoposted', 'whoposted');
		if (!function_exists('get_username_string'))
		{
			include($this->root_path . 'includes/functions_content.' . $this->php_ext);
		}
	}

	public function whoposted($forum_id = 0, $topic_id = 0)
	{
		if (!$this->auth->acl_gets('f_list', 'f_read', $forum_id))
		{
			throw new http_exception(404, 'SORRY_AUTH_READ');
		}
		// make sure the topic exists
		$sql = 'SELECT t.topic_id
			FROM ' . TOPICS_TABLE . ' t
			WHERE t.topic_id = ' . (int) $topic_id . ' AND ' . $this->content_visibility->get_visibility_sql('topic', $forum_id, 't.') . ' AND t.forum_id = ' . $forum_id;
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row['topic_id'])
		{
			$topic_id = 0;
		}

		// if we have no topic id (or it was set to 0), display an error
		if (!$topic_id)
		{
			if ($this->request->is_ajax())
			{
				return new JsonResponse(array(
					'title' => $this->user->lang('ERROR'),
					'error' => $this->user->lang('NO_TOPIC'),
				));
			}
			else
			{
				throw new http_exception(404, 'NO_TOPIC');
			}
		}

		// main query: select all the data for users and posts
		$sql_ary = array(
			'SELECT'	=> 'u.username, u.user_id, u.user_colour, COUNT(DISTINCT p.post_id) as posts, p.post_username',
			'FROM'		=> array(
				POSTS_TABLE	=> 'p',
				USERS_TABLE	=> 'u',
			),
			'WHERE'		=> "p.topic_id = $topic_id AND u.user_id = p.poster_id",
			'GROUP_BY'	=> 'u.username, p.post_username',
			'ORDER_BY'	=> 'posts DESC, u.username_clean ASC, p.post_username ASC',
		);

		// hide unapproved posts for users without approve permission
		if (!$this->auth->acl_get('m_approve', $forum_id))
		{
			$sql_ary['WHERE'] .= ' AND ' . $this->content_visibility->get_forums_visibility_sql('post', array($forum_id), 'p.');
		}

		$result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_ary));
		$data = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$username = ($this->auth->acl_get('u_viewprofile')) ? get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], $row['post_username']) : get_username_string('no_profile', $row['user_id'], $row['username'], $row['user_colour'], $row['post_username']);
			$username = str_replace('./../../', generate_board_url() . '/', $username); // Fix paths
			$username = str_replace('./../', generate_board_url() . '/', $username); // Fix paths

			if ($this->request->is_ajax())
			{
				$data[] = array(
					'username'	=> $username,
					'posts'		=> $row['posts'],
				);
			}
			else
			{
				// assign the data as block vars
				$this->template->assign_block_vars('who_posted_row', array(
					'USERNAME'			=> $username,
					'USERNAME_PLAIN'	=> ($row['user_id'] != ANONYMOUS) ? $row['username'] : '',
					'POSTS'				=> $row['posts'],
				));
			}
		}
		$this->db->sql_freeresult($result);

		if ($this->request->is_ajax())
		{
			$json = new JsonResponse($data);

			return $json;
		}
		else
		{
			$this->template->set_filenames(array(
				'body' => 'who_posted.html',
			));

			page_header($this->user->lang['WHOPOSTED_TITLE']);

			// some last tpl assignments
			$this->template->assign_vars(array(
				'U_CLOSE'	=> append_sid("{$this->root_path}viewtopic.$this->php_ext", "t=$topic_id" . ($forum_id ? "&amp;f=$forum_id" : '')),
			));

			page_footer();
		}
	}
}
