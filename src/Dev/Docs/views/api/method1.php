<a name="<?=$doc->method->name?>"></a>
<div class="method">

<?php $declares = $doc->method->getDeclaringClass(); ?>
<h3 id="<?php echo $doc->method->name ?>">
	<?php echo $doc->modifiers, $doc->method->name ?>( <?php echo $doc->params ? $doc->params_short() : '' ?>)
	<small>(defined in <?php echo $declares->name?>)</small>
</h3>

<div class="description">
<?php echo $doc->description ?>
</div>

<?php if ($doc->params): ?>
<h4>Parameters</h4>
<ul>
<?php foreach ($doc->params as $param): ?>
<li>
<code><?php echo ($param->reference?'byref ':'').($param->type?$param->type:'unknown') ?></code>
<strong><?php echo '$'.$param->name ?></strong>
<?php echo $param->default?'<small> = '.$param->default.'</small>':'<small>required</small>'  ?>
<?php echo $param->description?' - '.$param->description:'' ?>
</li>
<?php endforeach; ?>
</ul>
<?php endif ?>

<?php if ($doc->tags) echo \Core\View::factory('api/tags')->set('tags', $doc->tags) ?>

<?php if ($doc->return): ?>
<h4><?php echo 'Return Values'; ?></h4>
<ul class="return">
<?php foreach ($doc->return as $set): list($type, $text) = $set; ?>
    <li><code><?php echo htmlspecialchars($type) ?></code><?php if ($text) echo ' - '.htmlspecialchars(ucfirst($text)) ?></li>
<?php endforeach ?>
</ul>
<?php endif ?>

<?php if ($doc->source): ?>
<div class="method-source">
<h4><?php echo 'Source Code'; ?></h4>
<pre><code class="PHP"><?php echo $doc->source ?></code></pre>
</div>
<?php endif ?>

</div>
