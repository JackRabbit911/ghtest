<h2>module <?=$name?></h2>

<b>Namespace:</b> <?=$namespace?>
<br>
<?=$desc?>
<br>

<?// var_dump($classes);?>

<? if(is_array($classes) && !empty($classes)): ?>
<? foreach($classes AS $class): ?>
<a href="/docs/api/<?=$url?>/<?=$class['link']?>"><?=$class['class']?></a><br>
<? endforeach; ?>
<? endif; ?>
<?// print_r(get_declared_classes()); ?>

