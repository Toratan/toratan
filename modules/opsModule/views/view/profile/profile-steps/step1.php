<style>
    div#step1 div#name-section div{float:left;}
    div#step1 div#birthday-section {padding-bottom: 20px;}
    div#step1 div#gender select,
    div#step1 div#birthday-section select{float: left;margin-right: 20px;width: 130px; width: 200px}
    div string{margin: auto auto 1% auto}
    .row div{padding-right: 10%}
    .row{padding: 1%}
</style>
<div id="step1">
    <div id="name-section" class="row">
        <div class="<?php echo isset($this->errors["first_name"])?"has-error":""?>">
            <label class="control-label"><strong class="">First Name</strong></label>
            <input type="text" name="first_name" class="form-control input input-medium  col-lg-4" required value="<?php echo $this->profile->first_name ?>" <?php \strlen($this->profile->first_name)?"":"autofocus" ?>/>
        </div>
        <div class="">
            <label class="control-label"><strong>Nick Name</strong></label>
            <input type="text" name="nick_name" class="form-control input input-medium col-lg-4" value="<?php echo $this->profile->nick_name ?>"/>
        </div>
        <div class="<?php echo isset($this->errors["first_name"])?"has-error":""?>">
            <label class="control-label"><strong class="">Last Name</strong></label>
            <input type="text" name="last_name"  class="form-control input input-medium col-lg-4" required value="<?php echo $this->profile->last_name ?>"/>
        </div>
    </div>
    <div class="clearfix" ></div>
    <hr />
    <div id="birthday-section" class="clearfix">
        <label class="control-label"><strong class="" >Birthday</strong></label>
        <div class="">
            <select name="birth_month" class="form-control" required>
                <option disabled="disabled" style="color:#e6e6e6">Month</option>
                <option value='0'>Prefer not to disclose</option>
                <?php $i=1; while($i<=12) echo "<option ".(($this->profile->birth_month==$i)?'selected="selected"':'')." value='$i'>".date('F',mktime(0,0,0,$i++))."</option>"; ?>
            </select>
            <select name="birth_day" class="form-control" required>
                <option disabled="disabled" style="color:#e6e6e6">Day</option>
                <option value='0'>Prefer not to disclose</option>
                <?php $i=1; while($i<=31) echo "<option ".(($this->profile->birth_day==$i)?'selected="selected"':'')." value='$i'>".$i++."</option>"; ?>
            </select>
            <select name="birth_year" class="form-control" required>
                <option disabled="disabled" style="color:#e6e6e6">Year</option>
                <option value='0'>Prefer not to disclose</option>
                <?php $i=date('Y'); while($i>=1900) echo "<option ".(($this->profile->birth_year==$i)?'selected="selected"':'')." value='$i'>".$i--."</option>"; ?>
            </select>
        </div>
    </div>
    <div class="clearfix" ></div>
    <hr />
    <div id="gender" class="clearfix" style="padding-bottom: 20px;">
        <label class="control-label"><strong class="">Gender</strong></label>
        <div class="">
            <select name="is_male" class="form-control" required>
                <option <?php echo $this->profile->is_male == -1?'selected="selected"':''?> value="-1">Prefer not to disclose</option>
                <option <?php echo $this->profile->is_male ==  1?'selected="selected"':''?> value="1">Male</option>
                <option <?php echo !$this->profile->is_male?'selected="selected"':''?> value="0">Female</option>
            </select>
        </div>
    </div>
</div>