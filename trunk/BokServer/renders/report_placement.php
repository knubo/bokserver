<div class="pageheading">Antall b&oslash;ker per plassering</div>
<table class="report">
<tr><th>Plassering</th><th>Antall b&oslash;ker</th></tr>
<?php
$row = 0;
foreach($result as $one) {
	$class = (($row % 6) < 3) ? "line1" : "line2";
	$row++; 	
?>
<tr class="<?=$class?>">
  <td><?=$one["placement"]?></td>
  <td><?=$one["bookcount"]?></td>
</tr>
<?php
}
?>
</table>