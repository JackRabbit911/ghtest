<? if(!empty($comment) || !empty($tags)): ?>
<pre>
<b>comment</b>: 
<?=$comment?>

<? foreach($tags AS $tag=>$array): ?>
<br><b><?=$tag?>:</b> <?= implode(', ', $array) ?>
<? endforeach; ?>
</pre>
<? endif; ?>