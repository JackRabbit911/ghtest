<div class="form-group <?=$class?>">
    <label for="<?=$name?>" class="col-sm-2 control-label"><?=$label?></label>
    <div class="col-sm-10">
      <input name="<?=$name?>" type="<?=$type?>" class="form-control" id="<?=$name?>" value="<?=$value?>" placeholder="<?=$plh?>" <?=$disabled?>>
      <div class="help-block"><?=$error?></div>
    </div>
  </div>