<?php
	include("guru_menu.php");
    include('sambungan.php');
?>

<link rel="stylesheet" href="senarai.css">
<link rel="stylesheet" href="menu.css">
<link rel="stylesheet" href="borang.css">

<body>
    <style>
    ul li a {
	font-family: verdana;
	font-size: 14px;
	font-weight: normal;
	text-align: center;
        color: black;

	width: 120px;
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

<table>
<caption>SENARAI NAMA PESERTA</caption>
<tr>
<th>ID Peserta</th>
<th>Nama Peserta</th>
<th>Password</th>
<th>E-Mel</th>
<th>Sekolah</th>
<th>ID Hakim</th>
<th>ID Guru</th>
</tr>

<?php
$sql = "select * from peserta";
$result = mysqli_query($sambungan, $sql);
while($peserta = mysqli_fetch_array($result)) {
echo "<tr> 
<td>$peserta[idpeserta]</td>
<td class='nama'>$peserta[namapeserta]</td>
<td>$peserta[password]</td>
<td>$peserta[emel]</td>
<td>$peserta[sekolah]</td>
<td>$peserta[idhakim]</td>
<td>$peserta[idguru]</td>
</tr>";
}
?>
</table>

