<style>
    div#profile-content div#step3 div strong{margin-top: 7px;}
    div#profile-content div#step3 .bit-left {margin-left:0px;}
    div#step3 a{margin: 0%;cursor: pointer}
    input[type=text]{margin-top: 2px}
</style>
<script type="text/javascript">
    $(document).ready(function(){
        $(".addanother").click(function($e){
            $e.preventDefault();
            $(this).prev().append($(this).prev().prev().clone().val(""));
        });
    });
</script>
<div id="step3">
    <div class="form-group">
        <strong class="control-label">Public Email Address</strong>
        <?php if(!count($this->profile->public_email)): ?>
        <input type="text" class="form-control input input-medium" name="public_email[]" value="" autofocus/>
        <?php else: foreach(explode(";", $this->profile->public_email) as $value): ?>
        <input type="text" class="form-control input input-medium" name="public_email[]" value="<?php echo $value ?>"/>
        <?php endforeach; endif; ?>
        <div class="addanother-container"></div>
        <a class="addanother">+ Add another</a>
    </div>
    
    <div class="clearfix"></div>
    <hr />
    
    <div class=" bit-left form-group">
        <strong class="control-label">Phone Number</strong>
        <?php if(!count($this->profile->phone)): ?>
        <input type="text" class="form-control input input-medium" name="phone[]" value="" />
        <?php else: foreach(explode(";", $this->profile->phone) as $value): ?>
        <input type="text" class="form-control input input-medium" name="phone[]" value="<?php echo $value ?>" />
        <?php endforeach; endif; ?>
        <div class="addanother-container"></div>
        <a class="addanother">+ Add another</a>
    </div>
    
    <div class="clearfix"></div>
    <hr />
    
    <div class="bit-left form-group">
        <strong class="control-label">Online Site</strong>
        <?php if(!count($this->profile->site)): ?>
        <input type="text" class="form-control input input-medium" name="site[]" value="" />
        <?php else: foreach(explode(";", $this->profile->site) as $value): ?>
        <input type="text" class="form-control input input-medium" name="site[]" value="<?php echo $value ?>" />
        <?php endforeach; endif; ?>
        <div class="addanother-container"></div>
        <a class="addanother">+ Add another</a>
    </div>
</div>