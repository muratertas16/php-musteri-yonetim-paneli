<?php
// session_start();

// if (!isset($_SESSION['kullanici_id'])) {
//     header("Location: index.php");
//     exit();
// }

// Veritabanı bağlantısı
$baglanti = new mysqli("localhost", "root", "", "musteri_takip");
if ($baglanti->connect_error) {
    die("Bağlantı hatası: " . $baglanti->connect_error);
}

// Arama parametrelerini al
$arama_ad = isset($_GET['ad']) ? trim($_GET['ad']) : '';
$arama_soyad = isset($_GET['soyad']) ? trim($_GET['soyad']) : '';
$arama_tc = isset($_GET['tc']) ? trim($_GET['tc']) : '';
$arama_sehir = isset($_GET['sehir']) ? trim($_GET['sehir']) : '';
$arama_ilce = isset($_GET['ilce']) ? trim($_GET['ilce']) : '';

// Sayfalama ayarları
$sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$sayfa = max(1, $sayfa);
$sayfada_goster = 5;

// SQL sorgusunu oluştur
$sql = "SELECT * FROM musteriler WHERE 1=1";
$sql_count = "SELECT COUNT(*) as toplam FROM musteriler WHERE 1=1";

if (!empty($arama_ad)) {
    $sql .= " AND ad LIKE '%" . $baglanti->real_escape_string($arama_ad) . "%'";
    $sql_count .= " AND ad LIKE '%" . $baglanti->real_escape_string($arama_ad) . "%'";
}

if (!empty($arama_soyad)) {
    $sql .= " AND soyad LIKE '%" . $baglanti->real_escape_string($arama_soyad) . "%'";
    $sql_count .= " AND soyad LIKE '%" . $baglanti->real_escape_string($arama_soyad) . "%'";
}

if (!empty($arama_tc)) {
    $sql .= " AND tc_no LIKE '%" . $baglanti->real_escape_string($arama_tc) . "%'";
    $sql_count .= " AND tc_no LIKE '%" . $baglanti->real_escape_string($arama_tc) . "%'";
}

if (!empty($arama_sehir)) {
    $sql .= " AND sehir LIKE '%" . $baglanti->real_escape_string($arama_sehir) . "%'";
    $sql_count .= " AND sehir LIKE '%" . $baglanti->real_escape_string($arama_sehir) . "%'";
}

if (!empty($arama_ilce)) {
    $sql .= " AND ilce LIKE '%" . $baglanti->real_escape_string($arama_ilce) . "%'";
    $sql_count .= " AND ilce LIKE '%" . $baglanti->real_escape_string($arama_ilce) . "%'";
}

$sql .= " ORDER BY id DESC";

// Toplam kayıt sayısını al
$toplam_kayit_sorgu = $baglanti->query($sql_count);
$toplam_kayit = $toplam_kayit_sorgu->fetch_assoc()['toplam'];
$toplam_sayfa = ceil($toplam_kayit / $sayfada_goster);

// Geçerli sayfa numarasını kontrol et
$sayfa = min($sayfa, $toplam_sayfa);

// Başlangıç pozisyonunu hesapla
$baslangic = ($sayfa - 1) * $sayfada_goster;

