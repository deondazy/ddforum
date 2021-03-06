<?php
/**
 * Administration Users List Screen.
 */
use DDForum\Core\Site;
use DDForum\Core\Database;

if (!defined('DDFPATH')) {
    define('DDFPATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
}

$title = 'All Users';
$file = 'user-edit.php';
$parent_menu = 'user-edit.php';
$has_child = true;

// Load admin
require_once DDFPATH.'admin/admin.php';

require_once DDFPATH.'admin/admin-header.php';

$message = isset($_GET['message']) ? $_GET['message'] : '';

Site::info($message);

$users = $user->getAll();

// Pagination
$all_record = count($users);
$limit = 5;

$current = isset($_GET['page']) ? $_GET['page'] : 1;
$first   = ($all_record - $all_record) + 1;
$last    = ceil($all_record / $limit);
$prev    = ($current - 1 < $first) ? $first : $current - 1;
$next    = ($current + 1 > $last) ? $last : $current + 1;
$offset  = isset($_GET['page']) ? $limit * ($current - 1) : 0;
$users   = $user->paginate('username', $limit, $offset);
?>
<a href="user-new.php" class="extra-nav">Add New User</a>

<?php if ($all_record > 5) : ?>
    <form action="" method="get">
        <div class="paginate">
            <a class="first-page <?php echo ($current == $first) ? 'disabled' : ''; ?>" href="?page=<?php echo $first; ?>">First</a>
            <a class="prev-page <?php echo ($current == $prev) ? 'disabled' : ''; ?>" href="?page=<?php echo $prev; ?>">Prev</a>
            <input class="current-page" type="text" size="2" name="page" value="<?php echo $current; ?>"> of <span class="all-page"><?php echo $last; ?></span>
            <a class="next-page <?php echo ($current == $next) ? 'disabled' : ''; ?>" href="?page=<?php echo $next; ?>">Next</a>
            <a class="last-page <?php echo ($current == $last) ? 'disabled' : ''; ?>" href="?page=<?php echo $last; ?>">Last</a>
        </div>
    </form>
<?php endif; ?>

<table class="manage-item-list">
    <thead>
        <tr>
        <!--<th scope="col" class="checker"><input id="select-all-1" type="checkbox"></th>-->
            <th scope="col">Username</th>
            <th scope="col">Display Name</th>
            <th scope="col">Email</th>
            <th scope="col">Level</th>
            <th scope="col">Posts</th>
            <th class="action-col" scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!$users) : ?>
            <tr>
                <td colspan="10">Nothing to display</td>
            </tr>
        <?php else : ?>
            <?php foreach ($users as $u) : ?>
                <tr id="entry-<?php echo $u->id; ?>">
                    <!--<th scope="row" class="checker">
                        <label class="screen-reader-text" for="item-select-<?php //echo $forum->forumID; ?>">Select <?php //echo $forum->forum_name; ?></label>
                        <input id="item-select-<?php //echo $forum->forumID; ?>" type="checkbox"></td>-->
                    <td>
                        <strong>
                            <?php  if ((int)$u->id == (int)$user->currentUserId()) : ?>
                                <a href="profile.php">
                            <?php else : ?>
                                <a href="user.php?action=edit&amp;id=<?php echo $u->id; ?>">
                            <?php endif; ?>
                                <?php echo $u->username; ?>
                            </a>
                        </strong>
                    </td>

                    <td><?php echo $u->display_name; ?></td>

                    <td><?php echo $u->email; ?></td>

                    <td><?php echo $user->level($u->level); ?></td>

                    <td  class="count-column"><?php echo $user->postCount($u->id); ?></td>

                    <td class="actions">
                        <a class="action-edit" href="user.php?action=edit&amp;id=<?php echo $u->id; ?>"><span class="fa fa-pencil"></span></a>

                        <a target="_blank" class="action-view" href="<?php echo Site::url(); ?>/user/<?php echo $u->username; ?>"><span class="fa fa-eye"></span></a>

                        <a class="action-delete" href="user.php?action=delete&amp;id=<?php echo $u->id; ?>"><span class="fa fa-remove"></span></a>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

    </tbody>

    <tfoot>
        <tr>
            <th scope="col">Username</th>
            <th scope="col">Display Name</th>
            <th scope="col">Email</th>
            <th scope="col">Level</th>
            <th scope="col">Posts</th>
            <th class="action-col" scope="col">Actions</th>
        </tr>
    </tfoot>

</table>

<?php if ($all_record > 5) : ?>
    <form action="" method="get">
        <div class="paginate">

            <a class="first-page <?php echo ($current == $first) ? 'disabled' : ''; ?>" href="?page=<?php echo $first; ?>">First</a>
            <a class="prev-page <?php echo ($current == $prev) ? 'disabled' : ''; ?>" href="?page=<?php echo $prev; ?>">Prev</a>

            <input class="current-page" type="text" size="2" name="page" value="<?php echo $current; ?>"> of <span class="all-page"><?php echo $last; ?></span>

            <a class="next-page <?php echo ($current == $next) ? 'disabled' : ''; ?>" href="?page=<?php echo $next; ?>">Next</a>
            <a class="last-page <?php echo ($current == $last) ? 'disabled' : ''; ?>" href="?page=<?php echo $last; ?>">Last</a>

        </div>
    </form>
<?php endif; ?>

<?php

include DDFPATH.'admin/admin-footer.php';
?>
