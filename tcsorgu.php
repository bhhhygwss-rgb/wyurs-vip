<?php require_once 'sorgu_bekcisi.php'; ?>
<?php
session_start();
if (!isset($_SESSION["username"])) { header("Location: login.php"); exit; }
require "db.php";
$db = getDB();
$r = null; $hata = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!$db) { $hata = "DB: " . dbError(); }
    else {
        $tc = trim($_POST["tc"] ?? "");
        if ($tc) {
            try {
                $st = $db->prepare("SELECT * FROM 101m WHERE TC = ? LIMIT 1");
                $st->execute([$tc]);
                $r = $st->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) { $hata = $e->getMessage(); }
        } else $hata = "TC gir";
    }
}
?>
<!DOCTYPE html><html lang="tr"><head>
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
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>TC</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:#0a0a0f;color:#eee;font-family:sans-serif;font-size:14px}
nav{background:#111;padding:8px;display:flex;gap:6px;overflow-x:auto;white-space:nowrap}
nav a{color:#eab308;text-decoration:none;padding:6px 8px;background:#222;border-radius:4px;font-size:12px}
.box{padding:10px}
input,button{width:100%;padding:10px;margin:4px 0;border:1px solid #333;border-radius:6px;background:#18181b;color:#fff;font-size:15px}
button{background:linear-gradient(90deg,#b91c1c,#eab308);border:0;font-weight:700}
.res{margin-top:10px;background:#18181b;padding:10px;border-radius:6px;font-size:13px;word-break:break-all}
.e{color:#f87171;margin-top:8px}
</style></head>
<body>
<nav>
<a href="dashboard.php">Panel</a>
<a href="tcsorgu.php">TC</a>
<a href="adsoyad.php">Ad</a>
<a href="tcdennumara.php">GSM</a>
<a href="nodantc.php">Noâ†’TC</a>
<a href="sulale.php">SÃ¼lale</a>
</nav>
<div class="box">
<h3 style="color:#eab308;margin-bottom:8px">TC Sorgu</h3>
<form method="post">
<input name="tc" placeholder="TC" maxlength="11" value="<?=htmlspecialchars($_POST['tc']??'')?>">
<button>SORGULA</button>
</form>
<?php if($hata):?><div class="e"><?=htmlspecialchars($hata)?></div><?php endif;?>
<?php if($r):?><div class="res"><?php foreach($r as $k=>$v){if($v!==null&&$v!=='')echo htmlspecialchars($k).': '.htmlspecialchars($v).'<br>';}?></div><?php elseif($_SERVER["REQUEST_METHOD"]==="POST"&&!$hata):?><div class="e">Yok</div><?php endif;?>
</div>
</body></html>


