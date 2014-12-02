<?php
namespace core\db\models;

/**
 * Profile Entity
 */
class notification extends \core\db\models\baseModel
{
    static $has_one = array(
            array("notification_type", "class_name" => "notification_type")
    );
    /**
     * Item shared notification
     */
    const NOTIF_TYPE_COMMENT = 1;
    
    static $NOTIF_MSG_TEMPLATE = array(
        self::NOTIF_TYPE_COMMENT => "<b>%s<b> commented on your note <b>%s</b>"
    );
    
    static $belongs_to = array(
            array("user", "select" => "user_id, email, username")
    );
    /**
     * Pushes a notification with a gived type related to a model to a given not
     * @param integer $type The notification type
     * @param \core\db\models\note $note The related not
     * @param \core\db\models\abstractModel $model The notification triggered model
     */
    public function push($type, note $note, abstractModel $model) {
        if(!in_array($type, array(self::NOTIF_TYPE_COMMENT)))
                throw new \zinux\kernel\exceptions\invalidArgumentException("`$type` is in valid as a notification type.");
        $this->user_id = $note->owner_id;
        $this->item_id = "{$note->WhoAmI()}#{$note->getItemID()}";
        $this->notification_type_id = $type;
        switch($type) {
            case self::NOTIF_TYPE_COMMENT:
                $this->notification_message = "{$note->getItemTitle()}";
                $dt = new \modules\frameModule\models\directoryTree(new \zinux\kernel\routing\request, \modules\frameModule\models\directoryTree::REGULAR);
                $this->notification_link = "{$dt->getNavigationLink($note)}#comment-{$model->comment_id}";
                unset($dt);
                $this->is_read = 0;
                break;
        }
        $this->save();
    }
    /**
     * Pulls unread notifications
     * @param $limit
     * @param $offset
     * @return array
     */
    public function pull($user_id, $limit = 10, $offset = 0) {
        $builder = new \ActiveRecord\SQLBuilder(self::connection(), self::table_name());
        $builder
                ->select("item_id, notifications.notification_type_id,  notification_link, notification_message, notification_title, COUNT( * ) AS count")
                ->joins("INNER JOIN notification_types ON notification_types.notification_type_id = notifications.notification_type_id")
                ->where("user_id = ? AND is_read <> ?", $user_id, 1)
                ->group("item_id, notifications.notification_type_id, notification_message")
                ->limit($limit)
                ->offset($offset);
        return self::find_by_sql($builder->to_s(), $builder->bind_values());
    }
    /**
     * Clears notifications
     * @param $limit
     * @param $offset
     * @return affected rows#
     */
    public function clear($user_id, $limit = 10, $offset = 0) {
        $builder = new \ActiveRecord\SQLBuilder(self::connection(), self::table_name());
        $builder
                ->delete()
                ->where("user_id = ?", $user_id)
                ->limit($limit)
                ->offset($offset);
        return self::query($builder->to_s(), $builder->bind_values());
    }
}