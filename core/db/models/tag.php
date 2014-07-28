<?php
namespace core\db\models;

class tag extends baseModel
{
    static $has_many = array(
        array('note_tags', "class_name" => "note_tag"),
        array('notes', 'through' => 'note_tags', "order" => "notes.popularity DESC")
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
        return array_shift(self::find_by_sql($builder->to_s(), $builder->bind_values()));
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
    /**
     * Fetch related notes that are bind to this tag
     * @param integer $offset The query offset
     * @param integer $limit The query limit(default: 10)
     * @param string $order The query order(default: `popularity DESC`)
     * @return array of array(count_of_all_notes_with_this_tag, current_result_due_to_offset_and_limit)
     */
    public function fetch_related_notes($offset, $limit = 10, $order = "popularity DESC") {
        $tags = note_tag::all(array("conditions" => array("tag_id = ?", $this->tag_id), "offset" => $offset, "limit" => $limit, "select" => "note_id"));
        $in = "";
        foreach($tags as $tag) {
            $in = "$in, '$tag->note_id'";
        }
        $in_val = trim($in, ", ");
        $builder = new \ActiveRecord\SQLBuilder(note::connection(), note::table_name());
        $builder->select("note_id, popularity")->where("note_id IN ($in_val)")->limit($limit)->offset($offset)->order($order);
        return array(count($tags), note::find_by_sql($builder->to_s(), $builder->bind_values()));
    }
}