<?php
	include("urusetia_menu.php");
    include("sambungan.php");

	if (isset($_POST["submit"])) {
		$idhakim = $_POST["idhakim"];
		$password = $_POST["password"];
		$namahakim = $_POST["namahakim"];
        $kategorihakim = $_POST["kategorihakim"];

		$sql = "update hakim set password='$password', namahakim = '$namahakim', kategorihakim = '$kategorihakim' where idhakim = '$idhakim' ";

		$result = mysqli_query($sambungan, $sql);
		if ($result == true)
			echo "<br><center>Berjaya kemaskini</center>";
		else
			echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
	}

	if (isset($_GET['idhakim']))
		$idhakim = $_GET['idhakim'];

	$sql = "select * from hakim where idhakim = '$idhakim' ";
	$result = mysqli_query($sambungan, $sql);
	while($hakim = mysqli_fetch_array($result)) {
		$password = $hakim['password'];
		$namahakim = $hakim['namahakim'];
        $kategorihakim = $hakim['kategorihakim'];
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

<h3 class="sederhana">KEMASKINI HAKIM</h3>
<form class="sederhana" action="hakim_update.php" method="post">
	<table>
<tr>
       <tr>
	       <td>ID Hakim</td>
	       <td><input type="text" name="idhakim" value= "<?php echo $idhakim; ?>"
               placeholder="max: 3 char"></td>
	   </tr>
	   <tr>
	       <td>Password</td>
	       <td><input type="password" name="password" value= "<?php echo $password; ?>"
               placeholder="max: 8 char"></td>
	   </tr>

		<tr>
			<td>Nama Hakim</td>
			<td><input type="text" name="namahakim" value= "<?php echo $namahakim; ?>"></td>
		</tr>
        <tr>
			<td>Kategori Hakim</td>
             <td><input type="text" name="kategorihakim" value= "<?php echo $kategorihakim; ?>"></td>
		</tr>
	</table>
	<button class="update" type="submit" name="submit">Update</button>
</form>
