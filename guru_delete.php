<?php
	include("guru_menu.php");
    include("sambungan.php");

	if (isset($_POST["submit"])) {
		$idpeserta = $_POST["idpeserta"];
		$password = $_POST["password"];
		$namapeserta = $_POST["namapeserta"];
        $emel = $_POST["emel"];
        $sekolah = $_POST["sekolah"];
        $idhakim = $_POST["idhakim"];
        $idurusetia = $_POST["idurusetia"];
        $idguru = $_POST["idguru"];

		$sql = "update peserta set password='$password', namapeserta = '$namapeserta', emel = '$emel', sekolah = '$sekolah', idhakim = '$idhakim', idurusetia = '$idurusetia', idguru = '$idguru' where idpeserta = '$idpeserta' ";

		$result = mysqli_query($sambungan, $sql);
		if ($result == true)
			echo "<br><center>Berjaya kemaskini</center>";
		else
			echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
	}

	if (isset($_GET['idpeserta']))
		$idpeserta = $_GET['idpeserta'];

	$sql = "select * from peserta where idpeserta = '$idpeserta' ";
	$result = mysqli_query($sambungan, $sql);
	while($peserta = mysqli_fetch_array($result)) {
		$password = $peserta['password'];
		$namapeserta = $peserta['namapeserta'];
        $emel = $peserta['emel'];
        $sekolah = $peserta['sekolah'];
        $idhakim = $peserta['idhakim'];
        $idurusetia = $peserta['idurusetia'];
        $idguru = $peserta['idguru'];
	}
?>

<link rel="stylesheet" href="borang.css">
<link rel="stylesheet" href="button.css">

<head>
    <style>
        body {
            background-image: url(imej/wood8.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }
               button.update{
             background-image: url(imej/update.png);
             background-repeat: no-repeat;
             background-position: 6px ;

        }
    </style>
</head>

<h3 class="sederhana">KEMASKINI PESERTA</h3>
<form class="sederhana" action="gpeserta_update.php" method="post">
	<table>
<tr>
       <tr>
	       <td>ID Peserta</td>
	       <td><input type="text" name="idpeserta" value= "<?php echo $idpeserta; ?>"
               placeholder="max: 3 char"></td>
	   </tr>
	   <tr>
	       <td>Password</td>
	       <td><input type="password" name="password" value= "<?php echo $password; ?>"
               placeholder="max: 8 char"></td>
	   </tr>

		<tr>
			<td>Nama Peserta</td>
			<td><input type="text" name="namapeserta" value= "<?php echo $namapeserta; ?>"></td>
		</tr>
        <tr>
			<td>E-mel</td>
             <td><input type="text" name="emel" value= "<?php echo $emel; ?>"></td>
		</tr>
        <tr>
			<td>Sekolah</td>
             <td><input type="text" name="sekolah" value= "<?php echo $sekolah; ?>"></td>
		</tr>
	   <tr>
	       <td>ID Hakim</td>
	       <td><input type="text" name="idhakim" 
               placeholder="max: 3 char" value= "<?php echo $idhakim; ?>" ></td>
	   </tr>
	   <tr>
	       <td>ID Urusetia</td>
	       <td><input type="text" name="idurusetia" 
               placeholder="max: 3 char"value= "<?php echo $idurusetia; ?>" ></td>
	   </tr>
	   <tr>
	       <td>ID Guru</td>
	       <td><input type="text" name="idguru" 
               placeholder="max: 3 char" value= "<?php echo $idguru; ?>" ></td>
	   </tr>
	</table>
	<button class="update" type="submit" name="submit">Update</button>
</form>
