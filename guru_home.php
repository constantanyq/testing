<?php
	include("urusetia_menu.php");
    include("sambungan.php");

	if (isset($_POST["submit"])) {
		$idguru = $_POST["idguru"];

		$sql = "delete from guru where idguru = '$idguru' ";
		$result=mysqli_query($sambungan, $sql);
		if ($result == true) {
			$bilrekod = mysqli_affected_rows($sambungan);
			if ($bilrekod > 0)
				echo "<br><center>Berjaya padam</center>";
			else
				echo "Tidak berjaya padam. Rekod tidak ada dalam jadual";
			}
			else
				echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
	}
	
	if (isset($_GET['idguru'])) 
		$idguru = $_GET['idguru'];
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

	width: 95px;
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

<h3 class="sederhana">PADAM GURU</h3>
<form class="sederhana" action="guru_delete.php" method="post">
	<table>
		<tr>
			<td>ID Guru</td>
			<td><input type="text" name="idguru" value = "<?php echo $idguru; ?>" ></td>
		</tr>
	</table>
	<button class="padam" type="submit" name="submit">Padam</button>
</form>
