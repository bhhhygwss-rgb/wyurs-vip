<?php require_once 'sorgu_bekcisi.php'; ?>
<?php
error_reporting(0);
$api_url = "";
$sonuclar = null;
$hata = "";

$page = basename($_SERVER['PHP_SELF']);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && count($_GET) > 0) {
    switch ($page) {
        case 'api_tc.php': if(isset($_GET['tc'])) $api_url = "https://api.ajaxsystems.fun/tc.php?tc=" . urlencode($_GET['tc']); break;
        case 'api_tcpro.php': if(isset($_GET['tc'])) $api_url = "https://api.ajaxsystems.fun/tcpro.php?tc=" . urlencode($_GET['tc']); break;
        case 'api_aile.php': if(isset($_GET['tc'])) $api_url = "https://api.ajaxsystems.fun/aile.php?tc=" . urlencode($_GET['tc']); break;
        case 'api_ailepro.php': if(isset($_GET['tc'])) $api_url = "https://api.ajaxsystems.fun/ailepro.php?tc=" . urlencode($_GET['tc']); break;
        case 'api_sulale.php': if(isset($_GET['tc'])) $api_url = "https://api.ajaxsystems.fun/sulale.php?tc=" . urlencode($_GET['tc']); break;
        case 'api_tcgsm.php': if(isset($_GET['tc'])) $api_url = "https://api.ajaxsystems.fun/tcgsm.php?tc=" . urlencode($_GET['tc']); break;
        case 'api_gsmtc.php': if(isset($_GET['gsm'])) $api_url = "https://api.ajaxsystems.fun/gsmtc.php?gsm=" . urlencode($_GET['gsm']) . "&auth=fire"; break;
        case 'api_eokul.php': if(isset($_GET['tc'])) $api_url = "https://api.ajaxsystems.fun/eokul.php?tc=" . urlencode($_GET['tc']); break;
        case 'api_adres.php': if(isset($_GET['tc'])) $api_url = "https://api.ajaxsystems.fun/adres.php?tc=" . urlencode($_GET['tc']); break;
        case 'api_tapu.php': if(isset($_GET['tc'])) $api_url = "https://api.ajaxsystems.fun/tapu.php?tc=" . urlencode($_GET['tc']); break;
        case 'api_adsoyad.php': 
            if(isset($_GET['ad']) || isset($_GET['soyad']) || isset($_GET['il'])) {
                $api_url = "https://api.ajaxsystems.fun/adsoyad.php?ad=" . urlencode($_GET['ad'] ?? '') . "&soyad=" . urlencode($_GET['soyad'] ?? '');
                if (!empty($_GET['il'])) $api_url .= "&il=" . urlencode($_GET['il']);
                if (!empty($_GET['ilce'])) $api_url .= "&ilce=" . urlencode($_GET['ilce']);
            }
            break;
        case 'api_adaparsel.php': 
            if(isset($_GET['il']) || isset($_GET['ilce'])) 
                $api_url = "https://api.ajaxsystems.fun/adaparsel.php?il=" . urlencode($_GET['il'] ?? '') . "&ilce=" . urlencode($_GET['ilce'] ?? ''); 
            break;
    }
    
    if ($api_url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        
        if(curl_errno($ch)){
            $hata = "Bağlantı Hatası: " . curl_error($ch);
        } else {
            $decoded = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                if (isset($decoded['data'])) {
                    $raw_data = $decoded['data'];
                    if (is_string($raw_data)) {
                        $clean_data = stripslashes($raw_data);
                        $inner_array = json_decode($clean_data, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($inner_array)) {
                            $sonuclar = isset($inner_array[0]) && is_array($inner_array[0]) ? $inner_array : [$inner_array];
                        } else {
                            $sonuclar = [$decoded];
                        }
                    } else {
                        $sonuclar = is_array($raw_data) ? $raw_data : [$decoded];
                    }
                } else {
                    if (isset($decoded[0]) && is_array($decoded[0])) {
                        $sonuclar = $decoded;
                    } else {
                        $sonuclar = [$decoded];
                    }
                }
            } else {
                $sonuclar = $response;
            }
        }
        curl_close($ch);
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
    <title><?= ucfirst(str_replace(array('api_', '.php'), '', $page)) ?> Sorgulama</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Excel İndirme İçin Kütüphane -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- PDF İndirme İçin Kütüphane -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        :root { --bg-color: #0b0c10; --panel-bg: #12141a; --text-main: #e0e0e0; --accent: #ffc107; --green: #00e676; --red: #ff1744; }
        body { background: var(--bg-color); color: var(--text-main); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; margin: 0; background-image: radial-gradient(circle, rgba(255,193,7,0.05) 1px, transparent 1px); background-size: 20px 20px; }
        
        .container { max-width: 1400px; margin: 0 auto; background: var(--panel-bg); border: 1px solid #333; border-radius: 12px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .header { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: #fff; font-size: 20px; font-weight: bold; }
        .header i { color: var(--accent); }
        .desc { font-size: 13px; color: #888; margin-bottom: 20px; }
        
        /* Form Alanı */
        .search-form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .search-form input { background: #1a1d24; border: 1px solid #333; color: #fff; padding: 12px 15px; border-radius: 6px; font-size: 14px; outline: none; transition: 0.3s; }
        .search-form input:focus { border-color: var(--accent); box-shadow: 0 0 5px rgba(255,193,7,0.2); }
        
        /* Sorgula / Sıfırla Butonları */
        .button-group { display: flex; justify-content: center; gap: 15px; margin-bottom: 25px; }
        .btn-action { padding: 12px 40px; border: none; border-radius: 6px; font-weight: bold; font-size: 15px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-sorgula { background: var(--green); color: #000; box-shadow: 0 0 15px rgba(0,230,118,0.3); }
        .btn-sorgula:hover { box-shadow: 0 0 25px rgba(0,230,118,0.6); }
        .btn-sifirla { background: var(--red); color: #fff; box-shadow: 0 0 15px rgba(255,23,68,0.3); text-decoration: none; }
        .btn-sifirla:hover { box-shadow: 0 0 25px rgba(255,23,68,0.6); }
        
        /* Alt Butonlar ve Genel Arama */
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .btn-group { display: flex; gap: 8px; }
        .btn-small { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 12px; display: flex; align-items: center; gap: 5px; }
        .btn-copy { background: #ffc107; color: #000; }
        .btn-excel { background: #217346; color: #fff; }
        .btn-pdf { background: #d32f2f; color: #fff; }
        .btn-print { background: #0288d1; color: #fff; }
        
        .general-search { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #aaa; }
        .general-search input { background: #1a1d24; border: 1px solid #333; color: #fff; padding: 8px 12px; border-radius: 4px; outline: none; }
        
        /* Tablo */
        .table-wrapper { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; white-space: nowrap; min-width: max-content; }
        th { color: var(--accent); text-align: left; padding: 15px 10px; font-weight: bold; border-bottom: 2px solid var(--accent); }
        td { padding: 12px 10px; border-bottom: 1px solid #222; }
        tr:hover { background: rgba(255,255,255,0.02); }
        
        /* Sütun İçi Filtre Kutuları */
        .col-filter { background: #1a1d24; border: 1px solid #333; color: #fff; padding: 6px 10px; border-radius: 4px; font-size: 12px; width: 90%; outline: none; margin-top: 5px; }
        .col-filter:focus { border-color: var(--accent); }
        
        /* Pagination (Alt Kısım) */
        .pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; font-size: 13px; color: #888; }
        .page-btns { display: flex; gap: 10px; }
        .btn-page { background: #1a1d24; border: 1px solid #333; color: #888; padding: 8px 15px; border-radius: 4px; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><i class="fas fa-address-card"></i> <?= ucfirst(str_replace(array('api_', '.php'), '', $page)) ?> Sorgu</div>
        <div class="desc">Ad, Soyad, İl ve İlçe bilgilerini girerek arama yapabilirsiniz. Maksimum 1000 kayıt listelenir, sayfada 500 sonuç gösterilir.</div>
        
        <form method="GET">
            <div class="search-form">
                <?php if ($page == 'api_adsoyad.php'): ?>
                    <input type="text" name="ad" placeholder="Ad *" value="<?= htmlspecialchars($_GET['ad'] ?? '') ?>">
                    <input type="text" name="soyad" placeholder="Soyad *" value="<?= htmlspecialchars($_GET['soyad'] ?? '') ?>">
                    <input type="text" name="il" placeholder="Adres İl (Opsiyonel)" value="<?= htmlspecialchars($_GET['il'] ?? '') ?>">
                    <input type="text" name="ilce" placeholder="Adres İlçe (Opsiyonel)" value="<?= htmlspecialchars($_GET['ilce'] ?? '') ?>">
                <?php elseif ($page == 'api_adaparsel.php'): ?>
                    <input type="text" name="il" placeholder="İl *" value="<?= htmlspecialchars($_GET['il'] ?? '') ?>" required>
                    <input type="text" name="ilce" placeholder="İlçe *" value="<?= htmlspecialchars($_GET['ilce'] ?? '') ?>" required>
                    <input type="text" name="mahalle" placeholder="Mahalle (Opsiyonel)" value="<?= htmlspecialchars($_GET['mahalle'] ?? '') ?>">
                    <input type="text" name="ada" placeholder="Ada (Opsiyonel)" value="<?= htmlspecialchars($_GET['ada'] ?? '') ?>">
                <?php elseif ($page == 'api_gsmtc.php'): ?>
                    <input type="text" name="gsm" placeholder="GSM Numarası (Örn: 5550000000) *" value="<?= htmlspecialchars($_GET['gsm'] ?? '') ?>" required>
                <?php else: ?>
                    <input type="text" name="tc" placeholder="TC Kimlik No *" value="<?= htmlspecialchars($_GET['tc'] ?? '') ?>" required>
                <?php endif; ?>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-action btn-sorgula"><i class="fas fa-search"></i> Sorgula</button>
                <a href="<?= $page ?>" class="btn-action btn-sifirla"><i class="fas fa-sync-alt"></i> Sıfırla</a>
            </div>
        </form>

        <?php if ($hata): ?>
            <div style="color: #ff5252; text-align: center; padding: 20px;"><?= $hata ?></div>
        <?php elseif ($sonuclar !== null): ?>
            
            <div class="toolbar">
                <div class="btn-group">
                    <button class="btn-small btn-copy" onclick="copyTable()"><i class="fas fa-copy"></i> Kopyala</button>
                    <button class="btn-small btn-excel" onclick="downloadExcel()"><i class="fas fa-file-excel"></i> Excel İndir</button>
                    <button class="btn-small btn-pdf" onclick="downloadPDF()"><i class="fas fa-file-pdf"></i> PDF İndir</button>
                    <button class="btn-small btn-print" onclick="window.print()"><i class="fas fa-print"></i> Yazdır</button>
                </div>
                <div class="general-search">
                    Genel Arama: 
                    <input type="text" id="globalSearch" onkeyup="filterGlobal()">
                </div>
            </div>
            
            <?php if (empty($sonuclar) && $sonuclar !== "0"): ?>
                <div style="text-align: center; padding: 20px; color: #888;">Kayıt bulunamadı.</div>
            <?php else: ?>
                <!-- PDF için bu alanı sarıyoruz -->
                <div id="pdf-content">
                    <div class="table-wrapper">
                        <table id="dataTable">
                            <thead>
                                <tr>
                                    <?php 
                                    $firstItem = isset($sonuclar[0]) ? $sonuclar[0] : $sonuclar;
                                    $headers = [];
                                    if (is_array($firstItem)) {
                                        foreach (array_keys($firstItem) as $key) {
                                            $headers[] = $key;
                                            echo "<th>" . mb_strtoupper(htmlspecialchars($key), 'UTF-8') . "</th>";
                                        }
                                    } else {
                                        echo "<th>API YANITI</th>";
                                    }
                                    ?>
                                </tr>
                                <tr class="filter-row no-print">
                                    <?php 
                                    if (!empty($headers)) {
                                        foreach ($headers as $h) {
                                            echo "<th><input type='text' class='col-filter' onkeyup='filterColumn(this, " . array_search($h, $headers) . ")' placeholder='" . htmlspecialchars(ucfirst(strtolower($h))) . " Ara...'></th>";
                                        }
                                    } else {
                                        echo "<th></th>";
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php 
                                $rows = (isset($sonuclar[0]) && is_array($sonuclar[0])) ? $sonuclar : [$sonuclar];
                                foreach ($rows as $row): 
                                    if (!is_array($row) && !is_string($row)) continue;
                                ?>
                                <tr>
                                    <?php 
                                    if (is_array($row)) {
                                        foreach ($row as $val) {
                                            echo "<td>" . htmlspecialchars(is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val) . "</td>";
                                        }
                                    } else {
                                        echo "<td>" . htmlspecialchars($row) . "</td>";
                                    }
                                    ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="pagination no-print">
                    <div><?= count($rows) ?> kayıttan 1 - <?= count($rows) ?> arası gösteriliyor</div>
                    <div class="page-btns">
                        <button class="btn-page">Önceki</button>
                        <button class="btn-page" style="background: #0288d1; color: #fff; border-color: #0288d1;">1</button>
                        <button class="btn-page">Sonraki</button>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
    // 1. GENEL ARAMA
    function filterGlobal() {
        let input = document.getElementById("globalSearch").value.toLowerCase();
        let rows = document.querySelectorAll("#tableBody tr");
        
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(input) ? "" : "none";
        });
    }

    // 2. SÜTUN BAZLI ARAMA
    function filterColumn(inputElement, colIndex) {
        let filter = inputElement.value.toLowerCase();
        let rows = document.querySelectorAll("#tableBody tr");
        
        rows.forEach(row => {
            let cell = row.getElementsByTagName("td")[colIndex];
            if (cell) {
                let text = cell.innerText.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });
    }

    // 3. KOPYALA BUTONU (Panoya Kopyalar)
    function copyTable() {
        let table = document.getElementById("dataTable");
        let rows = table.querySelectorAll("tr");
        let text = "";
        
        rows.forEach(row => {
            // Filtre satırını atla
            if (row.classList.contains('filter-row')) return;
            
            let cells = row.querySelectorAll("th, td");
            let rowText = [];
            cells.forEach(cell => rowText.push(cell.innerText.trim()));
            text += rowText.join("\t") + "\n";
        });
        
        navigator.clipboard.writeText(text).then(() => {
            alert("Tablo panoya kopyalandı!");
        }).catch(err => {
            alert("Kopyalama başarısız: " + err);
        });
    }

    // 4. EXCEL İNDİR (SheetJS)
    function downloadExcel() {
        let table = document.getElementById("dataTable");
        // Filtre satırını Excel'e aktarmamak için klonluyoruz ve o satırı siliyoruz
        let cloneTable = table.cloneNode(true);
        let filterRow = cloneTable.querySelector('.filter-row');
        if(filterRow) filterRow.remove();
        
        let wb = XLSX.utils.table_to_book(cloneTable, {sheet: "Sayfa1"});
        XLSX.writeFile(wb, "<?= str_replace('.php', '', $page) ?>_Sonuclar.xlsx");
    }

    // 5. PDF İNDİR (html2pdf)
    function downloadPDF() {
        let element = document.getElementById('pdf-content');
        let opt = {
            margin:       0.5,
            filename:     '<?= str_replace('.php', '', $page) ?>_Sonuclar.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
        };
        html2pdf().set(opt).from(element).save();
    }
    </script>
    
    <style>
        /* Yazdırma esnasında butonları ve filtreleri gizle */
        @media print {
            .no-print, .toolbar, .button-group, .search-form, .header .desc { display: none !important; }
            body { background: #fff; color: #000; }
            th { color: #000; border-bottom: 2px solid #000; }
            td { border-bottom: 1px solid #ccc; }
        }
    </style>
<!-- ARKA PLAN VE EFEKTLER BASLANGIC -->
<style>
    /* Koyu Arka Plan */
    body {
        background-color: #0a0a0a !important;
        color: #eee !important;
    }

    /* Yıldızlar */
    .yildiz-arkaplan {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        pointer-events: none; z-index: -2; overflow: hidden;
    }
    .yildiz {
        position: absolute; background-color: #ffffff; border-radius: 50%;
        box-shadow: 0 0 8px 2px rgba(255, 255, 255, 0.8);
        animation: yildizParla linear infinite;
    }
    @keyframes yildizParla {
        0% { opacity: 0.2; transform: scale(0.8); }
        50% { opacity: 1; transform: scale(1.2); }
        100% { opacity: 0.2; transform: scale(0.8); }
    }

    /* Meteorlar */
    .meteor-kapsayici {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        pointer-events: none; z-index: -2; overflow: hidden;
    }
    .meteor {
        position: absolute; top: -10%; width: 2px; height: 80px;
        background: linear-gradient(to bottom, rgba(255, 215, 0, 1), rgba(255, 140, 0, 0.5), transparent);
        transform: rotate(45deg);
        animation: meteorDus linear infinite;
    }
    @keyframes meteorDus {
        0% { transform: translateY(-100px) translateX(0) rotate(45deg); opacity: 1; }
        100% { transform: translateY(120vh) translateX(-50vw) rotate(45deg); opacity: 0; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Yıldızları Oluştur
    const yildizKapsayici = document.createElement('div');
    yildizKapsayici.classList.add('yildiz-arkaplan');
    document.body.appendChild(yildizKapsayici);

    for (let i = 0; i < 70; i++) {
        const y = document.createElement('div');
        y.classList.add('yildiz');
        const boyut = Math.random() * 3 + 1;
        y.style.width = boyut + 'px'; y.style.height = boyut + 'px';
        y.style.left = Math.random() * 100 + 'vw';
        y.style.top = Math.random() * 100 + 'vh';
        y.style.animationDuration = (Math.random() * 3 + 1.5) + 's';
        y.style.animationDelay = Math.random() * 3 + 's';
        yildizKapsayici.appendChild(y);
    }

    // Meteorları Oluştur
    const meteorKapsayici = document.createElement('div');
    meteorKapsayici.classList.add('meteor-kapsayici');
    document.body.appendChild(meteorKapsayici);

    function meteorOlustur() {
        if (document.querySelectorAll('.meteor').length > 20) return;
        const m = document.createElement('div');
        m.classList.add('meteor');
        m.style.left = Math.random() * 100 + 'vw';
        const sure = Math.random() * 2 + 1.5;
        m.style.animationDuration = sure + 's';
        m.style.animationDelay = Math.random() * 2 + 's';
        m.style.height = (Math.random() * 60 + 40) + 'px';
        meteorKapsayici.appendChild(m);
        setTimeout(() => { m.remove(); }, (sure + 2) * 1000);
    }
    setInterval(meteorOlustur, 400);
});
</script>
<!-- ARKA PLAN VE EFEKTLER SON -->
</body>
</html>


