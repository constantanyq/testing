<?php
	include("urusetia_menu.php");
    include("sambungan.php");
	
	$idpeserta = $_POST['idpeserta'];
	
	$sql = "select * from peserta where idpeserta = '$idpeserta'";
	$result = mysqli_query($sambungan, $sql);
	$peserta = mysqli_fetch_array($result);

	$namapeserta = $peserta['namapeserta'];
	$emel = $peserta['emel'];
	$sekolah = $peserta['sekolah'];
	$password = $peserta['password'];
	$idhakim = $peserta['idhakim'];
    $idguru = $peserta['idguru']
?>

<link rel = "stylesheet" href = "senarai.css">

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
	<caption >MAKLUMAT PESERTA</caption>
	<tr>
		<th>Perkara</th>
		<th>Maklumat</th>
	</tr>
	<tr>
		<td class="keputusan">ID Peserta</td>
		<td class="keputusan"><?php echo $idpeserta; ?></td>
	</tr>
	<tr>
		<td class="keputusan">Nama Peserta</td>
		<td class="keputusan"><?php echo $namapeserta; ?></td>
	</tr>
<tr>
		<td class="keputusan">E-Mel</td>
		<td class="keputusan"><?php echo $emel; ?></td>
	</tr>
<tr>
		<td class="keputusan">Sekolah</td>
		<td class="keputusan"><?php echo $sekolah; ?></td>
	</tr>
<tr>
		<td class="keputusan">Password</td>
		<td class="keputusan"><?php echo $password; ?></td>
	</tr>
<tr>
		<td class="keputusan">ID Hakim</td>
		<td class="keputusan"><?php echo $idhakim; ?></td>
	</tr>
<tr>
		<td class="keputusan">ID Guru</td>
		<td class="keputusan"><?php echo $idguru; ?></td>
	</tr>
</table>
