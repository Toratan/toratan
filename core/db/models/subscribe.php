<?php
namespace core\db\models;

class subscribe extends \core\db\models\baseModel
{
    public static function subscribe($followed, $follower)
    {
        return parent::create(array("followed" => $followed, "follower" => $follower));
    }
    public static function unsubscribe($followed, $follower)
    {
        return parent::delete_all(array("conditions"=>array("followed = ? AND follower = ?", $followed, $follower)));
    }
    public static function has_subscribed($followed, $follower)
    {
        return parent::find(array("conditions"=>array("followed = ? AND follower = ?", $followed, $follower)))?TRUE:FALSE;
    }
}