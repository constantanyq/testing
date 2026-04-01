<?php
	include("urusetia_menu.php");
    include("sambungan.php");


	if (isset($_POST["submit"])) {
		$idurusetia = $_POST["idurusetia"];
		$sql = "delete from urusetia where idurusetia = '$idurusetia' ";
		$result = mysqli_query($sambungan, $sql);

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

	if (isset($_GET['idurusetia']))
		$idurusetia = $_GET['idurusetia'];
?>

<link rel="stylesheet" href="borang.css">
<link rel="stylesheet" href="button.css">
<link rel="stylesheet" href="menu.css">

<h3 class="sederhana">PADAM URUSETIA</h3>
<form class="sederhana" action="urusetia_delete.php" method="post">

<table>
	<tr>
		<td>
        ID Urusetia
        </td>
        <td><input type="text" name="idurusetia" value = "<?php echo $idurusetia; ?>" ></td>
	</tr>
</table>

<button class="padam" type="submit" name="submit">Padam</button>
</form>
