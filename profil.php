<?php require_once 'sorgu_bekcisi.php'; ?>
<?php
session_start();
if (!isset($_SESSION["username"])) { header("Location: login.php"); exit; }

$mesaj = "";
$mesajTipi = "";
$kullanici = $_SESSION["username"];

// --- FOTOĞRAF YÜKLEME İŞLEMİ ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["foto_yukle"])) {
    if (isset($_FILES["profil_resmi"]) && $_FILES["profil_resmi"]["error"] == 0) {
        $dosyaAdi = $_FILES["profil_resmi"]["name"];
        $dosyaBoyutu = $_FILES["profil_resmi"]["size"];
        $dosyaTipi = $_FILES["profil_resmi"]["type"];
        $geciciYol = $_FILES["profil_resmi"]["tmp_name"];
        
        // İzin verilen uzantılar
        $izinVerilen = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (in_array($dosyaTipi, $izinVerilen)) {
            if ($dosyaBoyutu < 2 * 1024 * 1024) { // Max 2MB
                // Dosya adını kullanıcı adına göre benzersiz yap
                $uzanti = pathinfo($dosyaAdi, PATHINFO_EXTENSION);
                $yeniAd = "profil_" . $kullanici . "." . $uzanti;
                $hedefYol = "uploads/" . $yeniAd;
                
                if (move_uploaded_file($geciciYol, $hedefYol)) {
                    // Oturuma fotoğraf yolunu kaydet (Basit simülasyon)
                    $_SESSION["profil_resmi"] = $hedefYol;
                    $mesaj = "Profil fotoğrafınız başarıyla yüklendi!";
                    $mesajTipi = "basari";
                } else {
                    $mesaj = "Dosya yüklenirken bir hata oluştu.";
                    $mesajTipi = "hata";
                }
            } else {
                $mesaj = "Dosya boyutu 2MB'dan küçük olmalıdır.";
                $mesajTipi = "hata";
            }
        } else {
            $mesaj = "Sadece JPG, PNG ve GIF formatları desteklenir.";
            $mesajTipi = "hata";
        }
    } else {
        $mesaj = "Lütfen bir dosya seçin.";
        $mesajTipi = "hata";
    }
}

// --- ŞİFRE DEĞİŞTİRME İŞLEMİ (Simülasyon) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sifre_degistir"])) {
    $eskiSifre = $_POST["eski_sifre"];
    $yeniSifre = $_POST["yeni_sifre"];
    $dogruSifre = "admin123"; 
    
    if ($eskiSifre === $dogruSifre) {
        if (strlen($yeniSifre) >= 4) {
            $mesaj = "Şifreniz başarıyla güncellendi! (Simülasyon)";
            $mesajTipi = "basari";
        } else {
            $mesaj = "Yeni şifre en az 4 karakter olmalıdır.";
            $mesajTipi = "hata";
        }
    } else {
        $mesaj = "Mevcut şifreniz yanlış! (Varsayılan: admin123)";
        $mesajTipi = "hata";
    }
}

