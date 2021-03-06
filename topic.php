<?php

/**
 * The file for displaying a single topic.
 */
use DDForum\Core\Database;
use DDForum\Core\Site;
use DDForum\Core\Util;
use DDForum\Core\Like;

if (!defined('DDFPATH')) {
    define('DDFPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
}

// Load DDForum Startup
require_once DDFPATH.'startup.php';

$like = new Like();

$topicSlug = isset($_GET['s']) ? $_GET['s'] : '';
$topicId   = $topic->get('id', $topicSlug);
$forumId   = $topic->get('forum', $topicId);
$title     = "{$topic->get('subject', $topicId)} - {$option->get('site_name')}";
$parent    = isset($_GET['replytopost']) ? $_GET['replytopost'] : 0;
$replies   = $reply->getAll("topic = {$topicId}", "create_date ASC");

// Pagination
$allRecords = Database::instance()->rowCount();
$limit   = 5; // TODO: Change to site limit settings
$current = isset($_GET['page']) ? $_GET['page'] : 1;
$first   = ($allRecords - $allRecords) + 1;
$last    = ceil($allRecords / $limit);
$prev    = ($current - 1 < $first) ? $first : $current - 1;
$next    = ($current + 1 > $last) ? $last : $current + 1;
$offset = isset($_GET['page']) ? $limit * ($current - 1) : 0;
$replies = $reply->paginate($limit, $offset);

// Views counter
$topic->addView($topicId);

$pageTitle = ($topic->isLocked($topicId)) ? '<i class="fa fa-lock"></i> '.$topic->get('subject', $topicId) : $topic->get('subject', $topicId);

$siteUrl = Site::url();

if ('POST' == $_SERVER['REQUEST_METHOD']) {
    if (!empty($_POST['reply-message'])) {
        $creatorOfTopic = $topic->get('poster', $topicId);

        $date = date('Y-m-d H:i:s');
        $poster = $user->currentUserId();

        $replyData = [
            'forum'       => $forumId,
            'topic'       => $topicId,
            'parent'      => $parent,
            'message'     => $_POST['reply-message'],
            'poster'      => $poster,
            'create_date' => $date,
        ];

        $topic->update(['last_post_date' => $date, 'last_poster' => $poster], $topicId);
        $forum->update(['last_post_date' => $date], $forumId);

        if (0 !== $reply->create($replyData)) {
            $lastInsertId = Database::instance()->lastInsertId();
            $lastPage = $last;

            if (isset($_FILES['attachment'])) {
                // Attachment
                $upload_time = date('YmdHis').'_';
                $upload_dir  = __DIR__ . '/uploads/attachments/';
                $upload_file = $upload_dir . basename($upload_time . $_FILES['attachment']['name']);

                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_file)) {
                    $attach->create([
                        'item'        => $lastInsertId,
                        'name'        => $_FILES['attachment']['name'],
                        'size'        => $_FILES['attachment']['size'],
                        'owner'       => $user->currentUserId(),
                        'create_date' => date('Y-m-d H:i:s'),
                        'url'         => Site::url() . '/uploads/attachments/' . $upload_time . $_FILES['attachment']['name'],
                        'mime'        => $_FILES['attachment']['type'],
                    ]);
                }
            }

            $notif->create([
                'notification' =>
                    "<a href=\"{$siteUrl}/topic/{$topic->get('slug', $topicId)}/page={$lastPage}/#post-{$lastInsertId}\">".$user->get('display_name', $poster)." replied to your topic \"".$topic->get('subject', $topicId)."\"</a>",
                'date' => $date,
                'to' => $creatorOfTopic,
            ]);

            if ($allRecords > 5) {
                Util::redirect(
                    "{$siteUrl}/topic/{$topic->get('slug', $topicId)}/page={$lastPage}#post-{$lastInsertId}"
                );
            } else {
                Util::redirect(
                    "{$siteUrl}/topic/{$topic->get('slug', $topicId)}/#post-{$lastInsertId}"
                );
            }
        } else {
            $err = 'Unable to post reply, please try again';
        }
    } else {
        $err = 'You tried to post an empty reply';
    }
}

include DDFPATH.'header.php';
?>

