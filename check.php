<?php
@require_once("class.php");
#################
// AKUN KLIKBCA	
$userid = "";
$pwd = "";
$date = "30day"; //jarak berapa hari? misal 7 hari = 7day (maks 30day), kalo pengen hari ini ganti jadi today atau 0day
#################
$a = new BCA($userid,$pwd,$date);
print_r(@json_decode($a->mutasiTrx()));
