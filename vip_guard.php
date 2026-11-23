<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION["is_vip"]) || $_SESSION["is_vip"] == 0) {
    // VIP değilse buraya yönlendir
    die("<div style='background:#111; color:#FFD700; padding:50px; text-align:center; font-family:sans-serif; min-height:100vh; display:flex; flex-direction:column; justify-content:center;'>
            <h1>🚫 Erişim Engellendi</h1>
            <p>Bu sayfayı görüntülemek için <b>VIP</b> üye olmanız gerekmektedir.</p>
            <a href='dashboard.php' style='color:#000; background:#FFD700; padding:10px 20px; text-decoration:none; border-radius:5px; margin-top:20px; display:inline-block;'>Panele Dön</a>
         </div>");
}
?>
