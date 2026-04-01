<?php
   include('sambungan.php');
/*Menentukan elemen-elemen yang diperlukan dan memasukkan data yang berkenaan dalam sistem*/
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
       /*Amaran daripada komputer*/
        if ($result)
            echo "<script>alert('Berjaya signup')</script>";
        else
            echo "<script>alert('Tidak berjaya signup')</script>";
        echo "<script>window.location='login.php'</script>";
   }
?>

<link rel="stylesheet" href="borang.css">
<link rel="stylesheet" href="button.css">
<head>
    <style>
        /*imej background*/
        body {
            background-image: url(imej/wood8.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }
        /*button tambah dan hiasan bagi tulisan dan borang*/
        button {
             font-family: verdana;
             font-weight: bold;
             font-size: 11px;

             background-color: #CCCCFF; 
             padding: 4px 6px 4px 32px;
             border: ridge;
             border-radius: 7px;
             border-width: thick;
             border-color: aliceblue;
             color: black;
             margin-top: 30px;
             padding: 8px 30px;
             text-align: center;
             text-decoration: underline;
             display: inline-block;
             font-size: 14px;
             
        }
        
        button.tambah{
             background-image: url(imej/add.png);
             background-repeat: no-repeat;
             background-position: 6px ;

        }
    </style>
</head>

<body>
   <center>
      <img src='imej/tajuk1.png'><br>
      <img src='imej/tajuk2.png'>
   </center>

   <h3 class="sederhana">SIGN UP</h3>
   <form class="sederhana" action="signup.php" method="post">
      <table>
          <!elemen bagi borang Sign Up>
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
    <button class="tambah" type="submit" name="submit">Daftar</button>
    <button class="padam" type="button" onclick="window.location='login.php'">Batal</button>
</form>
</body>
