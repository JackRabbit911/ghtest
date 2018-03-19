<b>properties:</b>
<?//print_r($properties)?>
<table class="table table-condensed">
<thead>
    <tr>
        <th>#</th>
        <th>Modifier</th>
        <th>Name</th>
        <th>Type</th>
        <th>Value</th>
        <th>Comment</th>
        <th>**</th>
    </tr>
</thead>
<tbody>
<? foreach($properties AS $key=>$property): ?>
    
<tr>
    <td><?=$key?></td>
    
    <td><?=$property['modifier']?></td>
    <td><?=$property['name']?></td>
    <td><?=$property['type']?></td>    
    <td><?=$property['value']?></td>
    <td><?=$property['comment']['comment']?></td>
    <td><?= ($property['extends'] == 1) ? 'parent' : 'self' ?></td>
</tr>


<? endforeach; ?>
</tbody>
</table>


