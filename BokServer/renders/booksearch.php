<table class="searchresult">
<tr><th>Boknummer</th><th>Tittel</th><th>ISBN</th><th>Plassering</th></tr>
<?php
$row = 0;
foreach($result as $one) {
	$class = (($row % 6) < 3) ? "line1" : "line2";
	$row++; 	
?>
<tr class="<?=$class?>">
  <td><?=$one["usernumber"]?></td>
  <td><?=$one["title"]?></td>
  <td><?=$one["ISBN"]?></td>
  <td><?=$one["placement"]?></td></tr>

<?php
}
?>
</table>