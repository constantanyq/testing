<?php
    include("urusetia_menu.php");
    include('sambungan.php');
?>

<html>
    <link rel="stylesheet" href="borang.css">
    <link rel="stylesheet" href="button.css">

    <head>
        <style>
            /*imej bagi background laman*/
            body {
               background-image: url(imej/wood8.png);
               background-repeat: no-repeat;
               background-size: cover;
               background-attachment: fixed;
                 }
        </style>
    </head>
    
<!sistem bagi pemilihan laporan jenis keseluruhan atau mengikut hakim>
    <body>
        <!tajuk jadual>
        <h3 class = "pendek">PILIHAN JENIS LAPORAN</h3>
        <form class = "pendek" action="laporan_cetak.php" method="post">
            <select id='pilihan' name='pilihan' onchange='papar_pilihan()'>
                <!Scroll down menu>
            <option value=1>Senarai Keseluruhan</option>
            <option value=2>Senarai Mengikut Hakim</option>
                </select> <br>
                <div id="hakim" style="display:none">
                <select name="idhakim">
                <?php
                    include('sambungan.php');
                    $sql = "select * from hakim";
                    $data = mysqli_query($sambungan, $sql);
                    while ($hakim = mysqli_fetch_array($data)) {
                        echo "<option value='$hakim[idhakim]'>$hakim[namahakim]>$hakim[kategorihakim]</option>";
                    }
                ?>
                </select>
                </div>
            <!butang-buatng>
            <button class="papar" type="submit">Papar</button>
        </form>
        
<!pemilihan jenis laporan sama ada mengikut hakim atau tidak>
<script>
    function papar_pilihan () {
        var pilih = document.getElementById("pilihan").value;
        if (pilih == 1) {
            document.getElementById('hakim').style.display = 'none';

        }
        else if (pilih == 2) {
            document.getElementById('hakim').style.display = 'block';
        }
    }
</script>
</body>
</html>
