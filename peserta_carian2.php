<?php
	include("urusetia_menu.php");
	include ('sambungan.php');
?>

<link rel="stylesheet" href="borang.css">
<link rel="stylesheet" href="button.css">

<h3 class="sederhana">CARIAN</h3>
<form class="sederhana" action="peserta_carian2.php" method="post">
<table>
	<tr>
		<td>Nama Peserta</td>
		<td>
			<select name="idpeserta">
				<?php
					$sql = "select * from peserta";
					$data = mysqli_query($sambungan, $sql);
					while ($peserta = mysqli_fetch_array($data)) {
						echo "<option value='$peserta[idpeserta]'>$peserta[idpeserta]:
					$peserta[namapeserta]</option>";
				} 
			?>
		</select>
	</td>
	</tr>
</table>
<button class="cari" type="submit">Search</button>
</form>
