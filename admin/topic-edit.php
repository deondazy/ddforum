<?php
/**
 * Administration Topic Screen
 *
 * @package DDForum
 * @subpackage Administration
 */

use DDForum\Core\Site;
use DDForum\Core\Forum;
use DDForum\Core\Topic;
use DDForum\Core\User;
use DDForum\Core\Util;
use DDForum\Core\Database;


if (!defined('DDFPATH')) {
  define('DDFPATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
}

$title        =  'Topics';
$file         =  'topic-edit.php';
$parent_menu  =  'topic-edit.php';
$has_child    =  true;

// Load admin
require_once(DDFPATH . 'admin/admin.php');

require_once(DDFPATH . 'admin/admin-header.php');

$message = isset( $_GET['message'] ) ? $_GET['message'] : '';

Site::info($message);

$topics = Topic::getAll();

// Pagination
$all_record = Database::rowCount();
$limit = 5;

$current = isset( $_GET['page'] ) ? $_GET['page'] : 1;
$first   = ( $all_record - $all_record ) + 1;
$last    = ceil( $all_record / $limit );
$prev    = ( $current - 1 < $first ) ? $first : $current - 1;
$next    = ( $current + 1 > $last ) ? $last : $current + 1;

$offset = isset( $_GET['page'] ) ? $limit * ( $current - 1 ) : 0;

$topics = Topic::paginate('topic_date DESC', $limit, $offset);
?>
<a href="topic-new.php" class="extra-nav">Add Topic</a>
<?php if ( $all_record > 5 ) : ?>
  <form action="" method="get">
    <div class="paginate">

      <a class="first-page <?php echo ( $current == $first ) ? 'disabled' : ''; ?>" href="?page=<?php echo $first; ?>">First</a>
      <a class="prev-page <?php echo ( $current == $prev ) ? 'disabled' : ''; ?>" href="?page=<?php echo $prev; ?>">Prev</a>

      <input class="current-page" type="text" size="2" name="page" value="<?php echo $current; ?>"> of <span class="all-page"><?php echo $last; ?></span>

      <a class="next-page <?php echo ( $current == $next ) ? 'disabled' : ''; ?>" href="?page=<?php echo $next; ?>">Next</a>
      <a class="last-page <?php echo ( $current == $last ) ? 'disabled' : ''; ?>" href="?page=<?php echo $last; ?>">Last</a>

    </div>
  </form>
<?php endif; ?>

<table class="manage-item-list">
  <thead>
    <tr>
    <!--  <th scope="col" class="checker"><input id="select-all-1" type="checkbox"></th> -->
      <th scope="col">Topic</th>
      <th scope="col">Forum</th>
      <th scope="col">Replies</th>
      <th scope="col">Creator</th>
      <th scope="col">Created</th>
      <th scope="col">Last reply</th>
      <th class="action-col" scope="col">Actions</th>
    </tr>
  </thead>

  <tbody>

    <?php if ( ! $topics ) : ?>
      <tr>
        <td colspan="10">Nothing to display</td>
      </tr>

    <?php else : ?>

      <?php foreach ($topics as $topic) : ?>

        <tr id="entry-<?php echo $topic->topicID; ?>">
          <!--<th scope="row" class="checker">
            <label class="screen-reader-text" for="item-select-<?php echo $forum->forumID; ?>">Select <?php echo $forum->forum_name; ?></label>
            <input id="item-select-<?php echo $forum->forumID; ?>" type="checkbox"></td>-->

          <td>
            <strong>
              <a href="topic.php?action=edit&amp;id=<?php echo $topic->topicID; ?>">
                <?php echo $topic->topic_subject; ?>
              </a>
            </strong>
          </td>

          <td class="count-column"><?php echo Forum::get('forum_name', $topic->forumID); ?></td>

          <td class="count-column"><?php echo Topic::countReplies($topic->topicID); ?></td>

          <td><?php echo User::get("username", $topic->topic_poster); ?></td>

          <td><?php echo Util::time2str(Util::timestamp($topic->topic_date)); ?></td>

          <td><?php echo Util::time2str(Util::timestamp($topic->topic_last_post_date)); ?></td>

          <td class="actions">
            <a class="action-edit" href="topic.php?action=edit&amp;id=<?php echo $topic->topicID; ?>"><span class="fa fa-pencil"></span></a>

            <a class="action-view" href="<?php echo Site::url(); ?>/topics/<?php echo $topic->topicID; ?>"><span class="fa fa-eye"></span></a>

            <a class="action-delete" href="topic.php?action=delete&amp;id=<?php echo $topic->topicID; ?>"><span class="fa fa-remove"></span></a>
          </td>
        </tr>

      <?php endforeach; ?>

    <?php endif; ?>

  </tbody>

  <tfoot>
    <tr>
      <!--<th scope="col" class="checker"><input id="select-all-2" type="checkbox"></th>-->
      <th scope="col">Topic</th>
      <th scope="col">Forum</th>
      <th scope="col">Replies</th>
      <th scope="col">Creator</th>
      <th scope="col">Created</th>
      <th scope="col">Last reply</th>
      <th class="action-col" scope="col">Actions</th>
    </tr>
  </tfoot>

</table>
<?php if ( $all_record > 5 ) : ?>
  <form action="" method="get">
    <div class="paginate">

      <a class="first-page <?php echo ( $current == $first ) ? 'disabled' : ''; ?>" href="?page=<?php echo $first; ?>">First</a>
      <a class="prev-page <?php echo ( $current == $prev ) ? 'disabled' : ''; ?>" href="?page=<?php echo $prev; ?>">Prev</a>

      <input class="current-page" type="text" size="2" name="page" value="<?php echo $current; ?>"> of <span class="all-page"><?php echo $last; ?></span>

      <a class="next-page <?php echo ( $current == $next ) ? 'disabled' : ''; ?>" href="?page=<?php echo $next; ?>">Next</a>
      <a class="last-page <?php echo ( $current == $last ) ? 'disabled' : ''; ?>" href="?page=<?php echo $last; ?>">Last</a>

    </div>
  </form>
<?php endif; ?>

<?php

include( DDFPATH . 'admin/admin-footer.php' );
?>
