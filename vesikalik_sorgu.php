<?php require_once 'sorgu_bekcisi.php'; ?>
<?php
session_start();
if (!isset($_SESSION["username"])) { header("Location: login.php"); exit; }

$hata = "";
$sonuc = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tc = trim($_POST["tc"] ?? "");
    if ($tc) {
        try {
            $db = new PDO("mysql:host=localhost;dbname=wyurs_vip;charset=utf8mb4", "root", "");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $db->prepare("SELECT * FROM `60k_vesika` WHERE tc = ? LIMIT 1");
            $stmt->execute([$tc]);
            $sonuc = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$sonuc) {
                $hata = "Bu TC numarasına ait kayıt bulunamadı.";
            }
        } catch (PDOException $e) {
            $hata = "Veritabanı hatası: " . $e->getMessage();
        }
    } else {
        $hata = "Lütfen TC Kimlik No giriniz.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<script>
    document.addEventListener('contextmenu', event => event.preventDefault());
    document.onkeydown = function(e) {
        if (e.keyCode == 123) return false; // F12
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) return false;
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) return false;
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) return false;
        if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) return false;
        if (e.ctrlKey && e.keyCode == 'S'.charCodeAt(0)) return false;
    }
</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vesikalık Sorgu - Wyurs VIP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #0a0a0a; color: #eee; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #111; border-right: 2px solid #FFD700; padding: 20px; position: fixed; height: 100%; z-index: 100; }
        .logo { font-size: 24px; color: #FFD700; font-weight: 900; margin-bottom: 30px; text-align: center; }
        .sidebar a { display: block; padding: 12px; color: #FFD700 !important; text-decoration: none; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .sidebar a:hover { background: #FFD700; color: #000 !important; }
        .sidebar i { margin-right: 10px; width: 20px; text-align: center; }
        .main-content { margin-left: 260px; flex: 1; padding: 30px; }
        .header-area h2 { color: #FFD700; margin-bottom: 20px; font-size: 22px; display: flex; align-items: center; gap: 10px; }
        .form-container { background: #16161c; border: 1px solid #2a2a35; border-radius: 12px; padding: 30px; max-width: 800px; margin: 0 auto; border-left: 4px solid #FFD700; }
        .form-container h3 { color: #FFD700; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #aaa; margin-bottom: 8px; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; background: #111; border: 1px solid #333; border-radius: 8px; color: #fff; }
        .form-group input:focus { outline: none; border-color: #FFD700; }
        .btn-sorgula { background: #FFD700; color: #000; border: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-sorgula:hover { background: #fff; }
        .sonuc-kutusu { margin-top: 30px; background: #111; border: 1px solid #333; border-radius: 8px; padding: 20px; }
        .sonuc-kutusu h4 { color: #FFD700; margin-bottom: 15px; border-bottom: 1px solid #333; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 10px; border-bottom: 1px solid #222; }
        td:first-child { color: #888; font-weight: bold; width: 20%; text-transform: uppercase; font-size: 12px; }
        td:last-child { color: #fff; }
        .vesika-img { max-width: 200px; border-radius: 8px; border: 2px solid #FFD700; margin-top: 10px; display: block; }
        .hata { color: #ff4444; background: rgba(255,0,0,0.1); padding: 10px; border-radius: 8px; border: 1px solid #ff4444; margin-bottom: 20px; }
        @media screen and (max-width: 1024px) { .sidebar { display: none; } .main-content { margin-left: 0; padding: 15px; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">Wyurs VIP</div>
        <a href="dashboard.php"><i class="fas fa-home"></i> Ana Sayfa</a>
        <a href="profil.php"><i class="fas fa-user"></i> Profil</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
    </div>
    <div class="main-content">
        <div class="header-area"><h2><i class="fas fa-portrait"></i> Vesikalık Sorgu</h2></div>
        <div class="form-container">
            <h3>Sorgulama Formu</h3>
            <?php if($hata): ?><div class="hata"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($hata) ?></div><?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>TC Kimlik No</label>
                    <input type="text" name="tc" placeholder="TC Kimlik Numaranızı giriniz..." value="<?= htmlspecialchars($_POST['tc'] ?? '') ?>" required>
                </div>
                <button type="submit" class="btn-sorgula"><i class="fas fa-search"></i> Sorgula</button>
            </form>
            <?php if($sonuc): ?>
                <div class="sonuc-kutusu">
                    <h4><i class="fas fa-check-circle"></i> Sorgu Sonucu</h4>
                    <table>
                        <tr><td>TC</td><td><?= htmlspecialchars($sonuc['tc']) ?></td></tr>
                        <tr><td>İsim</td><td><?= htmlspecialchars($sonuc['isim']) ?></td></tr>
                        <tr><td>Soyisim</td><td><?= htmlspecialchars($sonuc['soyisim']) ?></td></tr>
                    </table>
                    <?php if(!empty($sonuc['vesika'])): ?>
                        <p style="color:#FFD700; margin-top:15px; font-weight:bold;">Vesikalık Fotoğraf:</p>
                        <img src="data:image/jpeg;base64,<?= $sonuc['vesika'] ?>" class="vesika-img" alt="Vesikalık">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


