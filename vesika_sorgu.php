<?php require_once 'sorgu_bekcisi.php'; ?>
<?php
$host = 'localhost';
$db = 'vesika_db';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$tc = $_GET['tc'] ?? '';

if ($tc) {
    $stmt = $conn->prepare("SELECT * FROM `60k_vesika` WHERE tc = ?");
    $stmt->bind_param("s", $tc);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
} else {
    $row = null;
}
?>
<!DOCTYPE html>
<html>
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
    <title>Vesika Sorgu</title>
    <style>
        body { font-family: Arial; background: #1a1a1a; color: #eee; padding: 20px; }
        .card { background: #222; border-radius: 8px; padding: 20px; margin: 10px 0; }
        input { padding: 10px; width: 300px; }
        button { padding: 10px 20px; background: #ff9800; border: none; color: #fff; cursor: pointer; }
        img { max-width: 200px; border-radius: 8px; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Vesika Sorgu</h2>
    <div class="card">
        <form method="get">
            <input type="text" name="tc" placeholder="TC Kimlik No" value="<?= htmlspecialchars($tc) ?>">
            <button type="submit">Sorgula</button>
        </form>
    </div>
    <?php if ($row): ?>
        <div class="card">
            <h3><?= htmlspecialchars($row['isim'] . ' ' . $row['soyisim']) ?></h3>
            <p>TC: <?= htmlspecialchars($row['tc']) ?></p>
            <img src="data:image/jpeg;base64,<?= $row['vesika'] ?>" alt="Vesikalık">
        </div>
    <?php elseif ($tc): ?>
        <div class="card">Sonuç bulunamadı.</div>
    <?php endif; ?>
</body>
</html>


