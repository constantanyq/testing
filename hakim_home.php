<?php
	include("urusetia_menu.php");
    include("sambungan.php");

	if (isset($_POST["submit"])) {
		$idhakim = $_POST["idhakim"];

		$sql = "delete from hakim where idhakim = '$idhakim' ";
		$result=mysqli_query($sambungan, $sql);
		if ($result == true) {
			$bilrekod = mysqli_affected_rows($sambungan);
			if ($bilrekod > 0)
				echo "<br><center>berjaya padam</center>";
			else
				echo "Tidak berjaya padam. Rekod tidak ada dalam jadual";
			}
			else
				echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
	}
	
	if (isset($_GET['idhakim'])) 
		$idhakim = $_GET['idhakim'];
?>


<link rel="stylesheet" href="borang.css">
<link rel="stylesheet" href="button.css">
<link rel="stylesheet" href="menu.css">

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

<h3 class="sederhana">PADAM HAKIM</h3>
<form class="sederhana" action="hakim_delete.php" method="post">
	<table>
		<tr>
			<td>ID Hakim</td>
			<td><input type="text" name="idhakim" value = "<?php echo $idhakim; ?>" ></td>
		</tr>
	</table>
	<button class="padam" type="submit" name="submit">Padam</button>
</form>
