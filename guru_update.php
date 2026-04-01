<?php
	include("guru_menu.php");
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
        <th>Sekolah</th>
        <th>Kategori</th>
	</tr>

<?php
	$sql = "select * from guru";
	$result = mysqli_query($sambungan, $sql);
	while($guru = mysqli_fetch_array($result)) {
		echo "<tr>
			<td>$guru[idguru]</td>
			<td>$guru[namaguru]</td>
            <td>$guru[sekolah]</td>
            <td>$guru[kategoriguru]</td>
		</tr>";
}
?>
</table>