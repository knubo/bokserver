<div class="pageheading">Top 30 forfattere</div>
<table class="report">
<tr><th>Fornavn</th><th>Etternavn</th><th>Antall b&oslash;ker</th></tr>
<?php
$row = 0;
foreach($result as $one) {
	$class = (($row % 6) < 3) ? "line1" : "line2";
	$row++; 	
?>
<tr class="<?=$class?>">
  <td><?=$one["firstname"]?></td>
  <td><?=$one["lastname"]?></td>
  <td><?=$one["bookcount"]?></td>
</tr>
<?php
}
?>
</table>