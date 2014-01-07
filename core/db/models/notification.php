<?php
namespace core\db\models;

/**
 * Profile Entity
 */
class notification extends \core\db\models\baseModel
{
    const NOTIF_TYPE_SHARED_FOLDER = 0;
    /**
     * fetches notification from all subscribtions
     * @param string $user_id follower user ID
     * @param string $notif_type any of \core\db\models\notification::NOTIF_TYPE_* values
     * @param string $since_date notification since ?, if NULL will return at all times
     * @param integer $limit The limit of LIMIT statement in SQL
     * @param integer $offset The offset of LIMIT statement in SQL [ <b>note that</b> if both $offset and $limit be '0' no limit will apply to SQL ]
     * @return array of \core\db\models\notification;
     */
    public static function fetch($user_id, $limit = 0, $offset = 0, $notif_type = -1, $since_date = NULL)
    {
        $notif = new \core\db\models\notification;
        $cond = array("`subscribes`.follower = ?", $user_id);
        if($notif_type != -1)
        {
            $cond[0] .= " AND notification_type = ?";
            $cond[] = $notif_type;
        }
        $having = array();
        if($since_date)
        {
            $having = ("created_at >= '$since_date'");
        }
        $join = "INNER JOIN `subscribes` ON(`subscribes`.followed = `notifications`.trigger_user_id)";
        return $notif->all(array(
                "conditions" => $cond,
                "joins" => $join,
                "order" => "created_at desc",
                "having" => $having,
                "limit" => $limit,
                "offset" => $offset));
    }
    /**
     * fetches notification from one subscribtion
     * @param string $user_id follower user ID
     * @param string $followed_user_id the target user ID want to fetch from
     * @param string $notif_type any of \core\db\models\notification::NOTIF_TYPE_* values
     * @param string $since_date notification since ?, if NULL will return at all times
     * @param integer $limit The limit of LIMIT statement in SQL
     * @param integer $offset The offset of LIMIT statement in SQL [ <b>note that</b> if both $offset and $limit be '0' no limit will apply to SQL ]
     * @return array of \core\db\models\notification;
     */
    public static function fetch_from($user_id, $followed_user_id, $limit = 0, $offset = 0, $notif_type = -1, $since_date = NULL)
    {
        $notif = new \core\db\models\notification;
        $cond = array("`subscribes`.follower = ? AND `subscribes`.followed = ?", $user_id, $followed_user_id);
        if($notif_type != -1)
        {
            $cond[0] .= " AND notification_type = ?";
            $cond[] = $notif_type;
        }
        $having = array();
        if($since_date)
        {
            $having = ("created_at >= '$since_date'");
        }
        $join = "INNER JOIN `subscribes` ON(`subscribes`.followed = `notifications`.trigger_user_id)";
        return $notif->all(array(
                "conditions" => $cond,
                "joins" => $join,
                "order" => "created_at desc",
                "having" => $having,
                "limit" => $limit,
                "offset" => $offset));
    }
}