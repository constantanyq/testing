<?php
	include("urusetia_menu.php");
    include("sambungan.php");

	if (isset($_POST["submit"])) {
		$idguru = $_POST["idguru"];
		$namaguru = $_POST["namaguru"];
		$password = $_POST["password"];
        $kategoriguru = $_POST["kategoriguru"];
        $sekolah = $_POST["sekolah"];

		$sql = "insert into guru values('$idguru', '$namaguru', '$password', '$kategoriguru', '$sekolah')";

		$result = mysqli_query($sambungan, $sql);
		if ($result == true)
			echo "<br><center>Berjaya tambah</center>";
		else
			echo "<br><center>Sila masukkan maklumat yang betul.</center>";
	}
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

	width: 90px;
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

<h3 class="sederhana">TAMBAH GURU</h3>
<form class="sederhana" action="guru_insert.php" method="post">
	<table>
       <tr>
	       <td>ID Guru</td>
	       <td><input type="text" name="idguru" 
               placeholder="max: 3 char"></td>
	   </tr>
		<tr>
			<td>Nama Guru</td>
			<td><input type="text" name="namaguru"></td>
		</tr>
	   <tr>
	       <td>Password</td>
	       <td><input type="password" name="password" 
               placeholder="max: 8 char"></td>
	   </tr>

        <tr>
			<td>Kategori Guru</td>
             <td><input type="text" name="kategoriguru"></td>
		</tr>
        <tr>
			<td>Sekolah</td>
			<td><input type="text" name="sekolah"></td>
		</tr>
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