// Sayfalama ekleyerek sorguyu çalıştır
$sql .= " LIMIT $baslangic, $sayfada_goster";
$sonuc = $baglanti->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Müşteri Sorgulama Paneli</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .search-form {
            background: #f2f2f2;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .form-group label {
            width: 100px;
            font-weight: bold;
            color: #000000;
        }

        .form-group input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-search {
            background: #4da6ff;
            color: white;
        }

        .btn-clear {
            background: #ff0000;
            color: white;
        }

        .btn-back {
            background: #888;
            color: white;
            text-decoration: none;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background: #4da6ff;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }
        
        .no-results {
            text-align: center;
            padding: 20px;
            color: #888;
            font-style: italic;
        }
        
        .result-count {
            margin: 15px 0;
            font-weight: bold;
            color: #333;
        }
        
        .action-links {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        /* DÜZENLE butonu için yeşil arkaplan */
        .btn-edit {
            background: #28a745;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }
        
        /* SİL butonu için kırmızı arkaplan */
        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }
        
        .btn-edit:hover, .btn-delete:hover {
            opacity: 0.8;
            color: white;
        }
        
        /* Sayfalama stilleri */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination a:hover {
            background: #4da6ff;
            color: white;
        }
        
        .pagination .active {
            background: #4da6ff;
            color: white;
            border-color: #4da6ff;
        }
        
        .pagination-info {
            text-align: center;
            margin-top: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Müşteri Sorgulama Paneli</h2>
        
        <div class="search-form">
            <form method="GET" action="">
                <input type="hidden" name="sayfa" value="1">
                
                <div class="form-group">
                    <label for="ad">Ad:</label>
                    <input type="text" id="ad" name="ad" value="<?php echo htmlspecialchars($arama_ad); ?>">
                </div>
                
                <div class="form-group">
                    <label for="soyad">Soyad:</label>
                    <input type="text" id="soyad" name="soyad" value="<?php echo htmlspecialchars($arama_soyad); ?>">
                </div>
                
                <div class="form-group">
                    <label for="tc">TC No:</label>
                    <input type="text" id="tc" name="tc" value="<?php echo htmlspecialchars($arama_tc); ?>" 
                           maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                
                <div class="form-group">
                    <label for="sehir">Şehir:</label>
                    <input type="text" id="sehir" name="sehir" value="<?php echo htmlspecialchars($arama_sehir); ?>">
                </div>
                
                <div class="form-group">
                    <label for="ilce">İlçe:</label>
                    <input type="text" id="ilce" name="ilce" value="<?php echo htmlspecialchars($arama_ilce); ?>">
                </div>
                
                <div class="buttons">
                    <button type="submit" class="btn btn-search">Sorgula</button>
                    <button type="button" class="btn btn-clear" onclick="clearForm()">Temizle</button>
                    <a href="anasayfa.php" class="btn-back">Ana Sayfa</a>
                </div>
            </form>
        </div>
        
        <?php if (!empty($arama_ad) || !empty($arama_soyad) || !empty($arama_tc) || !empty($arama_sehir) || !empty($arama_ilce)): ?>
        <div class="result-count">
            Toplam <?php echo $toplam_kayit; ?> kayıttan, <?php echo $baslangic + 1; ?> - <?php echo min($baslangic + $sayfada_goster, $toplam_kayit); ?> arası gösteriliyor.
        </div>
        
        <?php if ($sonuc->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>TC No</th>
                <th>Şehir</th>
                <th>İlçe</th>
                <th>İşlemler</th>
            </tr>
            
            <?php while ($satir = $sonuc->fetch_assoc()): ?>
            <tr>
                <td><?php echo $satir['id']; ?></td>
                <td><?php echo htmlspecialchars($satir['ad']); ?></td>
                <td><?php echo htmlspecialchars($satir['soyad']); ?></td>
                <td><?php echo $satir['tc_no']; ?></td>
                <td><?php echo htmlspecialchars($satir['sehir']); ?></td>
                <td><?php echo htmlspecialchars($satir['ilce']); ?></td>
                <td>
                    <div class="action-links">
                        <a class="btn-edit" href="index.php?duzenle=<?php echo $satir['id']; ?>">Düzenle</a>
                        <a class="btn-delete" href="index.php?sil=<?php echo $satir['id']; ?>" onclick="return confirm('Silmek istediğinize emin misiniz?');">Sil</a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        
        <!-- Sayfalama linkleri -->
        <?php if ($toplam_sayfa > 1): ?>
        <div class="pagination">
            <?php if ($sayfa > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['sayfa' => 1])); ?>">İlk</a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['sayfa' => $sayfa - 1])); ?>">Önceki</a>
            <?php endif; ?>
            
            <?php
            // Sayfa numaralarını göster
            $baslangic_sayfa = max(1, $sayfa - 2);
            $bitis_sayfa = min($toplam_sayfa, $sayfa + 2);
            
            for ($i = $baslangic_sayfa; $i <= $bitis_sayfa; $i++): 
                if ($i == $sayfa): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['sayfa' => $i])); ?>"><?php echo $i; ?></a>
                <?php endif;
            endfor; 
            ?>
            
            <?php if ($sayfa < $toplam_sayfa): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['sayfa' => $sayfa + 1])); ?>">Sonraki</a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['sayfa' => $toplam_sayfa])); ?>">Son</a>
            <?php endif; ?>
        </div>
        
        <div class="pagination-info">
            Sayfa <?php echo $sayfa; ?> / <?php echo $toplam_sayfa; ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="no-results">
            Arama kriterlerinize uygun müşteri bulunamadı.
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        function clearForm() {
            document.getElementById('ad').value = '';
            document.getElementById('soyad').value = '';
            document.getElementById('tc').value = '';
            document.getElementById('sehir').value = '';
            document.getElementById('ilce').value = '';
            document.querySelector('form').submit();
        }
    </script>
</body>
</html>

<?php
$baglanti->close();
?>