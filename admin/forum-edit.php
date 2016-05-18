<?php
/**
 * Administration Forum Screen
 *
 * @package DDForum
 * @subpackage Administration
 */

use DDForum\Core\Site;
use DDForum\Core\Forum;
use DDForum\Core\User;
use DDForum\Core\Util;
use DDForum\Core\Database;


if (!defined('DDFPATH')) {
  define('DDFPATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
}

$title        =  'Forums';
$file         =  'forum-edit.php';
$parent_menu  =  'forum-edit.php';
$has_child    =  true;

// Load admin
require_once(DDFPATH . 'admin/admin.php');

require_once(DDFPATH . 'admin/admin-header.php');

$message = isset( $_GET['message'] ) ? $_GET['message'] : '';

Site::info($message);

$forums = Forum::getAll();

// Pagination
$all_record = Database::rowCount();
$limit = 5;

$current = isset( $_GET['page'] ) ? $_GET['page'] : 1;
$first   = ( $all_record - $all_record ) + 1;
$last    = ceil( $all_record / $limit );
$prev    = ( $current - 1 < $first ) ? $first : $current - 1;
$next    = ( $current + 1 > $last ) ? $last : $current + 1;

$offset = isset( $_GET['page'] ) ? $limit * ( $current - 1 ) : 0;

$forums = Forum::paginate('forum_date DESC', $limit, $offset);
?>
<a href="forum-new.php" class="extra-nav">Add Forum</a>
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
      <th scope="col">Forum</th>
      <th scope="col">Topics</th>
      <th scope="col">Replies</th>
      <th scope="col">Creator</th>
      <th scope="col">Last reply date</th>
      <th class="action-col" scope="col">Actions</th>
    </tr>
  </thead>

  <tbody>

    <?php if ( ! $forums ) : ?>
      <tr>
        <td colspan="10">Nothing to display</td>
      </tr>

    <?php else : ?>

      <?php foreach ($forums as $forum) : ?>

        <tr id="entry-<?php echo $forum->forumID; ?>">
          <!--<th scope="row" class="checker">
            <label class="screen-reader-text" for="item-select-<?php echo $forum->forumID; ?>">Select <?php echo $forum->forum_name; ?></label>
            <input id="item-select-<?php echo $forum->forumID; ?>" type="checkbox"></td>-->

          <td>
            <strong>
              <a href="forum.php?action=edit&amp;id=<?php echo $forum->forumID; ?>">
                <?php echo $forum->forum_name; ?>
              </a>
              <div class="item-type">- Type: <?php echo $forum->forum_type; ?></div>

              <?php if ( $forum->forum_type == 'forum' && $forum->forum_parent != 0 ) : ?>
                <div class="item-type"> - Parent: <?php echo Forum::get('forum_name', $forum->forum_parent); ?></div>
              <?php endif; ?>

            </strong>
            <div class="description"><?php echo $forum->forum_description; ?></div>
          </td>

          <td class="count-column"><?php echo Forum::countTopics($forum->forumID); ?></td>

          <td class="count-column"><?php echo Forum::countReplies($forum->forumID); ?></td>

          <td><?php echo User::get("username", $forum->forum_creator); ?></td>

          <td><?php echo Util::time2str(Util::timestamp($forum->forum_last_post)); ?></td>

          <td class="actions">
            <a class="action-edit" href="forum.php?action=edit&amp;id=<?php echo $forum->forumID; ?>"><span class="fa fa-pencil"></span></a>

            <a class="action-view" href="<?php echo Site::url(); ?>/<?php echo $forum->forum_slug; ?>"><span class="fa fa-eye"></span></a>

            <a class="action-delete" href="forum.php?action=delete&amp;id=<?php echo $forum->forumID; ?>"><span class="fa fa-remove"></span></a>
          </td>
        </tr>

      <?php endforeach; ?>

    <?php endif; ?>

  </tbody>

  <tfoot>
    <tr>
      <!--<th scope="col" class="checker"><input id="select-all-2" type="checkbox"></th>-->
      <th scope="col">Forum</th>
      <th scope="col">Topics</th>
      <th scope="col">Replies</th>
      <th scope="col">Creator</th>
      <th scope="col">Last reply date</th>
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