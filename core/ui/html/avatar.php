<?php
namespace core\ui\html;
/**
 * Fetches a proper avatar image address
 */
class avatar
{
    const TWITTER = 'twitter';
    const FACEBOOK = 'facebook';
    const GRAVATAR = 'gravatar';
    const INSTAGRAM = 'instagram';
    const SMALL_SIZE = "small";
    const MEDIUM_SIZE = "medium";
    const LARGE_SIZE = "large";
    public static function fetch_uri($avatar_type, $email_or_id, $size = self::SMALL_SIZE,$auto_echo = 1)
    {
        switch(\strtolower($avatar_type))
        {
            case self::TWITTER:
            case self::FACEBOOK:
            case self::GRAVATAR:
                break;
            default:
                throw new \zinux\kernel\exceptions\invalidArgumentException("No support exists for '$avatar_type'....");
        }
        switch(\strtolower($size))
        {
            case self::SMALL_SIZE:
            case self::MEDIUM_SIZE:
            case self::LARGE_SIZE:
                break;
            default:
                throw new \zinux\kernel\exceptions\invalidArgumentException("No size support exists for '$size'....");
        }
        $uri ="//avatars.io/$avatar_type/$email_or_id?size=$size";
        if($auto_echo)
            echo $uri;
        else
            return $uri;
    }
    public static function fetch_img($avatar_type, $email_or_id, $size = self::SMALL_SIZE, $alt='', $title = "", $style='', $css_class='',$auto_echo = 1)
    {
        $img = "<img src='".self::fetch_uri($avatar_type, $email_or_id, $size, 0)."' alt='$alt' title='$title' style='$style' class='$css_class'/>";
        if($auto_echo)
            echo $img;
        else
            return $img;
    }
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
        if(!is_writable($dest))
            throw new \zinux\kernel\exceptions\accessDeniedException;
        
	/* create the physical thumbnail image to its destination */
        imagejpeg($virtual_image, $dest);
        
        /* indicate the success */
        return true;
    }
    /**
     * fetches avatar's link
     * @param string|integer $user_id the target user ID
     * @return array of @list($avatar_uri , $def_avatar)
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
        $def_avatar = $avatar_uri = $orig_avatar = "/access/img/anonymous-".($profile->is_male?"":"fe")."male.jpg";
        # fetch profile's avatar settings
        $avatar = $profile->getSetting("/profile/avatar/");
        # if we any setting on profile's avatar
        if($avatar)
        {
            # if we have any active section
            if(@isset($avatar->{$avatar->activated}))
            {
                # for any supported active section
                # fetch the proper profile's avatar URI
                switch($avatar->activated)
                {
                    case \core\ui\html\avatar::INSTAGRAM:
                    case \core\ui\html\avatar::FACEBOOK:
                    case \core\ui\html\avatar::GRAVATAR:
                    case \core\ui\html\avatar::TWITTER:
                        $orig_avatar = $avatar_uri = self::fetch_uri($avatar->activated, $avatar->{$avatar->activated}->id, \core\ui\html\avatar::LARGE_SIZE, 0);
                        break;
                    case "custom":
                        $avatar_uri = $avatar->{$avatar->activated}->thumb_image;
                        $orig_avatar =  $avatar->{$avatar->activated}->origin_image;
                        break;
                }
                # otherwise just go with default avatar image
            }
            # otherwise just go with default avatar image
        }
        # otherwise just go with default avatar image
        return array($avatar_uri, $def_avatar, $orig_avatar);
    }
}