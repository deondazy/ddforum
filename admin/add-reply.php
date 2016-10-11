<?php
/**
 * Add new reply and send to reply.php
 */

use DDForum\Core\User;
use DDForum\Core\Forum;
use DDForum\Core\Topic;
use DDForum\Core\Reply;
use DDForum\Core\Util;
use DDForum\Core\Database;

/** Load admin **/
require_once( dirname( __FILE__ ) . '/admin.php' );

$user_id = User::currentUserId();

$Topic = new Topic();
$Reply = new Reply();

if ('POST' == $_SERVER['REQUEST_METHOD']) {

	if (!empty($_POST['reply-message'])) {

		$data = [
			'topic'   =>  $_POST['reply-topic'],
			'forum'   =>  $Topic->get('forum', $_POST['reply-topic']),
			'message' =>  $_POST['reply-message'],
			'poster'  =>  $user_id,
			'date'    =>  date('Y-m-d H:i:s'),
		];

		if ($Reply->create($data)) {
			Util::redirect("reply.php?action=edit&id=".Database::instance()->lastInsertId()."&message=Reply added");
		}
		else {
			Util::redirect("reply-new.php?message=Unable to add reply, try again");
		}
	}
	else {
		Util::redirect('reply-new.php?message=Enter reply message');
	}
}
else {
	Site::info('Access Denied', true, true);
}
