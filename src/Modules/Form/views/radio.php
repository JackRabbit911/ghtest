<?// print_r($value); ?>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        
        <? foreach($label AS $key => $label_str): ?>
        
       
        
        
      <div class="<?=$type?>">
        <label>
            <?if(isset($value) && isset($vars[$key]) && $value === $vars[$key])
            {
                $checked = ' checked'; 
            }
            else
            {
                $checked = ''; 
            }
            ?>
            <input id="<?=uniqid();?>" name="<?=$name?>" type="<?=$type?>"<?=$checked?> value="<?=$vars[$key]?>"> <?=$label_str?>
        </label>
      </div>
        
        <? endforeach; ?>
    </div>
  </div>