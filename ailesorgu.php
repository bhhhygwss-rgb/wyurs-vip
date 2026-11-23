<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tc'])) {
    $tcLog = trim($_POST['tc']);
    $kullaniciLog = $_SESSION['username'] ?? 'Bilinmeyen';
    try {
        $logDb = new PDO("mysql:host=localhost;dbname=wyurs_vip;charset=utf8mb4", "root", "");
        $logStmt = $logDb->prepare("INSERT INTO sorgu_gecmisi (username, sorgu_turu, aranan_deger) VALUES (?, ?, ?)");
        $logStmt->execute([$kullaniciLog, 'Aile Sorgu', $tcLog]);
    } catch(PDOException $e) {}
}

try {
    $db = new PDO("mysql:host=localhost;dbname=101m;charset=utf8", "root", "");
} catch(PDOException $e) {
    die("Veritabani baglanti hatasi: " . $e->getMessage());
}

$r = null;
$hata = null;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tc = trim($_POST["tc"] ?? "");
    if ($tc) {
        try {
            $st = $db->prepare("SELECT * FROM 101m WHERE TC = ? LIMIT 1");
            $st->execute([$tc]);
            $r = $st->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) { $hata = $e->getMessage(); }
    } else $hata = "TC gir";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Aile Sorgu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #0a0a0a; color: #eee; padding: 40px 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        h2 { color: #eab308; margin-bottom: 20px; }
        form { background: #111; padding: 20px; border-radius: 8px; border: 1px solid #222; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; background: #0a0a0a; border: 1px solid #333; border-radius: 6px; color: #fff; margin-bottom: 12px; }
        button { padding: 12px 24px; background: #eab308; color: #000; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; }
        .res { background: #111; padding: 20px; border-radius: 8px; border: 1px solid #222; }
        .e { background: rgba(255,0,0,0.1); color: #fca5a5; padding: 15px; border-radius: 8px; }
        a { color: #eab308; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Aile Sorgu</h2>
        <a href="dashboard.php">← Panele Dön</a>
        <form method="post">
            <input name="tc" placeholder="TC Kimlik No" maxlength="11" value="<?=htmlspecialchars($_POST['tc']??'')?>">
            <button type="submit">Sorgula</button>
        </form>
        <?php if($hata):?><div class="e"><?=htmlspecialchars($hata)?></div><?php endif;?>
        <?php if($r):?>
            <div class="res">
                <?php foreach($r as $k=>$v){if($v!==null&&$v!=='')echo htmlspecialchars($k).': '.htmlspecialchars($v).'<br>';}?>
            </div>
        <?php elseif($_SERVER["REQUEST_METHOD"]==="POST"&&!$hata):?>
            <div class="e">Kayıt yok</div>
        <?php endif;?>
    </div>
</body>
</html>