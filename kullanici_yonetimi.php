<?php
session_start();
// 1. Giriş yapılmış mı kontrol et
if (!isset($_SESSION["username"])) { header("Location: login.php"); exit; }

// 2. Sadece "admin" kullanıcısının girmesine izin ver
if ($_SESSION["username"] !== "admin") { 
    die("<div style='background:#111; color:#ff4444; padding:50px; text-align:center; font-family:sans-serif;'>
            <h1>Erişim Engellendi</h1>
            <p>Bu sayfayı sadece 'admin' yetkisine sahip kişiler görüntüleyebilir.</p>
            <a href='dashboard.php' style='color:#000; background:#FFD700; padding:10px 20px; text-decoration:none; border-radius:5px;'>Panele Dön</a>
         </div>"); 
}

$db = new PDO("mysql:host=localhost;dbname=wyurs_vip;charset=utf8mb4", "root", "");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// VIP Yetkisi Değiştirme
if (isset($_GET['toggle_id'])) {
    $id = intval($_GET['toggle_id']);
    $stmt = $db->prepare("UPDATE kullanicilar SET is_vip = 1 - is_vip WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: kullanici_yonetimi.php");
    exit;
}

// Yeni Kullanıcı Ekleme
$mesaj = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['yeni_kullanici'])) {
    $yeniAd = trim($_POST['yeni_username'] ?? '');
    $yeniSifre = trim($_POST['yeni_password'] ?? '');
    $yeniVip = isset($_POST['yeni_is_vip']) ? 1 : 0;

    if ($yeniAd && $yeniSifre) {
        try {
            $stmt = $db->prepare("INSERT INTO kullanicilar (username, password, is_vip) VALUES (?, ?, ?)");
            $stmt->execute([$yeniAd, $yeniSifre, $yeniVip]);
            $mesaj = "<div style='color: #28a745; background: rgba(40,167,69,0.1); padding: 10px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #28a745;'><i class='fas fa-check-circle'></i> Kullanıcı başarıyla eklendi!</div>";
        } catch (PDOException $e) {
            $mesaj = "<div style='color: #dc3545; background: rgba(220,53,69,0.1); padding: 10px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #dc3545;'><i class='fas fa-exclamation-triangle'></i> Hata: Bu kullanıcı adı zaten alınmış olabilir.</div>";
        }
    } else {
        $mesaj = "<div style='color: #dc3545; background: rgba(220,53,69,0.1); padding: 10px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #dc3545;'><i class='fas fa-exclamation-triangle'></i> Lütfen tüm alanları doldurun.</div>";
    }
}

$kullanicilar = $db->query("SELECT * FROM kullanicilar ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Yönetimi - Wyurs VIP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #0a0a0a; color: #eee; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #111; border-right: 2px solid #FFD700; padding: 20px; position: fixed; height: 100%; z-index: 100; }
        .logo { font-size: 24px; color: #FFD700; font-weight: 900; margin-bottom: 30px; text-align: center; }
        .sidebar a { display: block; padding: 12px; color: #FFD700 !important; text-decoration: none; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .sidebar a:hover { background: #FFD700; color: #000 !important; }
        .main-content { margin-left: 260px; flex: 1; padding: 30px; }
        .header-area h2 { color: #FFD700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        
        .form-ekle { background: #16161c; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #FFD700; display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 150px; }
        .form-group label { display: block; color: #aaa; margin-bottom: 5px; font-size: 12px; }
        .form-group input[type="text"], .form-group input[type="password"] { width: 100%; padding: 10px; background: #111; border: 1px solid #333; border-radius: 6px; color: #fff; outline: none; }
        .form-group input:focus { border-color: #FFD700; }
        .checkbox-group { display: flex; align-items: center; gap: 5px; height: 40px; }
        .checkbox-group input { width: 16px; height: 16px; cursor: pointer; }
        .checkbox-group label { margin: 0; cursor: pointer; color: #FFD700; font-weight: bold; }
        .btn-ekle { background: #FFD700; color: #000; padding: 10px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; height: 40px; transition: 0.3s; }
        .btn-ekle:hover { background: #fff; }

        table { width: 100%; border-collapse: collapse; background: #111; border-radius: 8px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #222; }
        th { background: #16161c; color: #FFD700; font-weight: bold; }
        tr:hover { background: #16161c; }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-block; border: none; cursor: pointer; }
        .btn-vip { background: #28a745; color: white; }
        .btn-vip-off { background: #dc3545; color: white; }
        .badge-vip { background: #FFD700; color: #000; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
        .badge-normal { background: #333; color: #aaa; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">Wyurs VIP</div>
        <a href="dashboard.php"><i class="fas fa-home"></i> Ana Sayfa</a>
        <a href="kullanici_yonetimi.php" style="background: #FFD700; color: #000 !important;"><i class="fas fa-users"></i> Kullanıcı Yönetimi</a>
        <a href="sorgu_gecmisi.php"><i class="fas fa-history"></i> Sorgu Geçmişi</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
    </div>
    <div class="main-content">
        <div class="header-area"><h2><i class="fas fa-users-cog"></i> Kullanıcı Yönetimi</h2></div>
        
        <?= $mesaj ?>

        <form method="POST" action="" class="form-ekle">
            <input type="hidden" name="yeni_kullanici" value="1">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Kullanıcı Adı</label>
                <input type="text" name="yeni_username" placeholder="Örn: ahmet123" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Şifre</label>
                <input type="password" name="yeni_password" placeholder="Örn: 123456" required>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" name="yeni_is_vip" id="vipCheck">
                <label for="vipCheck">VIP Yetkisi Ver</label>
            </div>
            <button type="submit" class="btn-ekle"><i class="fas fa-plus"></i> Kullanıcı Ekle</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kullanıcı Adı</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($kullanicilar as $k): ?>
                <tr>
                    <td><?= $k['id'] ?></td>
                    <td><?= htmlspecialchars($k['username']) ?></td>
                    <td>
                        <?php if($k['is_vip'] == 1): ?>
                            <span class="badge-vip">VIP ÜYE</span>
                        <?php else: ?>
                            <span class="badge-normal">NORMAL ÜYE</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($k['is_vip'] == 1): ?>
                            <a href="?toggle_id=<?= $k['id'] ?>" class="btn btn-vip-off"><i class="fas fa-times"></i> VIP Kaldır</a>
                        <?php else: ?>
                            <a href="?toggle_id=<?= $k['id'] ?>" class="btn btn-vip"><i class="fas fa-check"></i> VIP Yap</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

