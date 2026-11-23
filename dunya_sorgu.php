<?php require_once 'sorgu_bekcisi.php'; ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$u = $_SESSION["username"] ?? "admin";

$ulkeler = [
    "fr_db" => "Fransa (FR)",
    "gb_db" => "İngiltere (GB)",
    "de_db" => "Almanya (DE)",
    "us_db" => "ABD (US)"
];

$sonuc = "";
$hata = "";
$seciliUlke = $_POST["ulke"] ?? "fr_db";
$tc = $_POST["tc"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && $tc) {
    if (array_key_exists($seciliUlke, $ulkeler)) {
        $db = new mysqli('localhost', 'root', '', $seciliUlke);
        if ($db->connect_error) {
            $hata = "Baglanti Hatasi: " . $db->connect_error;
        } else {
            $db->set_charset("utf8mb4");
            $tcEsc = $db->real_escape_string($tc);
            $res = $db->query("SELECT * FROM 101m WHERE TC = '$tcEsc' LIMIT 1");
            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $sonuc = "<table class='result-table'>";
                foreach ($row as $k => $v) {
                    if ($v !== null && $v !== "null" && $v !== "") {
                        $sonuc .= "<tr><td>".htmlspecialchars($k)."</td><td>".htmlspecialchars($v)."</td></tr>";
                    }
                }
                $sonuc .= "</table>";
            } else {
                $hata = "Kayit bulunamadi.";
            }
        }
    } else {
        $hata = "Gecersiz ulke secimi.";
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
    <title>Dünya Sorgu — Daltonlar VIP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: #0d0d12; color: #e0e0e0; display: flex; }
        .sidebar { width: 250px; background-color: #16161c; border-right: 1px solid #2a2a35; height: 100vh; padding-top: 20px; position: fixed; display: flex; flex-direction: column; overflow-y: auto; }
        .sidebar .logo { color: #f39c12; font-weight: bold; font-size: 20px; padding: 0 20px 20px; border-bottom: 1px solid #2a2a35; margin-bottom: 20px; }
        .sidebar ul { list-style: none; padding: 0; margin: 0; flex: 1; }
        .sidebar li { padding: 10px 20px; color: #8b8b99; font-size: 13px; display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .sidebar li:hover, .sidebar li.active { color: #e0e0e0; background-color: rgba(255,255,255,0.05); border-left: 3px solid #f39c12; }
        .sidebar li i { width: 16px; text-align: center; }
        .logout { padding: 20px; color: #e74c3c; cursor: pointer; border-top: 1px solid #2a2a35; margin-top: auto; }

        .main-content { margin-left: 250px; padding: 30px; flex: 1; }
        .header-area h2 { margin: 0 0 20px 0; font-size: 22px; display: flex; align-items: center; gap: 10px; }
        .header-area h2 i { color: #f39c12; }
        .query-card { background-color: #16161c; border: 1px solid #2a2a35; border-radius: 12px; padding: 25px; max-width: 800px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 13px; color: #8b8b99; }
        .form-group input, .form-group select { width: 100%; max-width: 400px; padding: 12px; background-color: #0d0d12; border: 1px solid #2a2a35; border-radius: 8px; color: #fff; outline: none; }
        .form-group input:focus, .form-group select:focus { border-color: #f39c12; }
        .btn-query { background-color: #f39c12; color: #000; border: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
        .btn-query:hover { background-color: #e67e22; }
        .result-area { margin-top: 20px; padding: 20px; background-color: rgba(243, 156, 18, 0.05); border: 1px solid rgba(243, 156, 18, 0.2); border-radius: 8px; }
        .result-table { width: 100%; border-collapse: collapse; }
        .result-table td { padding: 10px; border-bottom: 1px solid #2a2a35; font-size: 14px; }
        .result-table td:first-child { color: #8b8b99; width: 30%; }
        .error { color: #e74c3c; font-weight: bold; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">Daltonlar VIP</div>
        <ul>
            <li onclick="window.location.href='dashboard.php'"><i class="fas fa-home"></i> Ana Sayfa</li>
            <li onclick="window.location.href='profile/overview.php'"><i class="fas fa-user"></i> Profil</li>
            <li onclick="window.location.href='profile/sorgu.php'"><i class="fas fa-search"></i> İstihbarat & Sorgu</li>
            <li class="active"><i class="fas fa-globe"></i> Dünya Sorgu</li>
        </ul>
        <div class="logout" onclick="window.location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Güvenli Çıkış</div>
    </div>

    <div class="main-content">
        <div class="header-area"><h2><i class="fas fa-globe"></i> Dünya Sorgu</h2></div>
        
        <div class="query-card">
            <form method="POST">
                <div class="form-group">
                    <label>Ülke Seçin</label>
                    <select name="ulke">
                        <?php foreach ($ulkeler as $db => $isim): ?>
                            <option value="<?php echo $db; ?>" <?php echo $seciliUlke === $db ? 'selected' : ''; ?>><?php echo $isim; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kimlik Numarası</label>
                    <input type="text" name="tc" maxlength="20" value="<?php echo htmlspecialchars($tc); ?>" required>
                </div>
                <button type="submit" class="btn-query"><i class="fas fa-search"></i> Sorgula</button>
            </form>

            <?php if ($hata): ?>
                <div class="result-area"><span class="error"><?php echo $hata; ?></span></div>
            <?php endif; ?>
            <?php if ($sonuc): ?>
                <div class="result-area"><?php echo $sonuc; ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


