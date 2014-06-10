<style>
    div#profile-content div#step2 div strong{margin-top: 7px;}
    textArea {max-width: 100%; min-height: 80px;}
</style>
<div id="step2">
    <div class="" >
        <label class="control-label"><strong>Introduction</strong></label>
        <textarea type="text" name="intro" class="form-control input input-medium" <?php echo strlen($this->profile->intro)?"":"autofocus" ?> rows="10"><?php echo $this->profile->intro ?></textarea>
    </div>
    <div class="clearfix" ></div>
    <div class="clearfix"></div>
    <hr />
    <div id="block3" class="block">
        <div class="">
            <label class="control-label"><strong>Country</strong></label>
            <select name="country" class="form-control input input-medium ">
                <option selected="selected" class="" disabled="disabled" style="color:#e6e6e6">Choose</option>
                <?php
                if(false)
                    {
                    $this->l = new Load();
                    $this->l->external_helper();
                    $this->cl = get_country_list();
                    foreach($this->cl as $this->c)
                    {
                        echo "<option ".($this->post_back && isset($this->post['country']) && $this->post['country']==$this->c?'selected="selected"':'')." value='$this->c'>$this->c</option>";


                }}
                ?>
            </select>
        </div>
        <div class="">
            <label class="control-label"><strong>Town</strong></label>
            <input type="text" name="city" class="form-control input input-medium " value="<?php echo $this->profile->city ?>" />
        </div>
    </div>
</div>
<div class="clearfix" style="margin-top: 10px;" ></div>