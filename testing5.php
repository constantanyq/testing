<?php
	include("guru_menu.php");
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
</table>
<?
    $kedudukan = 0;
	$sql = "select * from keputusan
		join peserta on keputusan.idpeserta = peserta.idpeserta
		join penilaian on keputusan.idpenilaian = penilaian.idpenilaian
		join hakim on peserta.idhakim = hakim.idhakim
		group by peserta.idpeserta order by keputusan.jumlah desc";

	$data = mysqli_query($sambungan, $sql);
	$bil = 0;
	while ($keputusan = mysqli_fetch_array($data)) {
		$bil = $bil + 1;
		if ($keputusan['idpeserta'] == $idpeserta)
			$kedudukan = $bil;
	}// tamat while
?>

<link rel="stylesheet" href="senarai.css">

<table>
	<caption>MARKAH PESERTA</caption>
	<tr>
		<th>Aspek Penilaian</th>
		<th>Markah Penuh</th>
		<th>Markah Diperoleh</th>
	</tr>
	<?php
		$sql = "select * from keputusan
			join penilaian on keputusan.idpenilaian = penilaian.idpenilaian
			where idpeserta = '$idpeserta' ";
		$data = mysqli_query($sambungan, $sql);
		$bilrekod = mysqli_num_rows($data);
		if ($bilrekod > 0) {
			while ($keputusan = mysqli_fetch_array($data)) {
				echo "<tr >
					<td>$keputusan[aspek]</td>
					<td>$keputusan[markahpenuh]</td>
					<td>$keputusan[markah]</td>
					</tr>";
				$jumlah_markah = $keputusan['jumlah'];
			}
			echo "<tr class='markah_jumlah'> <td ></td>
				<td class='markah_jumlah'>Jumlah Markah</td>
				<td>$jumlah_markah</td>
				</tr>";
			if ($kedudukan !=0)
				echo "<tr class='markah_jumlah'> <td ></td>
				<td class='markah_jumlah'>Kedudukan</td>
				<td>$kedudukan/$bil</td>
				</tr></table>";
		}
		else {
			echo "<tr > <td>markah</td> <td>belum</td> <td>dinilai</td>
				</tr></table>";
		}

?>
