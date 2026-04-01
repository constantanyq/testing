<?php
      include("guru_menu.php");
      include('sambungan.php');

      if (isset($_POST["submit"])) {
           $idpeserta = $_POST["idpeserta"];
           $password = $_POST["password"];
           $namapeserta = $_POST["namapeserta"];
           $emel = $_POST["emel"];
           $sekolah = $_POST["sekolah"];
           $idhakim = $_POST["idhakim"];
           $idurusetia = $_POST["idurusetia"];
           $idguru = $_POST["idguru"];
          
           $sql = "insert into peserta values('$idpeserta', '$password', '$namapeserta', '$emel', '$sekolah', '$idhakim', '$idurusetia', '$idguru')";
          
           $result = mysqli_query($sambungan, $sql);
           if ($result == true)
                 echo "<br><center>Berjaya tambah</center>";
           else
                 echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
      }  
?>

<link rel="stylesheet" href="borang.css">
<link rel="stylesheet" href="button.css"> 
<link rel="stylesheet" href="menu.css">

<h3 class="sederhana">TAMBAH PESERTA</h3>
<form class="sederhana" action="gpeserta_insert.php" method="post">

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

     <table>
       <tr>
              <td class="warna">ID Peserta</td>
	          <td><input type = "text" name = "idpeserta" 
               placeholder="max: 3 char"></td>
	   </tr>
       <tr>
	           <td class="warna">Password</td>
	           <td><input type="password" name="password" 
               placeholder="max: 8 char"></td>
	   </tr>
          <tr>
               <td class="warna">Nama Peserta</td>
               <td><input type="text" name="namapeserta"></td>
          </tr>
          <tr>
               <td class="warna">E-Mel</td>
               <td><input type="text" name="emel"></td>
          </tr>
          <tr>
               <td class="warna">Sekolah</td>
               <td><input type="text" name="sekolah"></td>
          </tr>

          <tr>
               <td class="warna">Nama Hakim</td>
               <td> <select name="idhakim">
                    <?php
                          $sql = "select * from hakim";
                          $data = mysqli_query($sambungan, $sql);
                          while ($hakim = mysqli_fetch_array($data)) {
                                echo "<option value='$hakim[idhakim]'>$hakim[namahakim]>$hakim[kategorihakim]</option>";
                          }
                    ?>
                    </select>
               </td>
          </tr>
	<tr>
		<td class="warna">Nama Urusetia</td>
		<td> <select name="idurusetia">
			<?php
				$sql = "select * from urusetia";
				$data = mysqli_query($sambungan, $sql);
				while ($urusetia = mysqli_fetch_array($data)) {
				echo "<option value='$urusetia[idurusetia]'>$urusetia[namaurusetia]</option>";
				}
			?>
			</select>
		</td>
	</tr>
         	<tr>
		<td class="warna">Nama Guru</td>
		<td> <select name="idguru">
			<?php
				$sql = "select * from guru" ;
				$data = mysqli_query($sambungan, $sql);
				while ($guru = mysqli_fetch_array($data)) {
				echo "<option value='$guru[idguru]'>$guru[namaguru]>$guru[kategoriguru]</option>";
				}
			?>
			</select>
		</td>
	</tr>
</table>
	<button class="tambah" type="submit" name="submit">Tambah</button>
</form>
<br>

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