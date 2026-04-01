<?php
	session_start();
	include("peserta_menu.php");
    include("sambungan.php");

	$nama = $_SESSION["nama"];
	$idpeserta = $_SESSION['idpengguna'];

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

<head>
    <style>
        body {
            background-image: url(imej/wood5.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }
        
        ul li a {
	        font-family: verdana;
	        font-size: 14px;
	        font-weight: normal;
	        text-align: center;
            color: black;

	width: 150px;
        padding: 10px;
        border: none;
        margin: 0px;
        background-color: gainsboro;

	display: block;
	text-decoration: none;
}
    </style>
</head>

<table>
	<caption>Nama Peserta : <?php echo $nama ?></caption>
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
			if ($kedudukan != 0)
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
