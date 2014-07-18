<style>
    div#step1 input.input-select,
    div#step1 select{float: left;margin-right: 20px;min-width: 120px;max-width: 255px}
</style>
<div id="step1">
    <div id="name-section" class="row">
        <div class="<?php echo isset($this->errors["first_name"])?"has-error":""?> col-md-4">
            <label class="control-label">First Name</label>
            <input type="text" name="first_name" class="form-control input input-medium" required value="<?php echo $this->profile->first_name ?>" <?php \strlen($this->profile->first_name)?"":"autofocus" ?>/>
        </div>
        <div class="col-md-4">
            <label class="control-label">Nick Name</label>
            <input type="text" name="nick_name" class="form-control input input-medium" value="<?php echo $this->profile->nick_name ?>"/>
        </div>
        <div class="<?php echo isset($this->errors["first_name"])?"has-error":""?> col-md-4">
            <label class="control-label">Last Name</label>
            <input type="text" name="last_name"  class="form-control input input-medium" required value="<?php echo $this->profile->last_name ?>"/>
        </div>
    </div>
    <div class="clearfix"></div>
    <hr />
    <div id="birth-section">
        <style type="text/css">
            #step1 .table tbody tr td:first-child{padding-left:0;vertical-align: middle;width: 120px!important}
            #step1 .table td {border: 0;}
        </style>
        <table class="table table-responsive">
            <tbody>
                <tr>
                    <td><label class="control-label">Birthday</label></td>
                    <td class="row">
                        <select name="birth_month" class="form-control col-md-4" required>
                            <option disabled="disabled" style="color:#e6e6e6">Month</option>
                            <option value='0'>Prefer not to disclose</option>
                            <?php $i=1; while($i<=12) echo "<option ".(($this->profile->birth_month==$i)?'selected="selected"':'')." value='$i'>".date('F',mktime(0,0,0,$i++))."</option>"; ?>
                        </select>
                        <select name="birth_day" class="form-control col-md-4" required>
                            <option disabled="disabled" style="color:#e6e6e6">Day</option>
                            <option value='0'>Prefer not to disclose</option>
                            <?php $i=1; while($i<=31) echo "<option ".(($this->profile->birth_day==$i)?'selected="selected"':'')." value='$i'>".$i++."</option>"; ?>
                        </select>
                        <select name="birth_year" class="form-control col-md-4" required>
                            <option disabled="disabled" style="color:#e6e6e6">Year</option>
                            <option value='0'>Prefer not to disclose</option>
                            <?php $i=date('Y'); while($i>=1900) echo "<option ".(($this->profile->birth_year==$i)?'selected="selected"':'')." value='$i'>".$i--."</option>"; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Gender</label></td>
                    <td class="row">
                        <select name="is_male" class="form-control col-md-4" required>
                            <option <?php echo $this->profile->is_male == -1?'selected="selected"':''?> value="-1">Prefer not to disclose</option>
                            <option <?php echo $this->profile->is_male ==  1?'selected="selected"':''?> value="1">Male</option>
                            <option <?php echo !$this->profile->is_male?'selected="selected"':''?> value="0">Female</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <hr />
    <div id="country-section" class="pull-left">
        <table class="table table-responsive">
            <tbody>
                <tr>
                    <td><label class="control-label">Country</label></td>
                    <td class="row">
                        <?php echo \modules\opsModule\views\helper\countriesHelper::__renderAsHTMLSelect("country", $this->profile->country, "form-control"); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="city-section" class="pull-left">
        <table class="table table-responsive">
            <tbody>
                <tr>
                    <td><label class="control-label">City</label></td>
                    <td class="row">
                        <input type="text" name="city" class="form-control input-select" value="<?php echo $this->profile->city ?>" placeholder="Town"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="clearfix"></div>
</div>