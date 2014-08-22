<?php
namespace core\db\models;

class link extends item
{
    /**
     * Save the link
     * @param boolean $validate should validate
     */
    public function save($validate=true)
    {
        # validate the links_body
        $this->validateBody($this->link_body);
        # since the body is valid now, save this link
        parent::save($validate);
    }
    /**
     * Validates link's body
     * @param string $body
     * @throws \zinux\kernel\exceptions\invalidArgumentException if The body is not valid URL
     */
    protected function validateBody(&$body) {
        # if the body is empty?
        if(!strlen($body))
            throw new \zinux\kernel\exceptions\invalidArgumentException("The link's URL cannot be empty.");
        # any URL should start with a letter
        $body = preg_replace("#^[^a-z]*#i", "", $body);
        # if no schema provided by default choose HTTP schema
        if(!parse_url($body, PHP_URL_SCHEME))
                $body = "http://$body";
        # final validation for URL
        if(!filter_var($body, FILTER_VALIDATE_URL))
                throw new \zinux\kernel\exceptions\invalidArgumentException("The `<b>$body</b>` is not a valid URL.");  
    }
    /**
     * Creates a new link in { title | body } datastructure
     * @param string $title the link's title
     * @param string $body the link's body
     * @param string $parent_id the link's parent id
     * @param string $owner_id the link's owner
     * @throws \zinux\kernel\exceptions\invalidArgumentException if title not string or be empty OR if The body is not valid URL 
     * @throws \zinux\kernel\exceptions\invalidOperationException if duplication problem raise during saving link to db
     * @throws \core\db\models\Exception if any other exception raised that didn't match with previous excepions
     * @return link the create link
    */
    public function newItem($title, $body, $parent_id, $owner_id) {
        // validate link's body
        $this->validateBody($body);
        return parent::newItem($title, $body, $parent_id, $owner_id);
    }
    /**
     * Edits a link
     * @param string $link_id the link's id
     * @param string $owner_id the link's owner id
     * @param string $title string the link's title
     * @param string $body the link's body
     * @param string $parent_id the item's new parent ID, pass '<b>item::NOCHANGE</b>' to don't chnage
     * @param boolean $is_public should it be public or not, pass '<b>link::NOCHANGE</b>' to don't chnage
     * @param boolean $is_trash should it be trashed or not, pass '<b>link::NOCHANGE</b>' to don't chnage
     * @param boolean $is_archive should it be archived or not, pass '<b>link::NOCHANGE</b>' to don't chnage
     * @throws \core\db\exceptions\dbNotFoundException if the link not found
     * @throws \zinux\kernel\exceptions\invalidArgumentException if title not string or be empty OR if The body is not valid URL 
     * @return link the create link
     */
    public function edit($link_id, $owner_id, $title, $body,
            $parent_id = self::NOCHANGE, $is_public = self::NOCHANGE,
            $is_trash = self::NOCHANGE, $is_archive = self::NOCHANGE) {
        // validate link's body
        $this->validateBody($body);
        return parent::edit($link_id, $owner_id, $title, $body, $parent_id, $is_public, $is_trash, $is_archive);
    }
}