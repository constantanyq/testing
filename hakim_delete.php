<link rel="stylesheet" href="menu.css">

<head>
<body>
    <style>
    ul li a {
	font-family: verdana;
	font-size: 14px;
	font-weight: normal;
	text-align: center;
        color: black;

	width: 120px;
    padding: 9px;
    border: 1px dotted darkblue;
    margin: 0px;
        background-color: gainsboro;
    padding-bottom: 10px;

	display: block;
	text-decoration:none;
              }
    </style>
</body>
</head>

<?php
	include("hakim_menu.php");
    include("sambungan.php");

	$idpeserta = $_POST["idpeserta"];
	$jumlah_markah = $_POST["jumlah_markah"];
	
	$sql = "select * from penilaian";
	$data = mysqli_query($sambungan, $sql);

	while ($penilaian = mysqli_fetch_array($data)) {
		$markah = $_POST["$penilaian[idpenilaian]"];
		$idpenilaian = $penilaian['idpenilaian'];
		$sql = "insert into keputusan values('$idpeserta', '$idpenilaian', '$markah', '$jumlah_markah')";
		$result = mysqli_query($sambungan, $sql);


	if ($result == true)
		echo "<br><center>Berjaya tambah</center>";
	else 
		echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
	} 
?>
