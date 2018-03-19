
<h2>class <?=$name?></h2>
<b>file:</b> <?=$file?>
<? if(!empty($extends)): ?>
<br><b>extends:</b> <?=$extends?>
<? endif; ?>

<?=_view('api/description', ['desc'=>$desc])?>

<?=_view('api/doc_comment', $comment);?>

<b>module:</b> <?=$module?><br>
<b>namespace:</b> <?=$namespace?><br>

<? if(!empty($parent)): ?>
<b>parent:</b> <a href="<?=$parent['url']?>"<?=$parent['target_link']?>><?=$parent['name']?></a><br>
<? endif; ?>

<? if(!empty($properties)): ?>
<?=_view('api/properties')->set('properties', $properties);?>
<? endif; ?>

<b>Methods:</b><br>
<? foreach($methods AS $method): ?>

    <?=_view('api/method', $method);?>

<? endforeach; ?>

<?// $x = new \ReflectionClass('\ReflectionParameter'); ?>
<?// var_dump($x->getMethods()); ?>


