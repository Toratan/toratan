<style>
    div#profile-content div#step2 div strong{margin-top: 7px;}
    textArea {max-width: 100%; min-height: 80px;}
</style>
<div id="step2">
    <div class="" >
        <strong class="">Introduction</strong>
        <textarea type="text" name="intro" class="form-control input input-medium" value=""  autofocus></textarea>
    </div>
    <div class="clearfix" ></div>
    <hr />
    <div class="">
        <strong class="">Occupation</strong>
        <textarea type="text" name="occu" class="form-control input input-medium"  value=""></textarea>
    </div>
    <div class="">
        <strong class="">Education</strong>
        <textarea type="text" name="edu" class="form-control input input-medium" value=""></textarea>
    </div>
    <div class="clearfix"></div>
    <hr />
    
    <div id="block3" class="block">
        <div class="">
            <strong class="">Country</strong>
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
            <strong class="">Town</strong>
            <input type="text" name="city" class="form-control input input-medium " value="" />
        </div>
    </div>
</div>
<div class="clearfix" style="margin-top: 10px;" ></div>