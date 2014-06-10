<?php
namespace core\ui\html;
/**
 * Fetches a proper avatar image address
 */
class avatar
{
    public static function make_thumbnail(
        $src, 
        $dest, 
        $desired_width = 200, 
        $auto_height = 0,
        $desired_height = 200) 
    {
        list($crop_width, $crop_height) = \getimagesize($src);
        
        return self::make_crop($src, $dest, 0, 0, $crop_width, $crop_height, $desired_width, $auto_height, $desired_height);
    }
    public static function make_crop(
        $src, 
        $dest, 
        $crop_start_x = 0, 
        $crop_start_y = 0, 
        $crop_width = 200, 
        $crop_height = 200, 
        $desired_width = 200, 
        $auto_height = 0,
        $desired_height = 200)
    {
	/* read the source image */
        switch(TRUE)
        {
            case preg_match('/[.](jp(e|eg|g)?)$/', $dest):
                 $source_image = imagecreatefromjpeg($src);
                break;
            case preg_match('/[.](gif)$/', $dest):
                $source_image = imagecreatefromgif($src);
                break;
            case preg_match('/[.](png)$/', $dest):
                $source_image =  imagecreatefrompng($src);
                break;
            default:
                throw new \zinux\kernel\exceptions\invalidOperationException("Image type `".end(\array_filter(\explode(".", $dest)))."` not supported");
        }
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	/* find the "desired height" of this thumbnail, relative to the desired width  */
        if($auto_height)
            $desired_height = floor($height * ($desired_width / $width));
        
	/* create a new, "virtual" image */
        $virtual_image = ImageCreateTrueColor( $desired_width, $desired_height);
        
	/* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, $crop_start_x, $crop_start_y, $desired_width, $desired_height, $crop_width, $crop_height);
        
        /* check permission */
        if(!is_writable(dirname($dest)))
            throw new \zinux\kernel\exceptions\accessDeniedException("Permission denied to `".dirname($dest)."`");
        
	/* create the physical thumbnail image to its destination */
        imagejpeg($virtual_image, $dest);
        
        /* indicate the success */
        return true;
    }
    /**
     * fetches avatar's link
     * @param string|integer $user_id the target user ID
     * @return array of @list($avatar_uri , $def_avatar, $orig_avatar)
     * @throws \zinux\kernel\exceptions\notFoundException if no profile found @ user ID
     */
    public static function get_avatar_link($user_id)
    {  
        /**
         * validate and fetch proper avatar URI here
         */
        # fetch the profile of user ID, ignore the cache data for user, we want realtime data from user profile
        $profile = \core\db\models\profile::getInstance($user_id, 0, 0);
        # validate the profile
        if(!$profile)
            throw new \zinux\kernel\exceptions\notFoundException("The profile not found!");
        # default avatar
        $def_avatar = $thumbnail_uri = $orig_uri = "/access/img/anonymous-".($profile->is_male?"":"fe")."male.jpg";
        # fetch profile's avatar settings
        $avatar = $profile->getSetting("/profile/avatar/");
        # validate if any avatar has been set?
        if(isset($avatar->image) && strlen($avatar->image))
            $orig_uri = $avatar->image;
        # validate if any avatar's thumbnail  has been set?
        if(isset($avatar->thumbnail) && strlen($avatar->thumbnail))
            $thumbnail_uri = $avatar->thumbnail;
        # return the avatars
        return array($thumbnail_uri, $def_avatar, $orig_uri);
    }
}