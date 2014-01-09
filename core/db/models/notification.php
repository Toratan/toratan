<?php
namespace core\db\models;

/**
 * Profile Entity
 */
class notification extends \core\db\models\baseModel
{
    static $belongs_to = array(
            array("user", "select" => "user_id, email, username")
    );
    /**
     * place holder for related notification item instance
     * @var type 
     */
    public $item;
    /**
     * Folder shared notification
     */
    const NOTIF_TYPE_SHARED_FOLDER = 0;
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
                $item = $instance->fetch($notifs[$index]->item_id, NULL, array("select" => "{$notifs[$index]->item_table}_id, {$notifs[$index]->item_table}_title, {$notifs[$index]->item_table}_body"));
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
}