<?php
    include("urusetia_menu.php");
    include("sambungan.php");

    if (isset($_POST["submit"])) {
        $idpenilaian = $_POST["idpenilaian"];
        $aspek = $_POST["aspek"];
        $markahpenuh = $_POST["markahpenuh"];
        $sql = "update penilaian set aspek = '$aspek', markahpenuh = '$markahpenuh' where idpenilaian = '$idpenilaian' "; 
        $result = mysqli_query($sambungan, $sql);
        if ($result == true)
            echo "<br><center>Berjaya kemaskini</center>";
        else
            echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
    }
    if (isset($_GET['idpenilaian']))
        $idpenilaian = $_GET['idpenilaian'];
    $sql = "select * from penilaian where idpenilaian = '$idpenilaian' ";
    $result = mysqli_query($sambungan, $sql);
    while($nilai = mysqli_fetch_array($result)) {
        $aspek = $nilai['aspek'];
        $markahpenuh = $nilai['markahpenuh'];
    }
?>
<link rel="stylesheet" href="borang.css">
<link rel="stylesheet" href="button.css">
<h3 class="sederhana">KEMASKINI PENILAIAN</h3>
<form class="sederhana" action="penilaian_update.php" method="post">
<table>
    <tr>
        <td>ID Penilaian</td>
        <td><input type="text" name="idpenilaian" value= "<?php echo $idpenilaian; ?>"></td>
    </tr>
    <tr>
        <td>Aspek Penilaian</td>
        <td><input type="text" name="aspek" value= "<?php echo $aspek; ?>"></td>
    </tr>
    <tr>
        <td>Markah Penuh</td>
        <td><input type="text" name="markahpenuh" value= "<?php echo $markahpenuh; ?>"></td>
    </tr>
    </table>
<button class="update" type="submit" name="submit">Update</button>
</form>
