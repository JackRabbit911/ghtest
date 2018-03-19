<h4>Список модулей</h4>
<?=$desc?>

<table class="table table-hover">
<thead>
    <tr>
        <th>#</th>
        <th>Module</th>
        <th>Namespace</th>
        <th>Path</th>
    </tr>
</thead>
<tbody>
<? foreach($modules AS $key=>$module): ?>
    <a href="/docs/api/<?=$module['link']?>">
<tr class="tr-a" onclick="window.location.href='/docs/api/<?=$module['link']?>'; return false">
    <td><?=$key?></td>
    
    <td><?=$module['module']?></td>
    <td><?=$module['namespace']?></td>
    <td><?=$module['path']?></td>
    
    
</tr>
</a>

<? endforeach; ?>
</tbody>
</table>


