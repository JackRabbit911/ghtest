<style>
    .help-block {
        margin-top: 0;
        margin-bottom: 0;
    }
</style>

<form id="<?=uniqid()?>" class="form-horizontal <?=$class?>"  name="<?=$form_name?>" action="" method="post" data-action="/user/auth">

    <?= $input ?>
    
    
    

    
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default">Отправить</button>
    </div>
  </div>
</form>