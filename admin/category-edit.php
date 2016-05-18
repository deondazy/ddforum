<?php

/** Load admin **/
require_once( dirname( __FILE__ ) . '/admin.php' );

$title = 'Categories';
//$file = 'category-edit.php';
//$parent_menu = 'category-edit.php';

require_once( DDFPATH . 'admin/admin-header.php' );

$message = isset( $_GET['message'] ) ? $_GET['message'] : '';
show_message($message);

$forums = $ddf_db->fetch_all("forums");

// Pagination
$all_record = $ddf_db->row_count;
$limit = 5;

$current = isset( $_GET['page'] ) ? $_GET['page'] : 1;
$first = ( $all_record - $all_record ) + 1;
$last = ceil( $all_record / $limit );
$prev = ( $current - 1 < $first ) ? $first : $current - 1;
$next = ( $current + 1 > $last ) ? $last : $current + 1;

$offset = isset( $_GET['page'] ) ? $limit * ( $current - 1 ) : 0;

$forums = $ddf_db->fetch_all("forums", "*", '', '', $limit, $offset);
?>
<a href="forum-new.php" class="extra-nav">Add Forum</a>
<form action="" method="get">
	<div class="paginate">

		<a class="first-page <?php echo ( $current == $first ) ? 'disabled' : ''; ?>" href="?page=<?php echo $first; ?>">First</a>
		<a class="prev-page <?php echo ( $current == $prev ) ? 'disabled' : ''; ?>" href="?page=<?php echo $prev; ?>">Prev</a>

		<input class="current-page" type="text" size="2" name="page" value="<?php echo $current; ?>"> of <span class="all-page"><?php echo $last; ?></span>

		<a class="next-page <?php echo ( $current == $next ) ? 'disabled' : ''; ?>" href="?page=<?php echo $next; ?>">Next</a>
		<a class="last-page <?php echo ( $current == $last ) ? 'disabled' : ''; ?>" href="?page=<?php echo $last; ?>">Last</a>

	</div>
</form>

<table class="manage-item-list">
	<thead>
		<tr>
		<!--	<th scope="col" class="checker"><input id="select-all-1" type="checkbox"></th> -->
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
						</strong>
						<div class="description"><?php echo $forum->forum_description; ?></div>
					</td>

					<td class="count-column"><?php echo $forum->forum_topics; ?></td>

					<td class="count-column"><?php echo $forum->forum_replies; ?></td>

					<td><?php echo $user->get_user("username", $forum->forum_creator); ?></td>

					<td><?php echo $forum->forum_last_post_date; ?></td>

					<td class="actions">
						<a class="action-edit" href="forum.php?action=edit&amp;id=<?php echo $forum->forumID; ?>"><span class="genericon genericon-edit"></span></a>

						<a class="action-view" href="<?php echo home_url(); ?>/forum.php?id=<?php echo $forum->forumID; ?>"><span class="genericon genericon-show"></span></a>

						<a class="action-delete" href="forum.php?action=delete&amp;id=<?php echo $forum->forumID; ?>"><span class="genericon genericon-close"></span></a>
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
<form action="" method="get">
	<div class="paginate">

		<a class="first-page <?php echo ( $current == $first ) ? 'disabled' : ''; ?>" href="?page=<?php echo $first; ?>">First</a>
		<a class="prev-page <?php echo ( $current == $prev ) ? 'disabled' : ''; ?>" href="?page=<?php echo $prev; ?>">Prev</a>

		<input class="current-page" type="text" size="2" name="page" value="<?php echo $current; ?>"> of <span class="all-page"><?php echo $last; ?></span>

		<a class="next-page <?php echo ( $current == $next ) ? 'disabled' : ''; ?>" href="?page=<?php echo $next; ?>">Next</a>
		<a class="last-page <?php echo ( $current == $last ) ? 'disabled' : ''; ?>" href="?page=<?php echo $last; ?>">Last</a>

	</div>
</form>

<?php

include( DDFPATH . 'admin/admin-footer.php' );
?>
