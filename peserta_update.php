<?php
	include("urusetia_menu.php");
    include("sambungan.php");
?>

<link rel="stylesheet" href="senarai.css">
<link rel="stylesheet" href="menu.css">
<link rel="stylesheet" href="borang.css">

<table>
<caption>SENARAI NAMA PESERTA</caption>
<tr>
<th>ID Peserta</th>
<th>Nama Peserta</th>
<th>Password</th>
<th>E-Mel</th>
<th>Sekolah</th>
<th>ID Hakim</th>
<th>ID Guru</th>
<th colspan="2">Tindakan</th>
</tr>

<?php
$sql = "select * from peserta";
$result = mysqli_query($sambungan, $sql);
while($peserta = mysqli_fetch_array($result)) {
echo "<tr> 
<td>$peserta[idpeserta]</td>
<td class='nama'>$peserta[namapeserta]</td>
<td>$peserta[password]</td>
<td>$peserta[emel]</td>
<td>$peserta[sekolah]</td>
<td>$peserta[idhakim]</td>
<td>$peserta[idguru]</td>
<td><a href='peserta_update.php?idpeserta=$peserta[idpeserta]'>update</a></td>
<td><a href='peserta_delete.php?idpeserta=$peserta[idpeserta]'>delete</a></td>
</tr>";
}
?>
</table>
