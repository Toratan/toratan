<style>
    div#step1 div#name-section div{float:left;}
    div#step1 div#birthday-section {padding-bottom: 20px;}
    div#step1 div#gender select,
    div#step1 div#birthday-section select{float: left;margin-right: 20px;width: 130px}
    div string{margin: auto auto 1% auto}
    .row div{padding-right: 10%}
    .row{padding: 1%}
</style>
<div id="step1">
    <div id="name-section" class="row">
        <div class="">
            <label><strong class="">First Name</strong></label>
            <input type="text" name="first_name" class="form-control input input-medium  col-lg-4" value="" required autofocus/>
        </div>
        <div class="">
            <label><strong>Nick Name</strong></label>
            <input type="text" name="nick_name" class="form-control input input-medium col-lg-4" value="" />
        </div>
        <div class="">
            <label><strong class="">Last Name</strong></label>
            <input type="text" name="last_name"  class="form-control input input-medium col-lg-4" value="" required=""/>
        </div>
    </div>
    <div class="clearfix" ></div>
    <hr />
    <div id="birthday-section" class="clearfix">
        <label><strong class="" >Birthday</strong></label>
        <div class="">
            <select name="birth_month" class="form-control" required>
                <option <?=$this->post_back?'':''/*'selected="selected"'*/?> disabled="disabled" style="color:#e6e6e6">Month</option>
                <?php $i=1; while($i<=12) echo "<option ".((($this->post_back && isset($this->post['birth_month']) && $i==$this->post['birth_month']) || (false && isset($this->user->userprofile->birth_month) && $this->user->userprofile->birth_month==$i))?''/*'selected="selected"'*/:'')." value='$i'>".date('F',mktime(0,0,0,$i++))."</option>"; ?>
            </select>
            <select name="birth_day" class="form-control" required>
                <option disabled="disabled" style="color:#e6e6e6">Day</option>
                <?php $i=1; while($i<=31) echo "<option ".((((false && isset($this->user->userprofile->birth_day) && $this->user->userprofile->birth_day==$i)
                        || ($this->post_back && isset($this->post['birth_day']) && $i==$this->post['birth_day'])))?''/*'selected="selected"'*/:'')." value='$i'>".$i++."</option>"; ?>
            </select>
            <select name="birth_year" class="form-control" required>
                <option disabled="disabled" style="color:#e6e6e6">Year</option>
                <?php $i=date('Y'); while($i>=1900) echo "<option ".((($this->post_back && isset($this->post['birth_year']) && $i==$this->post['birth_year']) || (false && isset($this->user->userprofile->birth_year) && $this->user->userprofile->birth_year==$i))?''/*'selected="selected"'*/:'')." value='$i'>".$i--."</option>"; ?>
            </select>
        </div>
    </div>
    <div class="clearfix" ></div>
    <hr />
    <div id="gender" class="clearfix" style="padding-bottom: 20px;">
        <label><strong class="">Gender</strong></label>
        <div class="">
            <select name="is_male" class="form-control" required>
                <option <?=$this->post_back && isset($this->post['is_male']) && $this->post['is_male']?'selected="selected"':''?> value="1">Male</option>
                <option <?=$this->post_back  && isset($this->post['is_male']) && !$this->post['is_male']?'selected="selected"':''?> value="0">Female</option>
            </select>
        </div>
    </div>
</div>