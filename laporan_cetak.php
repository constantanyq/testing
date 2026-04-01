<?php
    include("urusetia_menu.php");
    include('sambungan.php');

    if (isset($_POST["submit"])) {
        $namajadual = $_POST['namatable'];
        $namafail = $_FILES['namafail']['name'];

        $sementara = $_FILES['namafail']['tmp_name'];
        move_uploaded_file($sementara, $namafail);

        $fail = fopen($namafail, "r");
        while (!feof($fail)) {
            $medan = explode(",", fgets($fail));
            
            $berjaya = false;
            
            /*menentukan data yang perlu dimasukkan mengikut urutan*/
            if (strtolower($namajadual) === "peserta") {
                /*pengguna memilih kategori peserta*/
                $idpeserta = $medan[0];
                $password = $medan[1];
                $namapeserta = $medan[2];
                $emel = $medan[3];
                $sekolah = $medan[4];
                $idhakim = $medan[5];
                $idurusetia = $medan[6];
                $idguru = trim($medan[7]);

                $sql = "insert into peserta values('$idpeserta', '$password', '$namapeserta', '$emel', '$sekolah', '$idhakim', '$idurusetia', '$idguru')";

                if (mysqli_query($sambungan, $sql))
                    $berjaya = true;
                else
                    echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
            } 
        if (strtolower($namajadual) === "hakim") {
         /*pengguna memilih kategori hakim*/
            $idhakim = $medan[0];
            $namahakim = $medan[1];
            $password = $medan[2];
            $kategorihakim = trim($medan[3]);
            $sql = "insert into hakim values('$idhakim', '$namahakim','$password', '$kategorihakim')"; 
            
            if (mysqli_query($sambungan, $sql))
                $berjaya = true;
            else
                echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
        }
        if (strtolower($namajadual) === "guru") {
         //pengguna memilih kategori guru
            $idguru = $medan[0];
            $namaguru = $medan[1];
            $password = $medan[2];
            $kategoriguru = $medan[3];
            $sekolah = trim($medan[4]);
            
            $sql = "insert into guru values('$idguru', '$namaguru','$password', '$kategoriguru', '$sekolah')"; 
            
            if (mysqli_query($sambungan, $sql))
                $berjaya = true;
            else
                echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
             
        }
}
        //mesej loclhost
        if ($berjaya == true)
            echo "<script>alert('Rekod berjaya diimport');</script>";
        else
            echo "<script>alert('Rekod tidak berjaya diimport');</script>";
        mysqli_close($sambungan);
    }
?>

<html>
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
    </style>
</head>
    <body>
        <h3 class="sederhana">IMPORT DATA</h3>
        <form class="sederhana" action="import.php" method="post"
                enctype = "multipart/form-data" class="import">

            <table>
                <tr>
                    <td>Jadual</td>
                    <td>
                        <!Scroll down menu bagi memilih jenis jadual yang akan diimport>
                        <select name="namatable">
                            <option>Peserta</option>
                            <option>Hakim</option>
                            <option>Guru</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>Nama fail</td>
                    <td><input type="file" name="namafail" accept=".txt"></td>
                </tr>
            </table>
            <!butang-butang>
            <button class="import" type="submit" name="submit">Import</button>
        </form>
    </body>
</html>
