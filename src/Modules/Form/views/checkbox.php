<div class="form-group <?=$class?>">
    <div class="col-sm-offset-2 col-sm-10">
        
        <? foreach($label AS $key => $label_str): ?>
        
      <div class="<?=$type?>">
        <label>
            <?if(isset($value[$key]) && ($value[$key] === 'on' || $value[$key] === TRUE)) $checked = ' checked="checked"';
            else $checked = ''; 
            ?>
            <input name="<?=$name.'['.$key.']'?>" type="<?=$type?>"<?=$checked?>> <?=$label_str?>
        </label>
      </div>
        
        <? endforeach; ?>
    </div>
  </div>