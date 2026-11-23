<?php
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    $tc = isset($_POST['tc']) ? trim($_POST['tc']) : '';
    if (empty($tc)) { echo json_encode(['status'=>'error','message'=>'Lutfen TC Kimlik No giriniz.']); exit; }
    $apiUrl = "https://api.ajaxsystems.fun/aile.php?tc=" . urlencode($tc);
    $options = ["http"=>["method"=>"GET","header"=>"User-Agent: Mozilla/5.0\r\n"],"ssl"=>["verify_peer"=>false,"verify_peer_name"=>false]];
    $response = @file_get_contents($apiUrl, false, stream_context_create($options));
    if (!$response) { echo json_encode(['status'=>'error','message'=>'API baglanti hatasi.']); exit; }
    $data = json_decode($response, true);
    if (!$data || !isset($data['Status']) || $data['Status'] !== true) { echo json_encode(['status'=>'error','message'=>'Kayit bulunamadi.']); exit; }
    echo json_encode(['status'=>'success','data'=>$data]); exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Aile Sorgulama - Wyurs VIP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { 
            background-color: #050505; color: #fff; font-family: 'Inter', sans-serif; margin: 0; padding: 0; 
            min-height: 100vh; background-image: radial-gradient(circle at 50% 50%, #111 0%, #000 100%); 
            display: flex; justify-content: center; align-items: flex-start; padding-top: 50px; overflow: hidden; 
        }
        .space-bg { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; overflow: hidden; }
        
        .star { position: absolute; background: #fff; border-radius: 50%; animation: twinkle infinite ease-in-out; box-shadow: 0 0 8px #fff, 0 0 15px #fff; }
        @keyframes twinkle { 0%, 100% { opacity: 0.1; transform: scale(0.8); } 50% { opacity: 1; transform: scale(1.2); } }

        /* İnce, küçük ve parlamasız meteor */
        .meteor { 
            position: absolute; top: 50%; right: 0; width: 80px; height: 1px; 
            background: linear-gradient(90deg, #ffcc00, transparent); 
            animation: meteor linear infinite; opacity: 0; 
        }
        .meteor::before { 
            content: ''; position: absolute; top: 50%; right: 0; width: 2px; height: 2px; 
            background: #fff; border-radius: 50%; transform: translateY(-50%); 
            /* Parlama (box-shadow) tamamen kaldirildi */
        }
        @keyframes meteor { 
            0% { transform: rotate(315deg) translateX(0); opacity: 1; } 
            70% { opacity: 1; } 
            100% { transform: rotate(315deg) translateX(-1000px); opacity: 0; } 
        }

        .container { width: 100%; max-width: 900px; padding: 20px; position: relative; z-index: 1; }
        .query-panel { background: #0f0f0f; border: 1px solid #222; border-radius: 12px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .header-title { color: #ffcc00; font-size: 20px; font-weight: 700; margin-bottom: 5px; display: flex; align-items: center; gap: 10px; }
        .header-desc { color: #777; font-size: 13px; margin-bottom: 25px; border-bottom: 1px solid #222; padding-bottom: 15px; }
        .form-group { margin-bottom: 20px; }
        .form-control { width: 100%; background: #1a1a1a; border: 1px solid #333; color: #fff; padding: 12px 15px; border-radius: 8px; font-size: 14px; outline: none; transition: 0.3s; }
        .form-control:focus { border-color: #ffcc00; box-shadow: 0 0 10px rgba(255,204,0,0.2); }
        .form-control::placeholder { color: #555; }
        .btn-group { display: flex; gap: 15px; margin-top: 25px; }
        .btn { padding: 12px 25px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; justify-content: center; flex: 1; transition: 0.3s; }
        .btn-primary { background: #00ff88; color: #000; }
        .btn-primary:hover { background: #00cc6a; box-shadow: 0 0 15px rgba(0,255,136,0.3); }
        .btn-danger { background: #ff3366; color: #fff; }
        .btn-danger:hover { background: #cc002a; box-shadow: 0 0 15px rgba(255,51,102,0.3); }
        .result-area { margin-top: 30px; display: none; }
        .result-table { width: 100%; border-collapse: collapse; background: #111; border-radius: 8px; overflow: hidden; font-size: 13px; }
        .result-table th, .result-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #222; }
        .result-table th { background: #1a1a1a; color: #ffcc00; font-weight: 600; white-space: nowrap; }
        .result-table td { color: #ccc; }
        .loading { text-align: center; color: #ffcc00; padding: 20px; display: none; }
    </style>
</head>
<body>
    <div class="space-bg" id="space-bg"></div>
    
    <div class="container">
        <div class="query-panel">
            <div class="header-title"><i class="fa-solid fa-users"></i> Aile Sorgulama</div>
            <div class="header-desc">Ad, Soyad, Il ve Ilce bilgilerini girerek arama yapabilirsiniz. Maksimum 1000 kayit listelenir; sayfada 500 sonuc gosterilir.</div>
            <div class="form-group"><input type="text" id="tcInput" class="form-control" placeholder="TC Kimlik No *" maxlength="11"></div>
            <div class="btn-group">
                <button class="btn btn-primary" id="btnSorgula"><i class="fa-solid fa-magnifying-glass"></i> Sorgula</button>
                <button class="btn btn-danger" id="btnSifirla"><i class="fa-solid fa-rotate-right"></i> Sifirla</button>
            </div>
            <div class="loading" id="loading"><i class="fa-solid fa-spinner fa-spin"></i> Sorgulaniyor...</div>
            <div class="result-area" id="resultArea">
                <table class="result-table">
                    <thead><tr><th>Yakinlik</th><th>TC</th><th>Ad</th><th>Soyad</th><th>Dogum T.</th><th>Adres</th></tr></thead>
                    <tbody id="resultBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const spaceBg = document.getElementById('space-bg');
            
            for (let i = 0; i < 100; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                const size = Math.random() * 2 + 1;
                star.style.width = size + 'px';
                star.style.height = size + 'px';
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.animationDuration = (Math.random() * 3 + 2) + 's';
                star.style.animationDelay = Math.random() * 3 + 's';
                spaceBg.appendChild(star);
            }

            for (let i = 0; i < 3; i++) {
                const meteor = document.createElement('div');
                meteor.className = 'meteor';
                meteor.style.top = (Math.random() * 60) + '%';
                meteor.style.left = (Math.random() * 60 + 40) + '%';
                meteor.style.animationDuration = (Math.random() * 2 + 2) + 's';
                meteor.style.animationDelay = (Math.random() * 6) + 's';
                spaceBg.appendChild(meteor);
            }

            const btnSorgula = document.getElementById('btnSorgula');
            const btnSifirla = document.getElementById('btnSifirla');
            const tcInput = document.getElementById('tcInput');
            const loading = document.getElementById('loading');
            const resultArea = document.getElementById('resultArea');
            const resultBody = document.getElementById('resultBody');

            btnSorgula.addEventListener('click', () => {
                const tc = tcInput.value.trim();
                if (tc.length !== 11) { alert('Lutfen 11 haneli TC Kimlik Numarasini giriniz.'); return; }
                loading.style.display = 'block';
                resultArea.style.display = 'none';
                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: 'tc=' + encodeURIComponent(tc)
                })
                .then(r => r.json())
                .then(res => {
                    loading.style.display = 'none';
                    resultArea.style.display = 'block';
                    if (res.status === 'success') {
                        let html = '';
                        res.data.data.forEach(item => {
                            html += '<tr>' +
                                '<td>' + item.YAKINLIK + '</td><td>' + item.TC + '</td><td>' + item.AD + '</td>' +
                                '<td>' + item.SOYAD + '</td><td>' + item.DOGUMTARIHI + '</td><td>' + item.ADRES + '</td>' +
                            '</tr>';
                        });
                        resultBody.innerHTML = html;
                    } else {
                        resultBody.innerHTML = '<tr><td colspan="6" style="color:#ff4444; text-align:center;">' + res.message + '</td></tr>';
                    }
                })
                .catch(err => {
                    loading.style.display = 'none'; resultArea.style.display = 'block';
                    resultBody.innerHTML = '<tr><td colspan="6" style="color:#ff4444; text-align:center;">Baglanti Hatasi: ' + err.message + '</td></tr>';
                });
            });

            btnSifirla.addEventListener('click', () => { tcInput.value = ''; resultArea.style.display = 'none'; resultBody.innerHTML = ''; });
            tcInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') btnSorgula.click(); });
        });
    </script>
</body>
</html>