<?php

/** Load admin **/
require_once( dirname( __FILE__ ) . '/admin.php' );

$title = 'Replies';
$file = 'reply-edit.php';
$parent = 'reply-edit.php';

require_once( ABSPATH . 'admin/admin-header.php' );

$message = isset( $_GET['message'] ) ? $_GET['message'] : '';
show_message($message);

$replies = $ddf_db->fetch_all($ddf_db->replies);

// Pagination
$all_record = $ddf_db->row_count;
$limit = 5;
		
$current = isset( $_GET['page'] ) ? $_GET['page'] : 1;
$first = ( $all_record - $all_record ) + 1;
$last = ceil( $all_record / $limit );
$prev = ( $current - 1 < $first ) ? $first : $current - 1;
$next = ( $current + 1 > $last ) ? $last : $current + 1;
		
$offset = isset( $_GET['page'] ) ? $limit * ( $current - 1 ) : 0;

$replies = $ddf_db->fetch_all($ddf_db->replies, "*", '', 'replyID DESC', $limit, $offset);
?>
<a href="reply-new.php" class="extra-nav">Add Reply</a> 
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
		<!--	<th scope="col" class="checker"><input id="select-all-1" type="checkbox"></th> -->
			<th scope="col">Topic</th>
			<th scope="col">Forum</th>
			<th scope="col">Author</th>
			<th scope="col">Reply date</th>
			<th class="action-col" scope="col">Actions</th>
		</tr>
	</thead>

	<tbody>		
		
		<?php if ( ! $replies ) : ?>
			<tr>
				<td colspan="10">Nothing to display</td>
			</tr>
		
		<?php else : ?>

			<?php foreach ($replies as $reply) : ?>

				<tr id="entry-<?php echo $reply->replyID; ?>">
					<!--<th scope="row" class="checker">
						<label class="screen-reader-text" for="item-select-<?php echo $forum->forumID; ?>">Select <?php echo $forum->forum_name; ?></label>
						<input id="item-select-<?php echo $forum->forumID; ?>" type="checkbox"></td>-->

					<td>
						<strong>
							<a href="reply.php?action=edit&amp;id=<?php echo $reply->replyID; ?>&amp;forum=<?php echo $reply->forumID; ?>&amp;topic=<?php echo $reply->topicID; ?>">
								<?php echo $ddf_db->get_topic('topic_subject', $reply->topicID); ?>
							</a>
						</strong>
					</td>

					<td class="count-column"><?php echo $ddf_db->get_forum('forum_name', $reply->forumID); ?></td>

					<td class="count-column"><?php echo time2str($reply->reply_date); ?></td>

					<td><?php echo $user->get_user("username", $reply->reply_poster); ?></td>

					<td class="actions">
						<a class="action-edit" href="reply.php?action=edit&amp;id=<?php echo $reply->replyID; ?>&amp;forum=<?php echo $reply->forumID; ?>&amp;topic=<?php echo $reply->topicID; ?>"><span class="genericon genericon-edit"></span></a>
						
						<a class="action-view" href="<?php echo home_url(); ?>/reply.php?id=<?php echo $reply->replyID; ?>&amp;forum=<?php echo $reply->forumID; ?>&amp;topic=<?php echo $reply->topicID; ?>"><span class="genericon genericon-show"></span></a>
						
						<a class="action-delete" href="reply.php?action=delete&amp;id=<?php echo $reply->replyID; ?>&amp;forum=<?php echo $reply->forumID; ?>&amp;topic=<?php echo $reply->topicID; ?>"><span class="genericon genericon-close"></span></a>
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
			<th scope="col">Author</th>
			<th scope="col">Reply date</th>
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

include( ABSPATH . 'admin/admin-footer.php' );
?>