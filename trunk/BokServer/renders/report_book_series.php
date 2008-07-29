<div class="pageheading">Antall b&oslash;ker per kategori</div>
<table class="report">
<tr><th>Kategori</th><th>Antall</th></tr>
<?php
$row = 0;
foreach($result as $one) {
	$class = (($row % 6) < 3) ? "line1" : "line2";
	$row++; 	
?>
<tr class="<?=$class?>">
  <td><?=$one["name"]?></td>
  <td><?=$one["bookcount"]?></td>
</tr>
<?php
}
?>
</table>