<?php require_once 'sorgu_bekcisi.php'; ?>
<?php
session_start();
if (!isset($_SESSION["username"])) { header("Location: login.php"); exit; }
require "db.php";
$db = getDB();
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
</script><meta charset="UTF-8"><title>DB Test</title>
<style>body{background:#0a0a0f;color:#eee;font-family:sans-serif;padding:20px}pre{background:#12121a;padding:12px;border-radius:8px;overflow:auto}.e{color:#f87171}.ok{color:#22c55e}</style></head>
<body>
<h2>DB TeÅŸhis</h2>
<?php if(!$db): ?>
<div class="e">BaÄŸlantÄ± yok: <?=htmlspecialchars(dbError())?></div>
<?php else: ?>
<div class="ok">BaÄŸlantÄ± OK</div>
<?php
try {
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<h3>Tablolar:</h3><pre>".htmlspecialchars(implode("\n",$tables))."</pre>";
    if (in_array("101m",$tables)) {
        $cols = $db->query("DESCRIBE 101m")->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>101m SÃ¼tunlarÄ±:</h3><pre>";
        foreach($cols as $c) echo htmlspecialchars($c["Field"]." | ".$c["Type"])."\n";
        echo "</pre>";
        $count = $db->query("SELECT COUNT(*) FROM 101m")->fetchColumn();
        echo "<div>KayÄ±t sayÄ±sÄ±: $count</div>";
        $sample = $db->query("SELECT * FROM 101m LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($sample) {
            echo "<h3>Ã–rnek SatÄ±r:</h3><pre>";
            foreach($sample as $k=>$v) echo htmlspecialchars("$k: $v")."\n";
            echo "</pre>";
        }
    } else {
        echo "<div class='e'>101m tablosu yok</div>";
    }
} catch(Exception $e) { echo "<div class='e'>".htmlspecialchars($e->getMessage())."</div>"; }
?>
<?php endif; ?>
<p><a href="dashboard.php" style="color:#eab308">â† Panel</a></p>
<script src="snow.js"></script>
</body></html>


