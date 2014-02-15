/** inline inject of jquery.insert_sorted.js **/
(function($){
    /**
     * add a new item into a parent node in a sorted manner using binary sort strategy
     * @param jQuery $new the new node's jquery object
     * @param function $fetch_val the function for making decision when sorting them
     * @returns jQuery the inserted item
     **/
    $.fn.insert_sorted = function($new, $validate)
    {
        $parent = this;
        // if this is the first of its siblings
        if(($parent.children()).length === 0)
        {
            // just append it
            $parent.prepend($new);
            // return the appened item
            return $new;
        }
        // fetch the children
        children = $parent.children ();
        // fetch the value of the first child
        // if the new is greater than first child's value
        if($validate($new, $(children[0])) > 0)
        {
            // just append the new one before the first child
            ($new).insertBefore($(children[0]));
            // return the appened item
            return $new;
        }
        // if the new is less than last child's value
        if($validate($new, $(children[children.length - 1])) < 0)
        {
            // just append the new one before the first child
            ($new).insertAfter($(children[children.length - 1]));
            // return the appened item
            return  $new;
        }
       /**
        * init the binery search values
        */
        // the bs' top value
        itop = children.length;
        // the bs' bottom value
        ibot = 0;
        // a fail-safe for making bs recursively decision-able
        counter = 0;
        // here the bs goes
        do
        {
            // validate the medium value of { bottom, top }
            imid = Math.floor((itop + ibot) / 2);
            /**
             * Validate the new child's position from the medium child
             */
            if($validate($new, $(children[imid])) > 0)
                itop = imid;
            else
                ibot = imid;
            // the recursive's fail-safe
            if(counter++ > children.length)
                break;
            // the condition for terminating the recursive
        }while(Math.abs(ibot - itop) > 1);
        /**
         * now that bs' terminated
         */
        // if the new child's value is greater than the bs' last fetched child's value
        if($validate($new, $(children[imid])) > 0)
            // insert the new child after it
            ($new).insertAfter($(children[ibot]));
        // if the new child's value is less or equal than the bs' last fetched child's value
        else
            // insert the new child before it
            ($new).insertBefore($(children[ibot]));
        // return the inserted value
        return $new;
    };
})(jQuery);