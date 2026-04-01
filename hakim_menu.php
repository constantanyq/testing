<?php
	include("urusetia_menu.php");
    include("sambungan.php");

	if (isset($_POST["submit"])) {
		$idhakim = $_POST["idhakim"];
		$password = $_POST["password"];
		$namahakim = $_POST["namahakim"];
        $kategorihakim = $_POST["kategorihakim"];

		$sql = "insert into hakim values('$idhakim', '$password', '$namahakim', '$kategorihakim')";

		$result = mysqli_query($sambungan, $sql);
		if ($result == true)
			echo "<br><center>Berjaya tambah</center>";
		else
			echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
	}
?>

<link rel="stylesheet" href="borang.css">
<link rel="stylesheet" href="button.css">

<h3 class="sederhana">TAMBAH HAKIM</h3>
<form class="sederhana" action="hakim_insert.php" method="post">
	<table>
       <tr>
	       <td>ID Hakim</td>
	       <td><input type="text" name="idhakim" 
               placeholder="max: 3 char"></td>
	   </tr>
		<tr>
			<td>Nama Hakim</td>
			<td><input type="text" name="namahakim"></td>
		</tr>
        <tr>
	       <td>Password</td>
	       <td><input type="password" name="password" 
               placeholder="max: 8 char"></td>
	   </tr>
        <tr>
			<td>Kategori Hakim</td>
             <td><input type="text" name="kategorihakim">
	</table>
        <button class="tambah" type="submit" name="submit">Tambah</button>
</form>

<head>
    <style>
        body {
            background-image: url(imej/wood8.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }
        
        button.tambah{
             background-image: url(imej/add.png);
             background-repeat: no-repeat;
             background-position: 6px ;

        }
    </style>
</head>

    
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

