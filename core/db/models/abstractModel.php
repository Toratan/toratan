<?php
namespace core\db\models;

abstract class abstractModel extends baseModel
{
    /**
     * In certain operations that we want to do NOOP on some
     * prespective of item we pass this
     */
    const NOOP = -1;
    /**
     * the set flag value
     */
    const FLAG_SET = 1;
    /**
     * the unset flag value
     */
    const FLAG_UNSET = 0;
    /**
     * flag whatever
     */
    const WHATEVER = self::NOOP;
    /**
     * Get/Set item's table name
     * @var string
     */
    protected $item_table_name;
    /**
     * Get/Set item's raw name
     * @var string
     */
    protected $item_name;

    public function __construct(array $attributes = array(), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
        # fetch the cache sig.
        $cache_sig = get_called_class();
        # create a new cache
        # don't use xCache it will overload the session file
        $fc = new \zinux\kernel\caching\fileCache(__CLASS__);
        # check the cache if the $cache_sig has loaded before
        if($fc->isCached($cache_sig))
            # just fetch from cache system
            $item_info = $fc->fetch ($cache_sig);
        else
        {
            # a fast class name fetching [ do not use (str/preg)_replace we need it to be fast ]
            $this->item_table_name =
                    # we need to use \ActiveRecord\Inflector to normalize our table in activerecord way
                    \ActiveRecord\Inflector::instance()->tableize(($this->item_name = substr($cache_sig, strrpos($cache_sig, "\\")+1)));
             # create an item info package
             $item_info = array("item_table_name" => $this->item_table_name, "item_name" => $this->item_name);
             # save the { namespace\class => class, table_name } comb.
             $fc->save($cache_sig, $item_info);
        }
        # load item's table name
        $this->item_table_name = $item_info["item_table_name"];
        # load item's name
        $this->item_name = $item_info["item_name"];
        # we have now fetched our item info
        # we will set our table name's to its proper value
        parent::$table_name = $this->item_table_name;
        # after setting the table's name we go for parent contruction
        parent::__construct($attributes, $guard_attributes, $instantiating_via_find, $new_record);
        # unset the static table name
        self::$table_name = "";
    }
}