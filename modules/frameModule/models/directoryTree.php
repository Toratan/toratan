<?php
namespace modules\frameModule\models;
/**
* The directory tree handler
*/
class directoryTree extends \stdClass
{
    const REGULAR = 0x1;
    const TRASH = 0x2;
    const ARCHIVE = 0x3;
    const SHARED = 0x4;
    /**
     * The type directory Tree
     * @var integer
     */
    public $tree_type;
    /**
     * The request
     * @var \zinux\kernel\routing\request
     */
    public $request;
    /**
     * Construct a new directory tree
     * @param \zinux\kernel\routing\request $request The current request detail
     * @param integer $__tree_type should be one of <i>\modules\frameModule\models\directoryTree::{<b>REGULAR</b>, <b>TRASH</b>, <b>ARCHIVE</b>, <b>SHARED</b>}</i>
     */
    public function __construct (\zinux\kernel\routing\request $request, $__tree_type = self::REGULAR)
    {
        $this->request = $request;
        $this->tree_type = $__tree_type;
    }
    /**
     * PLots options of directory tree
     * @param string $active_type Which type this directory tree contains?
     * @param string|integer $pid the parrent directory this directory tree
     * @param boolean $is_owner is current user is owner of current tree?
     */
    public function plotOptions($active_type, $pid, $is_owner) { if($is_owner) require 'directoryTree-submodules/headOptions.phtml'; }
    /**
     * PLots head types of directory tree
     * @param string $active_type Which type this directory tree contains?
     * @param string|integer $pid the parrent directory this directory tree
     */
    public function plotHeadTypes($active_type, $pid) { $active_type = \ActiveRecord\Utils::pluralize($active_type); require 'directoryTree-submodules/headTypes.phtml'; }
    /**
     * PLots head next-prev
     * @param string $active_type Which type this directory tree contains?
     * @param string|integer $pid the parrent directory this directory tree
     */
    public function plot_next_prev_links($active_type, $pid) { if($this->tree_type === self::REGULAR) return; require 'directoryTree-submodules/nextPrevLinks.phtml'; }
    /**
     * Plots general JS for options of directory tree
     * @param string $active_type Which type this directory tree contains?
     * @param string|integer $pid the parrent directory this directory tree
     * @param boolean $is_owner is current user is owner of current tree?
     */
    public function plotJS($type, $parent_id, $is_owner) { require 'directoryTree-submodules/general-js.phtml'; }
    /**
     * PLots table's header html/js/css
     * @param string $active_type Which type this directory tree contains?
     */
    public function plotTableHeader($active_type) { require 'directoryTree-submodules/tableHeader.phtml'; }
    /**
     * PLots table's footer html/css
     */
    public function plotTableFooter() { require 'directoryTree-submodules/tableFooter.phtml'; }
    /**
     * Get a proper navigation link
     * @param \core\db\models\item $item The target item
     * @return string the navigation pure link(i.e no a &lt;a&gt; link just the content of `href`)
     */
    public function getNavigationLink(\core\db\models\item $item) {
        switch($item->WhoAmI()) {
            case "note":
                return "/view/note/{$item->note_id}";
            case "link":
                return "/goto/link/{$item->link_id}/".\zinux\kernel\security\hash::Generate($item->link_id, 1, 1);
            case "folder":
                return "/d/{$item->folder_id}.folders";
            default:
                trigger_error("Undefined `{$item->WhoAmI()}` item ", E_USER_ERROR);
        }
    }
    /**
     * Get proper value of `taget` for &lt;a&gt;
     * @param \core\db\models\item $item The target item
     * @return string The target value
     */
    protected  function getNavigationTarget(\core\db\models\item $item) {
        switch($item->WhoAmI()) {
            case "note": return "_top";
            case "link": return "_blank";
            case "folder": return "_self";
            default:
                trigger_error("Undefined `{$item->WhoAmI()}` item ", E_USER_ERROR);
        }
    }
    /**
     * Get proper status icon of an item
     * @param \core\db\models\item $item The target item
     * @return the status icons string collection
     */
    public function getStatusIcons(\core\db\models\item $item) {
        $si = "";
        $counter = 0;
        if($item->is_public && ++$counter)
            $si .= "<span class='glyphicon glyphicon-share-alt' title='Shared'></span>";
        if($item->is_trash && ++$counter)
            $si .= "<span class='glyphicon glyphicon-trash' title='Deleted'></span>";
        if($item->is_archive && ++$counter)
            $si .= " <span class='glyphicon glyphicon-save' title='Archived'></span>";
        if($counter < 3) {
            switch($item->WhoAmI()) {
                case "note":
                    $si = "<span class='glyphicon glyphicon-file' title='Note'></span> $si";
                    break;
                case "link":
                    $si = "<span class='glyphicon glyphicon-link' title='Link'></span> $si";
                    break;
                case "folder":
                    $si = "<span class='glyphicon glyphicon-folder-close' title='Folder'></span> $si";
                    break;
                default:
                    trigger_error("Undefined `{$item->WhoAmI()}` item ", E_USER_ERROR);
            }
        }
        return $si;
    }
    /**
     * Get proper CSS class value for an item's checkbox
     * @param \core\db\models\item $item The target item
     * @return string The classes string collection
     */
    public function getCheckBoxClasses(\core\db\models\item $item) {
        $cbc = "item-checkbox";
        if($item->is_public) $cbc .= " public-item";
        if(!$item->is_public) $cbc .= " private-item";
        return $cbc;
    }
    /**
     * Get binary representaion of item's status properties<br />
     * The binary format is `<i>{is_public}{is_archive}{is_trash}</i>`
     * @param \core\db\models\item $item The target item
     * @return string The binary representaion of item's status properties
     */
    public function getStatusBinary(\core\db\models\item $item) {
        return ($item->is_public?"1":"0").($item->is_archive?"1":"0").($item->is_trash?"1":"0");
    }
    /**
     * Get inverse verbose representaion of item's status properties
     * @param \core\db\models\item $item The target item
     * @return string The inverse verbose representaion of item's status properties
     */
    public function getStatusString(\core\db\models\item $item) {
        $s = "";
        $s .= ("&share=".($item->is_public?"0":"1"));
        $s .= ("&archive=".($item->is_archive?"0":"1"));
        $s .= ("&trash=".($item->is_trash?"0":"1"));
        return $s;
    }
    /**
     * Get checkbox POST value for an item
     * @param \core\db\models\item $item The target value
     * @return string the value
     */
    public function getPostCheckVal(\core\db\models\item $item) {
         return htmlentities(\modules\opsModule\models\itemInfo::encode($item));
    }
    /**
     * Plots a row of table
     * @param \core\db\models\item $item The target item to plot
     * @param string $active_type Which type this directory tree contains?
     * @param string|integer $pid the parrent directory this directory tree
     * @param boolean $is_owner is current user is owner of current tree?
     * @throws \zinux\kernel\exceptions\invalideArgumentException if item is NULL
     */
    public function plotTableRow(\core\db\models\item $item, $type, $parent_id, $is_owner, $part_of_all_precent = 0) {
        if($item === NULL) {
            throw new \zinux\kernel\exceptions\invalideArgumentException("The item cannot be null...");
        }
        static $fetch_more_meet = 0;
        require 'directoryTree-submodules/table-row.phtml';
    }
    /**
     * Plot entire collection directory tree
     * @param string $active_type Which type this directory tree contains?
     * @param array $collection The collection to plot
     * @param string|integer $pid the parrent directory this directory tree
     * @param boolean $is_owner is current user is owner of current tree?
     */
    public  function plotItems($active_type, array $collection, $parent_id, $is_owner) {
        $this->plotTableHeader($active_type);
        $index = 0;
        $all_count = count($collection);
        foreach($collection as $folder)
        {
            $this->plotTableRow($folder, $active_type, $parent_id, $is_owner, (++$index)/$all_count);
        }
        $this->plotTableFooter();
        if(!count($collection)) {
            require 'directoryTree-submodules/table-empty.phtml';
            return;
        }
        $this->plotTableJS($active_type);
    }
    /**
     * Plots necessary JS for table operations
     * @param string $active_type Which type this directory tree contains?
     */
    public function plotTableJS($active_type) {
        require 'directoryTree-submodules/table-js.phtml';
    }
    /**
     * Plots collection as { `Type`: `Folder` }
     * @param array $collection The collection to plot
     * @param string|integer $pid the parrent directory this directory tree
     * @param boolean $is_owner is current user is owner of current tree?
     */
    public function plotFolders(array $collection, $parent_id, $is_owner) {
        $this->plotItems("folder", $collection, $parent_id, $is_owner);
    }
    /**
     * Plots collection as { `Type`: `Note` }
     * @param array $collection The collection to plot
     * @param string|integer $pid the parrent directory this directory tree
     * @param boolean $is_owner is current user is owner of current tree?
     */
    public function plotNotes(array $collection, $parent_id, $is_owner) {
        $this->plotItems("note", $collection, $parent_id, $is_owner);
    }
    /**
     * Plots collection as { `Type`: `Link` }
     * @param array $collection The collection to plot
     * @param string|integer $pid the parrent directory this directory tree
     * @param boolean $is_owner is current user is owner of current tree?
     */
    public function plotLinks(array $collection, $parent_id, $is_owner) {
        $this->plotItems("link", $collection, $parent_id, $is_owner);
    }
}