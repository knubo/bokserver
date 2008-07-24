<table class="searchresult">
<tr><th>Kategri</th></tr>
<?php
$row = 0;
foreach($result as $one) {
	$class = (($row % 6) < 3) ? "line1" : "line2";
	$row++; 	
?>
<tr class="<?=$class?>" id="row<?=$one["id"]?>">
  <td><?=$one["name"]?></td>
</tr>
<?php
}
?>
</table>