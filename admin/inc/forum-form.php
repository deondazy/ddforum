<?php
/**
 * Form for New and Edit Forum Screen
 *
 * @package DDForum
 */

use DDForum\Core\Forum;
use DDForum\Core\Site;
use DDForum\Core\Util;

// Can't be accessed directly
if ( !defined( 'DDFPATH' ) ) {
  die( 'Direct access denied' );
}

$forumId         =   isset($forum_id) ? $forum_id : 0;
$forumName       =   Forum::get('forum_name', $forumId);
$forumSlug       =   Forum::get('forum_slug', $forumId);
$forumDesc       =   Forum::get('forum_description', $forumId);
$forumType       =   Forum::get('forum_type', $forumId);
$forumStatus     =   Forum::get('forum_status', $forumId);
$forumVisibility =   Forum::get('forum_visibility', $forumId);
$forumParent     =   Forum::get('forum_parent', $forumId);

require_once( DDFPATH . 'admin/admin-header.php' );

if (isset($message)) {
  Site::info($message);
}
elseif (isset($_GET['message'])) {
  Site::info($_GET['message']);
}
?>

<form action="<?php echo ($forumId == 0) ? 'add-forum.php' : 'forum.php?action=edit&id=' . $forumId; ?>" method="post" class="action-form clearfix">

  <div class="form-wrap-main">
    <div class="field">
      <span class="label">Forum Name</span>
      <label class="screen-reader-text" for="forum-title">Forum Name</label>
      <input type="text" id="forum-title" class="title-box js-title-box" name="forum-title" value="<?php echo $forumName; ?>">
    </div>

    <div class="field">
      <label class="screen-reader-text" for="form-box"></label>
      <textarea class="forum-description" id="form-box" name="forum-description"><?php echo $forumDesc; ?></textarea>
    </div>
  </div>

  <div class="form-wrap-side">

    <div class="field">
      <span class="label">Forum Slug</span>
      <label class="screen-reader-text" for="forum-slug">Forum Slug</label>
      <input type="text" id="forum-slug" class="title-box js-slug-box" name="forum-slug" value="<?php echo $forumSlug; ?>">
    </div>

    <div class="head">Forum Settings</div>

    <div class="content">
      <div class="field">
        <span class="label">Type</span>
        <label class="screen-reader-text" for="forum-type">Forum Type</label>
        <select id="forum-type" class="select-box" name="forum-type">
          <?php
          $type = ['forum' => 'Forum', 'category' => 'Category'];
          $type = array_map("trim", $type);

          foreach ($type as $id => $item) : ?>
            <option value="<?php echo $id ?>" <?php Util::selected($forumType, $id); ?>>
              <?php echo $item; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <span class="label">Status</span>
        <label class="screen-reader-text" for="forum-status">Forum Status</label>
        <select id="forum-status" class="select-box" name="forum-status">
          <?php
          $status = ['open' => 'Open', 'locked' => 'Locked'];
          $status = array_map("trim", $status);

          foreach ($status as $id => $item) : ?>
            <option value="<?php echo $id ?>" <?php Util::selected( $forumStatus, $id ); ?>>
              <?php echo $item; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <span class="label">Visibility</span>
        <label class="screen-reader-text" for="forum-status">Forum Visibility</label>
        <select id="forum-visibility" class="select-box" name="forum-visibility">
          <?php
          $visibility = ['public' => 'Public', 'private' => 'Private', 'hidden' => 'Hidden'];
          $visibility = array_map("trim", $visibility);

          foreach ($visibility as $id => $item) : ?>
            <option value="<?php echo $id ?>" <?php Util::selected($forumVisibility, $id); ?>>
              <?php echo $item; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <span class="label">Parent</span>
        <label class="screen-reader-text" for="forum-status">Forum Parent</label>

        <select id="forum-parent" class="select-box" name="forum-parent">

          <?php
          foreach (Forum::getAll() as $parents) {
            $parent_data[0] = 'None';
            $parent_data[$parents->forumID] = $parents->forum_name;
          }

          // Remove current forum from list
          if ( $forumId != 0 ) {
            unset($parent_data[$forumId]);
          }

          foreach ($parent_data as $id => $item) : ?>

            <option value="<?php echo $id; ?>" <?php Util::selected($forumParent, $id); ?>><?php echo $item; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <input type="submit" class="primary-button" value="<?php echo isset($action) ? 'Update' : 'Create'; ?>">
  </div>
</form>
