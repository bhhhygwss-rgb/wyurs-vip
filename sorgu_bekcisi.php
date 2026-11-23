<?php
// Sorgu kaydetme fonksiyonu
function sorguKaydet($sorguTuru, $arananDeger) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION["username"])) { return; }
    
    try {
        $db = new PDO("mysql:host=localhost;dbname=wyurs_vip;charset=utf8mb4", "root", "");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->prepare("INSERT INTO sorgu_gecmisi (username, sorgu_turu, aranan_deger) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION["username"], $sorguTuru, $arananDeger]);
    } catch (PDOException $e) {
        // Hata olursa sessizce geç (kullanıcıyı rahatsız etme)
    }
}
?>
<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["username"])) { header("Location: login.php"); exit; }

// Eğer admin değilse ve VIP değilse içeri sokma
if ($_SESSION["username"] !== "admin" && (!isset($_SESSION["is_vip"]) || $_SESSION["is_vip"] != 1)) {
    die("<div style='background:#0a0a0a; color:#ff4444; padding:50px; text-align:center; font-family:sans-serif; min-height:100vh; display:flex; flex-direction:column; justify-content:center; align-items:center;'>
            <h1 style='margin-bottom:15px;'>Erişim Engellendi</h1>
            <p>Bu sorguyu kullanabilmek için <b>VIP</b> üye olmanız gerekmektedir.</p>
            <a href='dashboard.php' style='color:#000; background:#FFD700; padding:10px 20px; text-decoration:none; border-radius:5px; margin-top:20px; font-weight:bold;'>Panele Dön</a>
         </div>");
}
?>

