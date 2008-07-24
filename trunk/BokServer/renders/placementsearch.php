<table class="searchresult">
<tr><th>Plassering</th><th>Info</th></tr>
<?php
$row = 0;
foreach($result as $one) {
	$class = (($row % 6) < 3) ? "line1" : "line2";
	$row++; 	
?>
<tr class="<?=$class?>" id="row<?=$one["id"]?>">
  <td><?=$one["placement"]?></td>
  <td><?=$one["info"]?></td>
</tr>
<?php
}
?>
</table>