// Değişkenler
$email = $_SESSION["email"] ?? 'E-posta belirtilmemiş';
$bakiye = $_SESSION["bakiye"] ?? '0.00';
$vip = $_SESSION["vip"] ?? 0;
$kayitTarihi = $_SESSION["kayit_tarihi"] ?? 'Bilinmiyor';
$profilResmi = $_SESSION["profil_resmi"] ?? null;
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
    <title>Profil - Wyurs VIP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #0a0a0a; color: #eee; display: flex; min-height: 100vh; }
        
        .sidebar { width: 250px; background: #111; border-right: 2px solid #FFD700; padding: 20px; position: fixed; height: 100%; z-index: 100; }
        .logo { font-size: 24px; color: #FFD700; font-weight: 900; margin-bottom: 30px; text-align: center; }
        .sidebar a { display: block; padding: 12px; color: #aaa; text-decoration: none; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #FFD700; color: #000; font-weight: bold; }
        .sidebar i { margin-right: 10px; }

        .main-content { margin-left: 250px; flex: 1; padding: 30px; }
        
        .profile-card { background: #16161c; border: 1px solid #FFD700; border-radius: 15px; padding: 30px; max-width: 600px; margin: 0 auto; box-shadow: 0 0 20px rgba(255, 215, 0, 0.1); }
        
        .profile-header { display: flex; align-items: center; gap: 20px; border-bottom: 1px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        
        .profile-avatar { 
    width: 90px; 
    height: 90px; 
    background: #FFD700; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-size: 40px; 
    color: #000; 
    font-weight: bold; 
    overflow: hidden; 
    border: 3px solid #333;
    position: relative;
}
.profile-avatar img { 
    width: 100%; 
    height: 100%; 
    object-fit: cover; /* Resmi çerçeveye tam oturtur */
    position: absolute;
    top: 0; 
    left: 0;
}
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        
        .profile-info h2 { color: #FFD700; margin-bottom: 5px; }
        .profile-info p { color: #888; font-size: 14px; margin-bottom: 10px; }
        
        .foto-form { display: flex; gap: 10px; align-items: center; }
        .foto-form input[type="file"] { 
    font-size: 12px; 
    color: #FFD700; 
    background: #111;
    padding: 5px;
    border-radius: 5px;
    border: 1px solid #333;
    max-width: 200px;
}
        .btn-foto { background: #333; color: #FFD700; border: 1px solid #FFD700; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px; transition: 0.3s; }
        .btn-foto:hover { background: #FFD700; color: #000; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .info-item { background: #111; padding: 15px; border-radius: 10px; border-left: 3px solid #FFD700; }
        .info-item label { display: block; color: #888; font-size: 12px; margin-bottom: 5px; }
        .info-item span { color: #fff; font-size: 16px; font-weight: bold; }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; color: #FFD700; margin-bottom: 5px; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; background: #111; border: 1px solid #333; border-radius: 8px; color: #fff; }
        .form-group input:focus { outline: none; border-color: #FFD700; }

        .btn-kaydet { background: #FFD700; color: #000; border: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-kaydet:hover { background: #fff; }

        @media screen and (max-width: 1024px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; padding: 15px; }
            .info-grid { grid-template-columns: 1fr; }
            .profile-header { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">Wyurs VIP</div>
        <a href="dashboard.php"><i class="fas fa-home"></i> Ana Sayfa</a>
        <a href="profil.php" class="active"><i class="fas fa-user"></i> Profil</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
    </div>

    <div class="main-content">
        <div class="profile-card">
            
            <?php if($mesaj): ?>
                <div style="padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: bold; 
                            background: <?= $mesajTipi == 'basari' ? 'rgba(0,255,0,0.1)' : 'rgba(255,0,0,0.1)' ?>; 
                            border: 1px solid <?= $mesajTipi == 'basari' ? '#00ff00' : '#ff0000' ?>; 
                            color: <?= $mesajTipi == 'basari' ? '#00ff00' : '#ff0000' ?>;">
                    <?= htmlspecialchars($mesaj) ?>
                </div>
            <?php endif; ?>

            <div class="profile-header">
                <div class="profile-avatar">
                    <?php if($profilResmi && file_exists($profilResmi)): ?>
                        <img src="<?= htmlspecialchars($profilResmi) ?>" alt="Profil Resmi">
                    <?php else: ?>
                        <?= strtoupper(substr($kullanici, 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <h2><?= htmlspecialchars($kullanici) ?></h2>
                    <p><?= htmlspecialchars($email) ?></p>
                    
                    <!-- FOTOĞRAF YÜKLEME FORMU -->
                    <form method="POST" action="" enctype="multipart/form-data" class="foto-form">
                        <input type="file" name="profil_resmi" accept="image/*" required>
                        <button type="submit" name="foto_yukle" class="btn-foto"><i class="fas fa-upload"></i> Yükle</button>
                    </form>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <label>Kullanıcı Adı</label>
                    <span><?= htmlspecialchars($kullanici) ?></span>
                </div>
                <div class="info-item">
                    <label>Bakiye</label>
                    <span><?= htmlspecialchars($bakiye) ?> TL</span>
                </div>
                <div class="info-item">
                    <label>VIP Durumu</label>
                    <span><?= ($vip == 1) ? 'Aktif VIP' : 'Standart Üye' ?></span>
                </div>
                <div class="info-item">
                    <label>Kayıt Tarihi</label>
                    <span><?= htmlspecialchars($kayitTarihi) ?></span>
                </div>
            </div>

            <form method="POST" action="">
                <h3 style="color:#FFD700; margin-bottom:15px; border-bottom:1px solid #333; padding-bottom:10px;">Şifre Değiştir</h3>
                <div class="form-group">
                    <label>Mevcut Şifre</label>
                    <input type="password" name="eski_sifre" required>
                </div>
                <div class="form-group">
                    <label>Yeni Şifre</label>
                    <input type="password" name="yeni_sifre" required>
                </div>
                <button type="submit" name="sifre_degistir" class="btn-kaydet">Şifreyi Güncelle</button>
            </form>
        </div>
    </div>

</body>
</html>


