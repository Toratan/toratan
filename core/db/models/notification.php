<?php
namespace core\db\models;

/**
 * Profile Entity
 */
class notification extends \core\db\models\baseModel
{
    /**
     * Item shared notification
     */
    const NOTIF_FLAG_SHARE = 0;
    
    static $belongs_to = array(
            array("user", "select" => "user_id, email, username")
    );
    /**
     * place holder for related notification item instance
     * @var type 
     */
    public $item;
    /**
     * fetches notification from all subscribtions in JSON format
     * @param string $user_id follower user ID
     * @param string $since_date notification since ?, if NULL will return at all times
     * @param integer $limit The limit of LIMIT statement in SQL
     * @param boolean $include_meta Should it include meta data about the related notification item?
     * @param string $notif_type any of \core\db\models\notification::NOTIF_TYPE_* values
     * @param integer $offset The offset of LIMIT statement in SQL [ <b>note that</b> if both $offset and $limit be '0' no limit will apply to SQL ]
     * @return array of \core\db\models\notification;
     */
    public static function fetch_json($user_id, $limit = 0, $offset = 0, $include_meta = 0, $notif_type = -1, $since_date = NULL)
    {
        $notifs = self::fetch($user_id, $limit, $offset, $include_meta, $notif_type, $since_date);
        $json_output = "[";
        for($index = 0; $index<count($notifs); $index++)
        {
            if($include_meta)
            {
                $class = "\\".__NAMESPACE__."\\".$notifs[$index]->item_table;
                $instance = new $class;
                try
                {
                    $item = $instance->fetch($notifs[$index]->item_id, NULL, array("select" => "{$notifs[$index]->item_table}_id, {$notifs[$index]->item_table}_title, {$notifs[$index]->item_table}_body"));
                }
                # fail-safe for in-case of no item related to notification found
                catch(\core\db\exceptions\dbNotFoundException $e)
                {
                    unset($e);
                    # we just delete the notification them :)
                    $Invalid_notif = \core\db\models\notification::find($notifs[$index]->notification_id);
                    $Invalid_notif->delete();
                    # proceed with others
                    continue;
                }
                $user = \preg_replace("#}$#i", ", \"profile\":{$notifs[$index]->user->profile->to_json()}}", $notifs[$index]->user->to_json());
                $json_output .= \preg_replace(array('#}$#i', '#("item_table"\s*:\s*)("[a-z]+")#i'), array(",\"user\":{$user}}", "\"item_type\":$2, \"item\":".\preg_replace("#{$notifs[$index]->item_table}#i", "item", $item->to_json())), $notifs[$index]->to_json());
            }
            else
                $json_output .= $notifs[$index]->to_json();
            if($index!=count($notifs)-1)
                $json_output.=",";
        }
        $json_output.="]";
        return $json_output;
    }
    /**
     * fetches notification from all subscribtions in array format
     * @param string $user_id follower user ID
     * @param string $since_date notification since ?, if NULL will return at all times
     * @param integer $limit The limit of LIMIT statement in SQL
     * @param boolean $include_meta Should it include meta data about the related notification item?
     * @param string $notif_type any of \core\db\models\notification::NOTIF_TYPE_* values
     * @param integer $offset The offset of LIMIT statement in SQL [ <b>note that</b> if both $offset and $limit be '0' no limit will apply to SQL ]
     * @return array of \core\db\models\notification;
     */
    public static function fetch_array($user_id, $limit = 0, $offset = 0, $include_meta = 0, $notif_type = -1, $since_date = NULL)
    {
        $notifs = self::fetch($user_id, $limit, $offset, $include_meta, $notif_type, $since_date);
        for($index = 0; $index<count($notifs); $index++)
        {
            if($include_meta)
            {
                $class = "\\".__NAMESPACE__."\\".$notifs[$index]->item_table;
                $instance = new $class;
                 $notifs[$index]->item = $instance->fetch($notifs[$index]->item_id, NULL, array("select" => "{$notifs[$index]->item_table}_id, {$notifs[$index]->item_table}_title, {$notifs[$index]->item_table}_body"));
            }
        }
        return $notifs;
    }
    /**
     * fetches notification from all subscribtions
     * @param string $user_id follower user ID
     * @param string $since_date notification since ?, if NULL will return at all times
     * @param integer $limit The limit of LIMIT statement in SQL
     * @param boolean $include_meta Should it include meta data about the related notification item?
     * @param string $notif_type any of \core\db\models\notification::NOTIF_TYPE_* values
     * @param integer $offset The offset of LIMIT statement in SQL [ <b>note that</b> if both $offset and $limit be '0' no limit will apply to SQL ]
     * @return array of \core\db\models\notification;
     */
    public static function fetch($user_id, $limit = 0, $offset = 0, $include_meta = 0, $notif_type = -1, $since_date = NULL)
    {
        $notif = new \core\db\models\notification;
        $cond = array("`subscribes`.follower = ? AND is_visible = 1 ", $user_id);
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
        $includes = array();
        if($include_meta)
        {
            $includes = array("user" => array("include"=>"profile"));
        }
        $join = "INNER JOIN `subscribes` ON(`subscribes`.followed = `notifications`.user_id)";
        return $notif->all(array(
                "conditions" => $cond,
                "joins" => $join,
                "order" => "created_at desc",
                "having" => $having,
                "limit" => $limit,
                "offset" => $offset,
                "include" => $includes,
                "readonly" => TRUE)
            );
    }
    /**
     * fetches notification from one subscribtion
     * @param string $user_id follower user ID
     * @param string $followed_user_id the target user ID want to fetch from
     * @param string $since_date notification since ?, if NULL will return at all times
     * @param integer $limit The limit of LIMIT statement in SQL
     * @param boolean $include_meta Should it include meta data about the related notification item?
     * @param string $notif_type any of \core\db\models\notification::NOTIF_TYPE_* values
     * @param integer $offset The offset of LIMIT statement in SQL [ <b>note that</b> if both $offset and $limit be '0' no limit will apply to SQL ]
     * @return array of \core\db\models\notification;
     */
    public static function fetch_from($user_id, $followed_user_id, $limit = 0, $offset = 0, $include_meta = 0, $notif_type = -1, $since_date = NULL)
    {
        $notif = new \core\db\models\notification;
        $cond = array("`subscribes`.follower = ? AND `subscribes`.followed = ? AND is_visible = 1", $user_id, $followed_user_id);
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
        $includes = array();
        if($include_meta)
        {
            $includes = array("user" => array("include"=>"profile"));
        }
        $join = "INNER JOIN `subscribes` ON(`subscribes`.followed = `notifications`.user_id)";
        return $notif->all(array(
                "conditions" => $cond,
                "joins" => $join,
                "order" => "created_at desc",
                "having" => $having,
                "limit" => $limit,
                "offset" => $offset,
                "include" => $includes,
                "readonly" => TRUE)
            );
    }
    public static function deleteNotification($user_id, $item_id, $item_table)
    {
        \core\db\models\notification::delete_all(array("conditions" => array("user_id = ? AND item_id = ? AND item_table = ?", $user_id, $item_id, $item_table)));
    }
    public static function visibleNotification($user_id, $item_id, $item_table, $is_visible = 1)
    {
        $notifs = \core\db\models\notification::all(array("conditions" => array("user_id = ? AND item_id = ? AND item_table = ?", $user_id, $item_id, $item_table)));
        foreach ($notifs as $notif)
        {
            $notif->is_visible = $is_visible?1:0;
            $notif->save();
        }
    }
    public static function put($user_id, $item_id, $item_table, $notification_type)
    {
        if(count(($notifs = self::all(array("conditions" => array("user_id = ? AND item_id = ? AND item_table = ?", $user_id, $item_id, $item_table))))))
        {
            foreach($notifs as $notif)
            {
                $notif->is_visible = 1;
                $notif->save();
            }
            return;
        }
        $notif = new \core\db\models\notification;
        $notif->user_id = $user_id;
        $notif->item_id = $item_id;
        $notif->item_table = $item_table;
        switch($notification_type)
        {
            case self::NOTIF_FLAG_SHARE:
                $notif->notification_type = $notification_type;
                break;
            default:
                throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid income notification type ID# $notification_type");
        }
        $notif->save();
    }
}