<table class="searchresult" id="personsearch">
<tr><th>Fornavn</th><th>Etternavn</th><th>Forfatter</th><th>Oversetter</th><th>Redakt&oslash;r</th><th>Illustrat&oslash;r</th><th>Oppleser</th></tr>
<?php
$row = 0;
foreach($result as $one) {
	$class = (($row % 6) < 3) ? "line1" : "line2";
	$row++; 	
?>
<tr class="<?=$class?>" id="row<?=$one["id"]?>">
  <td><?=$one["firstname"]?></td>
  <td><?=$one["lastname"]?></td>
  <td><?=$one["author"] ? 'f': ' '?></td>
  <td><?=$one["translator"]? 'o': ' '?></td>
  <td><?=$one["editor"]? 'r': ' '?></td>
  <td><?=$one["illustrator"]? 'i': ' '?></td>
  <td><?=$one["reader"]? 'l': ' '?></td>
</tr>

<?php
}
?>
</table>