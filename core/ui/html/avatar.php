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
                throw new \zinux\kernel\exceptions\invalideArgumentException("No support exists for '$avatar_type'....");
        }
        switch(\strtolower($size))
        {
            case self::SMALL_SIZE:
            case self::MEDIUM_SIZE:
            case self::LARGE_SIZE:
                break;
            default:
                throw new \zinux\kernel\exceptions\invalideArgumentException("No size support exists for '$size'....");
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
}