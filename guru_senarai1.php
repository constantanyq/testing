<?php
	include("urusetia_menu.php");
    include("sambungan.php");
?>

<link rel="stylesheet" href="senarai.css">
<link rel="stylesheet" href="borang.css">
<head>
    <style>
        body {
            background-image: url(imej/wood8.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }
    </style>
</head>

<table>
	<caption>SENARAI GURU</caption>
	<tr>
		<th>ID</th>
		<th>Nama</th>		
		<th>Password</th>
        <th>Sekolah</th>
        <th>Kategori</th>
		<th colspan="2">Tindakan</th>
	</tr>

<?php
	$sql = "select * from guru";
	$result = mysqli_query($sambungan, $sql);
	while($guru = mysqli_fetch_array($result)) {
		echo "<tr>
			<td>$guru[idguru]</td>
			<td>$guru[namaguru]</td>
			<td>$guru[password]</td>
            <td>$guru[sekolah]</td>
            <td>$guru[kategoriguru]</td>
			<td><a href='guru_update.php?idguru=$guru[idguru]'>update</a></td>
			<td><a href='guru_delete.php?idguru=$guru[idguru]'>delete</a></td>
		</tr>";
}
?>
</table>