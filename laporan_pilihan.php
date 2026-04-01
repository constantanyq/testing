<html>
    <link rel="stylesheet" href="senarai.css">
    <link rel="stylesheet" href="button.css">
    <body>
        <table>
            <?php
                include("urusetia_menu.php");
                include("sambungan.php");

                $pilihan = $_POST["pilihan"];
                $kepala = " <tr>
                           <th>Bil</th>
                           <th>Nama</th>";

                $sql = "select * from penilaian order by idpenilaian asc";
                $data = mysqli_query($sambungan, $sql);
                while ($penilaian = mysqli_fetch_array($data)) {
                    $kepala = $kepala."<th>".$penilaian['aspek']."</th>";
                }
                $kepala = $kepala."<th>Jumlah</th></tr>";
//pemilihan laporan jenis keseluruhan atau mengikut hakim, jika mengikut hakim, fetch data daripada jadual hakim untuk memaparkan markah-markah yang telah diinputkan
                switch ($pilihan) {
                    case 1 : $syarat = " ";
                        $tajuk = " <caption>SENARAI MARKAH KESELURUHAN</caption> ";
                        break;
                    case 2 : $idhakim = $_POST["idhakim"];
                        $syarat = "where hakim.idhakim = '$idhakim' "; 
                        $tajuk = " <caption>SENARAI PESERTA MENGIKUT HAKIM</caption> ";
                        break;
                }
                echo $kepala;
                $bil = 1;
                $sql = "select * from keputusan join peserta on keputusan.idpeserta = peserta.idpeserta 
                        join penilaian on keputusan.idpenilaian = penilaian.idpenilaian
                        join hakim on peserta.idhakim = hakim.idhakim $syarat
                        order by keputusan.jumlah desc, penilaian.idpenilaian asc";

                $data = mysqli_query($sambungan, $sql);
                $a = 1;
                $bil = 1;

                while ($keputusan = mysqli_fetch_array($data)) {
                    if ($a == 1)
                        echo "<tr>
                            <td>".$bil."</td>
                            <td class='nama'>".$keputusan['namapeserta']."</td>";
                    if ($a < 5)
                        echo "<td>".$keputusan['markah']."</td>";

                    $a = $a + 1;
                    if ($a == 5) {
                        echo "<td>".$keputusan['jumlah']."</td>    </tr>";
                        $a = 1;
                        $bil = $bil + 1;
                    } 
                } // tamat while
            ?>
        <caption><?php echo $tajuk; ?> </caption>
        </table>
        <center><button class="cetak" onclick="window.print()">Cetak</button></center>
</body>
</html>
