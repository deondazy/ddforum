<?php

namespace DDForum\Core;

class Topic extends ForumItem
{
    public function __construct()
    {
        parent::__construct('topics');
    }

    public function getPinned()
    {
        Database::instance()->query("SELECT * FROM {$this->table} WHERE pinned = 1 ORDER BY create_date DESC");
        return Database::instance()->fetchAll();
    }

    public function getRecent()
    {
        Database::instance()->query("SELECT * FROM {$this->table} ORDER BY create_date DESC");
        return Database::instance()->fetchAll();
    }

    public function getTrending()
    {
        Database::instance()->query("SELECT * FROM {$this->table} WHERE pinned = 1 ORDER BY create_date DESC");
        return Database::instance()->fetchAll();
    }

    public function countReplies($topic_id)
    {
        Database::instance()->query("SELECT * FROM ".Config::get('db_connection')->table_prefix."replies WHERE topic = :topic_id");
        Database::instance()->bind(':topic_id', $topic_id);
        Database::instance()->execute();
        return Database::instance()->rowCount();
    }

    public function check($item, $value, $topicId)
    {
        Database::instance()->query("SELECT {$item} FROM {$this->table} WHERE {$item} = :value AND id = :topic_id");
        Database::instance()->bind(':topic_id', $topicId);
        Database::instance()->bind(':value', $value);
        Database::instance()->execute();

        if (Database::instance()->rowCount() > 0) {
            return true;
        }

        return false;
    }
}
