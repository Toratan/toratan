<?php
namespace core\db\models;

class subscribe extends \core\db\models\baseModel
{
    /**
     * subscribe a user from another one
     * @param string|integer $followed the followed user ID
     * @param string|integer $follower the follower user ID
     * @return boolean TRUE if any row has been deleted from subscribe table; otherwise FALSE
     */
    public static function subscribe($followed, $follower)
    {
        parent::create(array("followed" => $followed, "follower" => $follower));
        return true;
    }
    /**
     * unsubscribe a user from another one
     * @param string|integer $followed the followed user ID
     * @param string|integer $follower the follower user ID
     * @return boolean TRUE if any row has been deleted from subscribe table; otherwise FALSE
     */
    public static function unsubscribe($followed, $follower)
    {
        return parent::delete_all(array("conditions"=>array("followed = ? AND follower = ?", $followed, $follower)));
    }
    /**
     * Check if a follower has been subscribe to another user or not
     * @param string|integer $followed the followed user ID
     * @param string|integer $follower the follower user ID
     * @return boolean TRUE if $follower has been subscribe to $followed, otherwise FALSE
     */
    public static function has_subscribed($followed, $follower)
    {
        return parent::find(array("conditions"=>array("followed = ? AND follower = ?", $followed, $follower)))?TRUE:FALSE;
    }
    /**
     * fetches all followed user IDs which given followes has been subscribed to.
     * @param string|integer $follower the follower user ID
     * @return array of \core\db\models\subscribe
     */
    public static function fetch_subscribed($follower)
    {
        return parent::all(array("conditions" => array("follower = ?", $follower), "select" => "followed"));
    }
}