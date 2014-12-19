<?php
namespace modules\opsModule\models;
    
/**
* The modules\opsModule\models\genAvatarName
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class avatarPathName
{
    /**
     * Generates avatar path name due to the origin image file address
     * @param $origin_file_name The origin image address, it can be on local disk or a link to an online image, it does not matter
     * @param \core\db\models\profile $profile A profile instance which the origin file will related to
     * @return array of ($orig_path, $thum_path)
     * @throws \zinux\kernel\exceptions\invalidArgumentException If site's configuration is not valid
     */
    public static function generate($origin_file_name, \core\db\models\profile $profile) {
        # fetch upload location for original image 
        $orig_path = \zinux\kernel\application\config::GetConfig("upload.avatar.original_image_path");
        # fetch upload location for original image 
        $thum_path = \zinux\kernel\application\config::GetConfig("upload.avatar.thumbnail_image_path");
        # if we have a miss configured project
        if(!$orig_path || !$thum_path)
            # indecate it
            throw new \zinux\kernel\exceptions\invalidArgumentException("No configuration found for `upload.avatar`!!");
        # set default extension type
        $ext = "png";
        # validate the actual image file type
        switch(exif_imagetype($origin_file_name)) {
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                # update extension to JPG
                $ext = "jpg";
            case IMAGETYPE_PNG:
                # already is set
                break;
            default:
                throw new \zinux\kernel\exceptions\appException("Only the following file types are supported `jpg, png`.");
        }
        # fetch file's original name
        $alt_name = $origin_file_name;
        # define a counter for naming
        $counter = 0;
        # unlink any possible perviously profile picture
        @\shell_exec("rm -f .".$profile->getSetting("/profile/avatar/image"));
        # while original image file already exists, increase the counters
        while(\file_exists($orig_path.sha1($alt_name.(++$counter)).".$ext")) ;
        # generate a new name for original image
        $alt_name = sha1($alt_name.$counter);
        # generate the original image's paths
        $orig_path .= "$alt_name.$ext";
        # define a counter for naming
        $counter = 0;
        # unlink any possible perviously profile picture
        @\shell_exec("rm -f .".$profile->getSetting("/profile/avatar/thumbnail"));
        # while thumbnail image file already exists, increase the counters
        while(\file_exists($thum_path.sha1($alt_name.(++$counter)."-tmb").".$ext")) ;
        # generate the new name for thumbnail
        $thum_path .= sha1($alt_name.$counter."-tmb").".$ext";
        # return the complete path
        return array($orig_path, $thum_path);
    }
    /**
     * set avatar pathes into a profile instance
     * @param \core\db\models\profile $profile The profile instance(note: the changes will not be save in this method)
     * @param string $orig_path The origin image's path
     * @param string $thum_path The origin's thumbnail image's path
     */
    public static function setProfile(\core\db\models\profile &$profile, $orig_path = NULL, $thum_path = NULL) {
        if($orig_path && strlen($orig_path))
            # setting the profile settings for original image path
            $profile->setSetting("/profile/avatar/image", "$orig_path", 0);
        if($thum_path && strlen($thum_path))
            # setting the profile settings for thumbnail image path
            $profile->setSetting("/profile/avatar/thumbnail", "$thum_path", 0);
    }
}