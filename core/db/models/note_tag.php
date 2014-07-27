<?php
namespace core\db\models;

class note_tag extends baseModel
{
    static $belongs_to = array(
        array('note'),
        array('tag')
    );
    public static function tagit_array(note $note, array $tags) {
        $note->readonly();
        $count = 0;
        foreach($tags as $tag) {
            try{
                $nt = new self;
                $nt->note_id = $note->note_id;
                $nt->tag_id = tag::create($tag)->tag_id;
                $nt->save();
                $count++;
            } catch(\core\db\exceptions\alreadyExistsException $aee) {
               # ignore
                unset($aee);
            }
        }
        $note->readonly(false);
        return $count;
    }
    public static function tagit(note $note, $tag) { return self::tagit_array($note, array($tag)); }
}