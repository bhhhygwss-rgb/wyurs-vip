<?php
session_start();
if (!isset($_SESSION["username"])) { header("Location: login.php"); exit; }

try {
    $db = new PDO("mysql:host=localhost;dbname=wyurs_vip;charset=utf8mb4", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SESSION["username"] === "admin") {
        $stmt = $db->query("SELECT * FROM sorgu_gecmisi ORDER BY id DESC LIMIT 100");
    } else {
        $stmt = $db->prepare("SELECT * FROM sorgu_gecmisi WHERE username = ? ORDER BY id DESC LIMIT 100");
        $stmt->execute([$_SESSION["username"]]);
    }
    $gecmis = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $gecmis = [];
    $hata = "Veritabanı hatası: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sorgu Geçmişi - Wyurs VIP</title>
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
        table { width: 100%; border-collapse: collapse; background: #111; border-radius: 8px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #222; }
        th { background: #16161c; color: #FFD700; font-weight: bold; }
        tr:hover { background: #16161c; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
        .badge-admin { background: #FFD700; color: #000; }
        .badge-user { background: #333; color: #aaa; }
        .bos-mesaj { text-align: center; color: #888; padding: 50px; font-size: 18px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">Wyurs VIP</div>
        <a href="dashboard.php"><i class="fas fa-home"></i> Ana Sayfa</a>
        <a href="kullanici_yonetimi.php"><i class="fas fa-users"></i> Kullanıcı Yönetimi</a>
        <a href="sorgu_gecmisi.php" style="background: #FFD700; color: #000 !important;"><i class="fas fa-history"></i> Sorgu Geçmişi</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
    </div>
    <div class="main-content">
        <div class="header-area"><h2><i class="fas fa-history"></i> Sorgu Geçmişi</h2></div>
        
        <?php if(isset($hata)): ?>
            <div style="color:red; background:rgba(255,0,0,0.1); padding:15px; border-radius:8px; margin-bottom:20px;"><?= $hata ?></div>
        <?php endif; ?>

        <?php if(count($gecmis) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kullanıcı</th>
                    <th>Sorgu Türü</th>
                    <th>Aranan Değer</th>
                    <th>Tarih</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($gecmis as $g): ?>
                <tr>
                    <td><?= $g['id'] ?></td>
                    <td>
                        <?php if($g['username'] === 'admin'): ?>
                            <span class="badge badge-admin">ADMIN</span>
                        <?php else: ?>
                            <span class="badge badge-user"><?= htmlspecialchars($g['username']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($g['sorgu_turu']) ?></td>
                    <td><?= htmlspecialchars($g['aranan_deger']) ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($g['tarih'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="bos-mesaj"><i class="fas fa-inbox"></i> Henüz hiç sorgu yapılmamış.</div>
        <?php endif; ?>
    </div>
</body>
</html>
