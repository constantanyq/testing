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
	<caption>SENARAI HAKIM</caption>
	<tr>
		<th>ID</th>
		<th>Nama</th>		
		<th>Password</th>
        <th>Kategori</th>
		<th colspan="2">Tindakan</th>
	</tr>

<?php
	$sql = "select * from hakim";
	$result = mysqli_query($sambungan, $sql);
	while($hakim = mysqli_fetch_array($result)) {
		echo "<tr>
			<td>$hakim[idhakim]</td>
			<td>$hakim[namahakim]</td>
			<td>$hakim[password]</td>
            <td>$hakim[kategorihakim]</td>
			<td><a href='hakim_update.php?idhakim=$hakim[idhakim]'>update</a></td>
			<td><a href='hakim_delete.php?idhakim=$hakim[idhakim]'>delete</a></td>
		</tr>";
}
?>
</table>