<div class="topic-view">
    <?php
    if (!$topic->exist($topicSlug)) :
        Site::info("Topic doesn't exist, maybe it was removed or moved", true);
    else : ?>

        <?php if ($user->isLogged()) : ?>
            <a href="#reply-form" class="add-btn pull-right">Add Reply</a>
        <?php endif; ?>

        <section class="topic-main">
            <h1 class="topic-subject"><?php echo $pageTitle; ?></h1>
            <ul class="topic-listing">
                <?php if ((isset($current) && $current == $first) || !isset($current)) : ?>
                    <li class="topic-view" id="post-<?php echo $topicId; ?>">
                        <div class="topic-poster-image">
                            <img src="<?php echo $user->get('avatar', $topic->get('poster', $topicId)); ?>" height="60" width="60">
                        </div>

                        <div class="topic-body">
                            <div class="topic-message">
                                <div class="topic-meta">
                                    <a href="<?php echo Site::url(); ?>/user/<?php echo $user->get('username', $topic->get('poster', $topicId)); ?>" class="topic-author">
                                        <?php echo $user->get('display_name', $topic->get('poster', $topicId)); ?>
                                    </a>

                                    <span class="topic-date"><?php echo Util::time2str(Util::timestamp($topic->get('create_date', $topicId))); ?></span>
                                </div>
                                <?php echo Util::parseMentions($topic->get('message', $topicId)); ?>
                            </div>

                            <footer class="pull-right">
                                <form style="display:inline-block" method="post" action="<?php echo "{$siteUrl}/like.php"; ?>">
                                    <input type="hidden" name="isLiked" value="<?php echo $like->isLiked($topicId, $user->currentUserId()); ?>">
                                    <input type="hidden" name="like-item" value="<?php echo $topicId; ?>">
                                    <input type="hidden" name="liker" value="<?php echo $user->currentUserId(); ?>">
                                    <input type="hidden" name="like-return-url" value="<?php echo "{$siteUrl}/topic/{$topicSlug}/#post-{$topicId}"; ?>">
                                    <span class="likes"><i class="fa fa-heart"></i> <?php echo $like->count($topicId); ?></span>

                                    <?php $value = $like->isLiked($topicId, $user->currentUserId()) ? 'Unlike' : 'Like'; ?>
                                    <input class="topic-action js-like like-button" type="submit" value="<?php echo $value; ?>" name="like-button">
                                </form>
                            </footer>
                            <?php if ($attach->exist($topicId)) : ?>
                                <div style="border-bottom: 1px solid #ddd; clear: both; margin-bottom: 10px;"></div>
                                <img class="post-attachment" src="<?php echo $attach->get('url', $topicId); ?>">
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endif; ?>
                
                <?php foreach ($replies as $r) : ?>
                    <li class="topic-view" id="post-<?php echo $r->id; ?>">
                        <div class="topic-poster-image">
                            <img src="<?php echo $user->get('avatar', $r->poster); ?>" height="60" width="60">
                        </div>

                        <div class="topic-body">
                            <div class="topic-message">
                                <div class="topic-meta">
                                    <a href="<?php echo Site::url(); ?>/user/<?php echo $user->get('username', $r->poster); ?>" class="topic-author">
                                        <?php echo $user->get('display_name', $r->poster); ?>
                                    </a>

                                    <span class="topic-date"><?php echo Util::time2str(Util::timestamp($r->create_date)); ?></span>
                                </div>
                                <?php echo Util::parseMentions($r->message); ?>
                            </div>
                            <footer class="pull-right">
                                <form style="display:inline-block" method="post" action="<?php echo "{$siteUrl}/like.php"; ?>">
                                    <input type="hidden" name="isLiked" value="<?php echo $like->isLiked($r->id, $user->currentUserId()); ?>">
                                    <input type="hidden" name="like-item" value="<?php echo $r->id; ?>">
                                    <input type="hidden" name="liker" value="<?php echo $user->currentUserId(); ?>">
                                    <input type="hidden" name="like-return-url" value="<?php echo "{$siteUrl}/topic/{$topicSlug}/#post-{$r->id}"; ?>">
                                    <span class="likes"><i class="fa fa-heart"></i> <?php echo $like->count($r->id); ?></span>

                                    <?php $value = $like->isLiked($r->id, $user->currentUserId()) ? 'Unlike' : 'Like'; ?>
                                    <input class="topic-action js-like like-button" type="submit" value="<?php echo $value; ?>" name="like-button">
                                </form>
                            </footer>
                            <?php if ($attach->exist($r->id)) : ?>
                                <div style="border-bottom: 1px solid #ddd; clear: both; margin-bottom: 10px;"></div>
                                <img class="post-attachment" src="<?php echo $attach->get('url', $r->id); ?>">
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($allRecords > 5) : ?>
                <form action="<?php echo "{$siteUrl}/topic/{$topic->get('slug', $topicId)}/{$topicId}/"; ?>" method="get" class="paginate-form">
                    <div class="paginate centered">
                        <a class="page first-page <?php echo ($current == $first) ? 'disabled' : ''; ?>" href="<?php echo Site::url(); ?>/topic/<?php echo $topicSlug; ?>/page=<?php echo $first; ?>"><< First</a>

                        <a class="page prev-page <?php echo ($current == $prev) ? 'disabled' : ''; ?>" href="<?php echo Site::url(); ?>/topic/<?php echo $topicSlug; ?>/page=<?php echo $prev; ?>">< Prev</a>

                        <span class="all-page"><?php echo "{$current} of {$last}"; ?></span>
                        <a class="page next-page <?php echo ($current == $next) ? 'disabled' : ''; ?>" href="<?php echo Site::url(); ?>/topic/<?php echo $topicSlug; ?>/page=<?php echo $next; ?>">Next ></a>
                        <a class="page last-page <?php echo ($current == $last) ? 'disabled' : ''; ?>" href="<?php echo Site::url(); ?>/topic/<?php echo $topicSlug; ?>/page=<?php echo $last; ?>">Last >></a>
                    </div>
                </form>
            <?php endif; ?>

            <?php
            if ($user->isLogged()) {
                if (!$topic->isLocked($topicId)) {
                    include(DDFPATH . 'inc/reply-form.php');
                } else {
                    Site::info('Replies are disabled for locked topics');
                }
            }
        endif; ?>
    </section>
    <?php include(DDFPATH . 'footer.php'); ?>
</div>
