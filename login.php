<?php
session_start();
if (isset($_SESSION["username"])) { header("Location: dashboard.php"); exit; }

$hata = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kullanici = trim($_POST["username"] ?? "");
    $sifre = trim($_POST["password"] ?? "");
    if ($kullanici && $sifre) {
        try {
            $db = new PDO("mysql:host=localhost;dbname=wyurs_vip;charset=utf8mb4", "root", "");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $db->prepare("SELECT * FROM kullanicilar WHERE username = ? AND password = ?");
            $stmt->execute([$kullanici, $sifre]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $_SESSION["username"] = $user["username"];
                $_SESSION["is_vip"] = $user["is_vip"]; // Burası çok önemli!
                header("Location: dashboard.php");
                exit;
            } else {
                $hata = "Kullanıcı adı veya şifre hatalı!";
            }
        } catch (PDOException $e) {
            $hata = "Veritabanı hatası: " . $e->getMessage();
        }
    } else {
        $hata = "Lütfen tüm alanları doldurun.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Wyurs VIP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #0a0a0a; color: #eee; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-box { background: #16161c; border: 1px solid #2a2a35; border-radius: 12px; padding: 40px; width: 100%; max-width: 400px; border-top: 4px solid #FFD700; box-shadow: 0 0 20px rgba(255, 215, 0, 0.1); }
        .login-box h2 { color: #FFD700; text-align: center; margin-bottom: 10px; font-size: 24px; }
        .login-box p { text-align: center; color: #888; margin-bottom: 30px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #aaa; margin-bottom: 8px; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; background: #111; border: 1px solid #333; border-radius: 8px; color: #fff; outline: none; transition: 0.3s; }
        .form-group input:focus { border-color: #FFD700; }
        .btn-login { width: 100%; background: #FFD700; color: #000; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; font-size: 16px; }
        .btn-login:hover { background: #fff; }
        .hata { color: #ff4444; background: rgba(255,0,0,0.1); padding: 10px; border-radius: 8px; border: 1px solid #ff4444; margin-bottom: 20px; text-align: center; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2><i class="fas fa-shield-alt"></i> Wyurs VIP</h2>
        <p>Devam etmek için giriş yapın</p>
        <?php if($hata): ?><div class="hata"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($hata) ?></div><?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Kullanıcı Adı</label>
                <input type="text" name="username" placeholder="Kullanıcı adınızı girin..." required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Şifre</label>
                <input type="password" name="password" placeholder="Şifrenizi girin..." required>
            </div>
            <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Giriş Yap</button>
        </form>
    </div>
</body>
</html>
