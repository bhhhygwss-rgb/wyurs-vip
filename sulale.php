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
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>TC Sorgu â€” Daltonlar</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:#0a0a0f;color:#eee;font-family:sans-serif;min-height:100vh}
.s{background:#0f0f14;border-bottom:1px solid #eab308;padding:8px;display:flex;gap:8px;flex-wrap:wrap;overflow-x:auto}
.s a{color:#eab308;text-decoration:none;padding:4px 8px;background:#18181b;border-radius:6px;font-size:12px;white-space:nowrap}
.m{padding:12px}
.c{background:#12121a;border:1px solid #333;border-radius:12px;padding:14px}
input{width:100%;padding:10px;margin:6px 0;background:#18181b;border:1px solid #333;border-radius:8px;color:#fff;font-size:15px}
button{width:100%;padding:10px;background:linear-gradient(90deg,#b91c1c,#eab308);border:0;border-radius:8px;font-weight:700;cursor:pointer;font-size:15px}
.res{margin-top:14px;background:#18181b;padding:12px;border-radius:8px;font-size:14px;line-height:1.5}
.e{color:#f87171;margin-top:10px}
@media(min-width:769px){
body{display:flex}
.s{width:240px;border-bottom:0;border-right:1px solid #eab308;flex-direction:column;height:100vh;position:fixed}
.m{margin-left:240px;flex:1}
}
</style></head>
<body>
<div class="s">
<a href="dashboard.php">â† Panel</a>
<a href="tcsorgu.php">TC</a>
<a href="adsoyad.php">Ad Soyad</a>
<a href="tcdennumara.php">TCâ†’GSM</a>
<a href="nodantc.php">GSMâ†’TC</a>
<a href="sulale.php">SÃ¼lale</a>
</div>
<div class="m"><div class="c">
<h2 style="color:#eab308;margin-bottom:12px">TC Sorgu</h2>
<form method="post">
<input name="tc" placeholder="TC Kimlik No" maxlength="11" value="<?=htmlspecialchars($_POST['tc']??'')?>">
<button>SORGULA</button>
</form>
<?php if($hata):?><div class="e"><?=htmlspecialchars($hata)?></div><?php endif;?>
<?php if($r):?><div class="res"><?php foreach($r as $k=>$v){if($v!==null&&$v!=='')echo htmlspecialchars($k).': '.htmlspecialchars($v).'<br>';}?></div><?php elseif($_SERVER["REQUEST_METHOD"]==="POST"&&!$hata):?><div class="e">KayÄ±t yok</div><?php endif;?>
</div></div>
<script src="snow.js"></script>
</body></html>


