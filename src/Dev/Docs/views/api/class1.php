<h3><?=$doc->module?></h3>

<h1>
    <!--<small><?php echo $doc->class->getModifiers() ?></small>-->
    <?php echo $doc->modifiers, $doc->class->name ?>
    <?php foreach ($doc->parents as $parent): ?>
        <?php $parent->url = str_replace('\\', '_', $parent->name); ?>
        <br/><small>extends <?php echo $doc->getLink($parent);?></small>
    <?php endforeach; ?>
    
</h1>

<?php if ($interfaces = $doc->class->getInterfaces()): ?>
<p class="interfaces">
Implements:
<?php
$split = FALSE;
foreach($interfaces AS $interfaceName=>$interface)
{
    echo $split.$doc->getLink($interface);
    $split = " | ";    
}
?>
</p>
<?php endif; ?>

<?php if ($traits = $doc->class->getTraits()): ?>
<p class="interfaces">
Use:
<?php
$split = FALSE;
foreach($traits AS $trait)
{
    echo $split.$doc->getLink($trait);
    $split = " | ";    
}
?>
</p>
<?php endif; ?>

<?php echo $doc->description() ?>

<?php if ($doc->tags): ?>
<dl class="tags">
<?php foreach ($doc->tags() as $name => $set): ?>
<dt><?php echo $name ?></dt>
<?php foreach ($set as $tag): ?>
<dd><?php echo $tag ?></dd>
<?php endforeach ?>
<?php endforeach ?>
</dl>
<?php endif; ?>

<p class="note">
<?php if ($path = $doc->class->getFilename()): ?>
    Class declared in <tt><?php echo \Core\Exception\Debug::path($path) ?></tt> on line <?php echo $doc->class->getStartLine() ?>.
<?php else: ?>
Class is not declared in a file, it is probably an internal 
    <?php echo Core\Helper\HTML::anchor('http://php.net/manual/class.'.strtolower($doc->class->name).'.php', 'PHP class', array('target'=>'_blank')) ?>.
<?php endif ?>
</p>



<div class="clearfix"></div>

<?php if ($doc->constants): ?>
<div class="constants">
<h1 id="constants"><?php echo 'Constants'; ?></h1>
<dl>
<?php foreach ($doc->constants() as $name => $value): ?>
<dt>
    <a name="constant:<?=$name?>"></a>
    <h4 id="constant:<?php echo $name ?>"><?php echo $name ?></h4>
</dt>
<dd><?php echo $value ?></dd>
<?php endforeach; ?>
</dl>
</div>
<?php endif ?>

<?php if ($properties = $doc->properties()): ?>
<h1 id="properties"><?php echo 'Properties'; ?></h1>
<div class="properties">
<dl>
<?php foreach ($properties as $prop): ?>
<dt>
    <a name="property:<?=$prop->property->name?>"></a>
    <h4 id="property:<?php echo $prop->property->name ?>"><?php echo $prop->modifiers ?> <code class="nohighlight"><?php echo $prop->type ?></code> $<?php echo $prop->property->name ?></h4>
</dt>
<dd><?php echo $prop->description ?></dd>
<dd><?php echo $prop->value ?></dd>
<?php if ($prop->default !== $prop->value): ?>
<dd><small><?php echo __('Default value:') ?></small><br/><?php echo $prop->default ?></dd>
<?php endif ?>
<?php endforeach ?>
</dl>
</div>
<?php endif ?>

<?php if ($methods = $doc->methods()): ?>
<h1 id="methods"><?php echo 'Methods'; ?></h1>
<div class="methods">
<?php foreach ($methods as $method): ?>
    
   
<?php echo _view('api/method1')->set('doc', $method) ?>
<?php endforeach ?>
</div>
<?php endif ?>

<br>

<?//var_dump($doc)?>


