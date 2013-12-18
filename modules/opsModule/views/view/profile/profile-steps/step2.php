<style>
    div#profile-content div#step2 div strong{margin-top: 7px;}
    div#profile-content div#step2 div .bit-left {margin-left:-10px;}
</style>
<div id="step2">
    <div class="">
        <strong class="">Introduction</strong>
        <input type="text" name="intro" class="form-control input input-medium" value="" autofocus/>
    </div>
    <div class="clearfix" ></div>
    <hr />
    <div class=" " style="margin: 0">
        <strong class=" ">Occupation</strong>
        <input type="text" id="first" name="occu" class="form-control bit-left  input input-medium"  value=""/>
    </div>
    <div class="">
        <strong class="">Education</strong>
        <input type="text" name="edu" class="form-control bit-left  input input-medium" value=""/>
    </div>
    <div class="clearfix"></div>
    <hr />
    
    <div id="block3" class="block">
        <div class="">
            <strong class="">Country</strong>
            <select name="country" class="form-control bit-left input input-medium ">
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
            <input type="text" name="city" class="form-control bit-left input input-medium " value="" />
        </div>
    </div>
</div>
<div class="clearfix" style="margin-top: 10px;" ></div>