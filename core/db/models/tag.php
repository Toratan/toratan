<?php
namespace core\db\models;

class tag extends \core\db\models\baseModel
{
    public static function search($tag) {
        $builder = new \ActiveRecord\SQLBuilder(self::connection(), self::table_name());     
        $builder->select("*")->where("`tag_value` LIKE ?", array("%$tag%"))->limit("14");
        return self::find_by_sql($builder->to_s(), $builder->bind_values());
    }
}