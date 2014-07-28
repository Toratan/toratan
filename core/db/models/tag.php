<?php
namespace core\db\models;

class tag extends baseModel
{
    static $has_many = array(
        array('note_tags', "class_name" => "\\core\\db\\models\\note_tag"),
        array('notes', 'through' => '\\core\\db\\models\\note_tags')
    );
    public function save($validate=true)
    {
        $this->tag_value = ucwords(trim($this->tag_value));
        if(!strlen($this->tag_value))
            throw new \zinux\kernel\exceptions\invalidArgumentException("Tag's Value cannot be empty!");
        parent::save($validate);
    }
    /**
     * Seach tags that is matched with passed tag's value
     * @param string $tag The tag value
     * @return tag  The tag or NULL if not existed
     */
    public static function search($tag) {
        $builder = new \ActiveRecord\SQLBuilder(self::connection(), self::table_name());     
        $builder->select("*")->where("`tag_value` = ?", array($tag))->limit("1");
        return self::find_by_sql($builder->to_s(), $builder->bind_values());
    }
    /**
     * Seach tags that is similar with passed tag's value
     * @param string $tag The tag value
     * @return array of Tags
     */
    public static function search_similar($tag) {
        $builder = new \ActiveRecord\SQLBuilder(self::connection(), self::table_name());     
        $builder->select("*")->where("`tag_value` LIKE ?", array("%$tag%"))->limit("14");
        return self::find_by_sql($builder->to_s(), $builder->bind_values());
    }
    /**
     * Create new tag, if already existed? it will be ignored
     * @param string $tag The tag value
     * @return tag (READONLY) of created or already existed tag.
     */
    public static function create($tag) {
        $t = new self;
        try {
            $t->tag_value = $tag;
            $t->save();
        } catch(\core\db\exceptions\alreadyExistsException $aee) {
            unset($aee);
            $t = self::first(array("conditions" => array("tag_value = ?", $tag)));
        }
        $t->readonly();
        return $t;
    }
}