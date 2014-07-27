<?php
namespace core\db\models;

class note_tag extends baseModel
{
    public static function tagit_array(note $note, array $tags) {
//        \zinux\kernel\utilities\debug::_var(func_get_args(), 1);
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
}