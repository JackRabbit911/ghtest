<form id="<?=$id?>" class="form-horizontal <?=$class?>"  name="<?=$name?>" data-action="<?=$action?>" method="post">

    <?php foreach($fields AS $field): ?>
    
    <?php echo $field->render(); ?>
    
    <?php endforeach; ?>
    
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default">Отправить</button>
    </div>
  </div>
</form>