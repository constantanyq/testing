<?php
      session_start();
      include("guru_menu.php");
      include("sambungan.php");
?>

<html>
<link rel="stylesheet" href="senarai.css">
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
</head>

<body>
<table>
<caption>SENARAI PESERTA MENGIKUT GURU</caption>

<?php
      $nama = $_SESSION["nama"];
      $idguru = $_SESSION['idpengguna'];

      $kepala = " <tr> <th>Bil</th>
                                <th>Nama</th>";

      $sql = "select * from peserta order by idpeserta asc";

      $data = mysqli_query($sambungan, $sql);
      while ($penilaian = mysqli_fetch_array($data)) {
            $kepala = $kepala."<th>".$penilaian['aspek']."</th>";
      }
      $kepala = $kepala."<th>Jumlah</th></tr>";

      echo $kepala;
      $bil = 1;
      $sql = "select * from guru
      join peserta on guru.idpeserta = peserta.idpeserta join penilaian on keputusan.idpenilaian = penilaian.idpenilaian where guru.idguru = '$idguru'
      order by keputusan.jumlah desc, penilaian.idpenilaian asc   ";

      $data = mysqli_query($sambungan, $sql);
      $a = 1;
      $bil = 1;
      while ($kpeserta = mysqli_fetch_array($data)) {
            if ($a == 1)
                  echo "<tr>
                  <td>".$bil."</td>
                  <td>".$keputusan['namapeserta']."</td>";

            if ($a < 6)
                  echo "<td>".$keputusan['markahl']."</td>";

            $a = $a + 1;
                        if ($a == 6) {
                 echo "<td>".$keputusan['jumlah']."</td>
                             </tr>";
                 $a = 1;
                 $bil = $bil + 1;
            }   
      } // tamat while    
?>
</table>
</body>
</html>