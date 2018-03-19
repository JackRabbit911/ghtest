<aside class="sidebar">
    <div id="qq"></div>   
<div class="toc">
	<div class="constants">
		<h3><?php echo 'Constants'; ?></h3>
		<ul>
		<?php if ($doc->constants): ?>
		<?php foreach ($doc->constants as $name => $value): ?>
			<li><a href="#constant:<?php echo $name ?>"><?php echo $name ?></a></li>
		<?php endforeach ?>
		<?php else: ?>
			<li><em><?php echo 'None'; ?></em></li>
		<?php endif ?>
		</ul>
	</div>
	<div class="properties">
		<h3><?php echo 'Properties'; ?></h3>
		<ul>
		<?php if ($properties = $doc->properties()): ?>
		<?php foreach ($properties as $prop): ?>
			<li><a href="#property:<?php echo $prop->property->name ?>">$<?php echo $prop->property->name ?></a></li>
		<?php endforeach ?>
		<?php else: ?>
			<li><em><?php echo 'None'; ?></em></li>
		<?php endif ?>
		</ul>
	</div>
	<div class="methods">
		<h3><?php echo 'Methods'; ?></h3>
		<ul>
		<?php if ($methods = $doc->methods()): ?>
		<?php foreach ($methods as $method): ?>
			<li><a href="#<?php echo $method->method->name ?>"><?php echo $method->method->name ?>()</a></li>
		<?php endforeach ?>
		<?php else: ?>
			<li><em><?php echo 'None'; ?></em></li>
		<?php endif ?>
		</ul>
	</div>
</div>
    
</aside>
