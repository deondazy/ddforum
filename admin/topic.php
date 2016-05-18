<?php

use DDForum\Core\User;
use DDForum\Core\Forum;
use DDForum\Core\Topic;
use DDForum\Core\Util;
use DDForum\Core\Site;

/** Load admin **/
require_once( dirname( __FILE__ ) . '/admin.php' );

$title       = 'Edit Topic';
$parent_menu = 'topic-edit.php';
$file        = 'topic-edit.php';

$topic_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
$action   = isset($_GET['action']) ? $_GET['action'] : '';
$user_id  = User::currentUserId();

switch ($action) {
  case 'edit':

    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

      if (!empty($_POST['topic-subject'])) {
        $data = [
          'topic_subject'        =>  $_POST['topic-subject'],
          'topic_slug'           =>  $_POST['topic-slug'],
          'topic_message'        =>  $_POST['topic-message'],
          'forumID'              =>  $_POST['topic-forum'],
          'topic_status'         =>  $_POST['topic-status'],
          'pin'                  =>  $_POST['pin'],
        ];
      }
      else {
        $message = 'Enter topic subject';
      }

      if (Topic::update($data, $topic_id)) {
        $message = 'Topic Updated';
      }
      else {
        $message = 'Unable to update topic, try again';
      }
    }

    break;

  case 'delete':

    if (Topic::delete($topic_id)) {
        Util::redirect("topic-edit.php?message=Topic Deleted");
      }
      else {
        Util::redirect("topic-edit.php?message=Unable to delete topic, try again");
      }
    break;

  default:
    Site::info('Unknown action', true, true);

    break;
}

include( DDFPATH . 'admin/inc/topic-form.php' );
include( DDFPATH . 'admin/admin-footer.php' );