<style>
    div#profile-content div#step3 div strong{margin-top: 7px;}
    div#profile-content div#step3 .bit-left {margin-left:0px;}
    div#step3 a{margin: 0%;cursor: pointer}
    input.red {border-color: red}
</style>
<script type="text/javascript">
    $(document).ready(function(){
        $(".addanother").click(function($e){
            $e.preventDefault();
            $(this).prev().append($(this).prev().prev().clone().css({"margin-top":"2px"}).val(""));
        });
    });
</script>
<div id="step3">
    <div class="">
        <strong class="">Public Email Address</strong>
        <input type="text" class="form-control input input-medium" name="public_email[]" value="" autofocus/>
        <div class="addanother-container"></div>
        <a class="addanother">+ Add another</a>
    </div>
    
    <div class="clearfix"></div>
    <hr />
    
    <div class=" bit-left">
        <strong class=" ">Phone Number</strong>
        <input type="text" class="form-control input input-medium" name="phone[]" value="" />
        <div class="addanother-container"></div>
        <a class="addanother">+ Add another</a>
    </div>
    
    <div class="clearfix"></div>
    <hr />
    
    <div class="bit-left">
        <strong class="">Online Site</strong>
        <input type="text" class="form-control input input-medium" name="site[]" value="" />
        <div class="addanother-container"></div>
        <a class="addanother">+ Add another</a>
    </div>
</div>