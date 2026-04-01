<?php
	include("guru_menu.php");
    include("sambungan.php");

	if (isset($_POST["submit"])) {
		$idpeserta = $_POST["idpeserta"];

	$sql = "delete from peserta where idpeserta = '$idpeserta'
";
	$result = mysqli_query($sambungan, $sql);
	if ($result == true) {
		$bilrekod = mysqli_affected_rows($sambungan);
		if ($bilrekod > 0)
			echo 'alert("Berjaya Padam")';
		else
			echo "Tidak berjaya padam. Rekod tidak ada dalam jadual";
}


	else
		echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
}
if (isset($_GET['idpeserta']))
		$idpeserta = $_GET['idpeserta'];

?>

<link rel="stylesheet" href="borang.css">
<link rel="stylesheet" href="button.css">
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

<h3 class="sederhana">PADAM PESERTA</h3>
<form class="sederhana" action="gpeserta_delete.php" method="post">
<table>
<tr>
		<td>ID Peserta</td>
		<td><input type="text" name="idpeserta" value = "<?php echo $idpeserta; ?>" ></td>
</tr>
</table>
<button class="padam" type="submit" name="submit">Padam</button>
</form>
