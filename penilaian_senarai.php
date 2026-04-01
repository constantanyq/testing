<?php
    include("urusetia_menu.php");
    include("sambungan.php");
    if (isset($_POST["submit"])) {
        $idpenilaian = $_POST["idpenilaian"];
        $aspek = $_POST["aspek"];
        $markahpenuh = $_POST["markahpenuh"];
        $sql = "insert into penilaian values('$idpenilaian', '$aspek', '$markahpenuh')";
        $result = mysqli_query($sambungan, $sql);
        if ($result == true)
            echo "<br><center>Berjaya tambah</center>";
        else
            echo "<br><center>Ralat : $sql<br>".mysqli_error($sambungan)."</center>";
    }
?>
<link rel="stylesheet" href="borang.css">
<link rel="stylesheet" href="button.css">
<h3 class="sederhana">TAMBAH PENILAIAN</h3>
<form class="sederhana" action="penilaian_insert.php" method="post">
    <table>
        <tr>
            <td>ID Penilaian</td>
            <td><input type="text" name="idpenilaian"></td>
        </tr>
        <tr>
            <td>Aspek Penilaian</td>
            <td><input type="text" name="aspek"></td>
        </tr>
        <tr>
            <td>Markah Penuh</td>
            <td><input type="text" name="markahpenuh"></td>
        </tr>
    </table>
<button class="tambah" type="submit" name="submit">Tambah</button>
</form>


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

