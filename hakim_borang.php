<?php
	include("urusetia_menu.php");
    include("sambungan.php");

	if (isset($_POST["submit"])) {
		$idguru = $_POST["idguru"];
		$password = $_POST["password"];
		$namaguru = $_POST["namaguru"];
        $kategoriguru = $_POST["kategoriguru"];
        $sekolah = $_POST["sekolah"];

		$sql = "update guru set password='$password', namaguru = '$namaguru', kategoriguru = '$kategoriguru', sekolah = '$sekolah' where idguru = '$idguru' ";

		$result = mysqli_query($sambungan, $sql);
		if ($result == true)
			echo "<br><center>Berjaya kemaskini</center>";
		else
			echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
	}

	if (isset($_GET['idguru']))
		$idguru = $_GET['idguru'];

	$sql = "select * from guru where idguru = '$idguru' ";
	$result = mysqli_query($sambungan, $sql);
	while($guru = mysqli_fetch_array($result)) {
		$password = $guru['password'];
		$namaguru = $guru['namaguru'];
        $kategoriguru = $guru['kategoriguru'];
        $sekolah = $guru['sekolah'];
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

<h3 class="sederhana">KEMASKINI GURU</h3>
<form class="sederhana" action="guru_update.php" method="post">
	<table>
<tr>
       <tr>
	       <td>ID Guru</td>
	       <td><input type="text" name="idguru" value= "<?php echo $idguru; ?>"
               placeholder="max: 3 char"></td>
	   </tr>
	   <tr>
	       <td>Password</td>
	       <td><input type="password" name="password" value= "<?php echo $password; ?>"
               placeholder="max: 8 char"></td>
	   </tr>

		<tr>
			<td>Nama Guru</td>
			<td><input type="text" name="namaguru" value= "<?php echo $namaguru; ?>"></td>
		</tr>
        <tr>
			<td>Kategori Guru</td>
             <td><input type="text" name="kategoriguru" value= "<?php echo $kategoriguru; ?>"></td>
		</tr>
        <tr>
			<td>Sekolah</td>
             <td><input type="text" name="sekolah" value= "<?php echo $sekolah; ?>"></td>
		</tr>
	</table>
	<button class="update" type="submit" name="submit">Update</button>
</form>
