<?php
    include("urusetia_menu.php");
	include("sambungan.php");

	if (isset($_POST["submit"])) {
		$idurusetia = $_POST["idurusetia"];
		$password = $_POST["password"];
		$namaurusetia = $_POST["namaurusetia"];

		$sql = "insert into urusetia values('$idurusetia', '$password', '$namaurusetia')";
		$result = mysqli_query($sambungan, $sql);
		if ($result == true)
				echo "berjaya tambah";
		else
				echo "Ralat : $sql<br>".mysqli_error($sambungan);
	}
?>

<link rel="stylesheet" href="borang.css">
<link rel="stylesheet" href="button.css">
<link rel="stylesheet" href="menu.css">

<h3 class="sederhana">TAMBAH URUSETIA</h3>
<form class="sederhana" action="urusetia_insert.php" method="post">
    <table>

	   <tr>
	       <td>ID urusetia</td>
	       <td><input type="text" name="idurusetia" 
               placeholder="max: 3 char"></td>
	   </tr>

	   <tr>
	       <td>Nama Urusetia</td>
	       <td><input type="text" name="namaurusetia"></td>
	   </tr>

	   <tr>
	       <td>Password</td>
	       <td><input type="password" name="password" 
               placeholder="max: 8 char"></td>
	   </tr>

    </table>
    <button class="tambah" type="submit" 
            name="submit">Tambah</button>
</form>

<center>
	<button class="biru" onclick="tukar_warna(0)">Biru</button>
	<button class="hijau" onclick="tukar_warna(1)">Hijau</button>
	<button class="merah" onclick="tukar_warna(2)">Merah</button>
	<button class="hitam" onclick="tukar_warna(3)">Hitam</button>
</center>

<script>
	function tukar_warna(n){
		var warna = ["Blue", "Green", "Red", "Black"];
		var teks = document.getElementsByClassName("warna");
		for(var i=0; i<teks.length; i++)
			teks[i].style.color=warna[n];
	}
</script>

