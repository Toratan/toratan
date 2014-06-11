<style>
    div#profile-content div#step2 div strong{margin-top: 7px;}
    textArea {max-width: 100%; min-height: 80px;}
</style>
<div id="step2">
    <div>
        <label class="control-label"><strong>Introduction</strong></label>
        <textarea type="text" name="intro" class="form-control input input-medium" <?php echo strlen($this->profile->intro)?"":"autofocus" ?> rows="10"><?php echo $this->profile->intro ?></textarea>
    </div>
    <div class="clearfix"></div>
</div>
<div class="clearfix" style="margin-top: 10px;"></div>