<?php
namespace core\db\models;

/**
 * Profile Entity
 */
class notification_type extends \core\db\models\baseModel
{
    static $has_many = array("notification");
}