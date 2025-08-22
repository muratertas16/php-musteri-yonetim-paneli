<?php
// Veritabanı bağlantısı
$baglanti = new mysqli("localhost", "root", "", "musteri_takip");
if ($baglanti->connect_error) {
    die("Bağlantı hatası: " . $baglanti->connect_error);
}

// Sayfalama ayarları
$sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$sayfa = max(1, $sayfa); // Sayfa numarası en az 1 olmalı
$sayfada_goster = 5; // Her sayfada gösterilecek kayıt sayısı

// Toplam kayıt sayısını al
$toplam_kayit_sorgu = $baglanti->query("SELECT COUNT(*) as toplam FROM musteriler");
$toplam_kayit = $toplam_kayit_sorgu->fetch_assoc()['toplam'];
$toplam_sayfa = ceil($toplam_kayit / $sayfada_goster);

// Geçerli sayfa numarasını kontrol et
$sayfa = min($sayfa, $toplam_sayfa);

// Başlangıç pozisyonunu hesapla
$baslangic = ($sayfa - 1) * $sayfada_goster;

// Silme işlemi
if (isset($_GET['sil'])) {
    $id = intval($_GET['sil']);
    $baglanti->query("DELETE FROM musteriler WHERE id = $id");
    header("Location: index.php?silindi=1&sayfa=" . $sayfa);
    exit();
}

// Düzenleme için müşteri bilgilerini çek
$duzenlenecek = null;
if (isset($_GET['duzenle'])) {
    $id = intval($_GET['duzenle']);
    $sonuc = $baglanti->query("SELECT * FROM musteriler WHERE id = $id");
    if ($sonuc->num_rows > 0) {
        $duzenlenecek = $sonuc->fetch_assoc();
    }
}

// Güncelleme işlemi
if (isset($_POST["guncelle"])) {
    $id = intval($_POST["id"]);
    $ad = $_POST["ad"];
    $soyad = $_POST["soyad"];
    $tc_no = $_POST["tc_no"];
    $sehir = $_POST["sehir"];
    $ilce = $_POST["ilce"];

    // ✅ TC No kontrolü
    if (!preg_match('/^[0-9]{11}$/', $tc_no)) {
        header("Location: index.php?hata=tcno&sayfa=" . $sayfa);
        exit();
    }

    $sql = "UPDATE musteriler
            SET ad='$ad', soyad='$soyad', tc_no='$tc_no', sehir='$sehir', ilce='$ilce' 
            WHERE id=$id";

    if ($baglanti->query($sql) === TRUE) {
        header("Location: index.php?guncellendi=1&sayfa=" . $sayfa);
        exit();
    } else {
        header("Location: index.php?hata=1&sayfa=" . $sayfa);
        exit();
    }
}

// Ekleme işlemi
if (isset($_POST["ekle"])) {
    $ad = $_POST["ad"];
    $soyad = $_POST["soyad"];
    $tc_no = $_POST["tc_no"];
    $sehir = $_POST["sehir"];
    $ilce = $_POST["ilce"];

    // ✅ TC No kontrolü
    if (!preg_match('/^[0-9]{11}$/', $tc_no)) {
        header("Location: index.php?hata=tcno&sayfa=" . $sayfa);
        exit();
    }

    $sql = "INSERT INTO musteriler (ad, soyad, tc_no, sehir, ilce) 
            VALUES ('$ad', '$soyad', '$tc_no', '$sehir', '$ilce')";

    if ($baglanti->query($sql) === TRUE) {
        header("Location: index.php?eklendi=1&sayfa=1");
        exit();
    } else {
        header("Location: index.php?hata=1&sayfa=" . $sayfa);
        exit();
    }
}

// Müşteri listesini çek (sayfalama ile)
$sql_liste = "SELECT * FROM musteriler ORDER BY id DESC LIMIT $baslangic, $sayfada_goster";
$sonuc = $baglanti->query($sql_liste);
$kayit_sayisi = $sonuc->num_rows;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Müşteri Takip Paneli</title>
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

        .form-container {
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

        .btn-primary {
            background: #28a745;
            color: white;
        }

        .btn-secondary {
            background: #888;
            color: white;
        }

        .btn-secondary-1{
            background: #ff3333;
            color: white;
            text-decoration: none;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
        }

        .btn-danger {
            background: #ff3333;
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
            background: #28a745 !important;
            color: white !important;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }
        
        /* SİL butonu için kırmızı arkaplan */
        .btn-delete {
            background: #dc3545 !important;
            color: white !important;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            cursor: pointer;
        }
        
        .btn-edit:hover, .btn-delete:hover {
            opacity: 0.8;
            color: white !important;
        }
        
        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            text-align: center;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        a {
            text-decoration: none;
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
        
        /* Modal stilleri */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        
        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
        }
        
        .modal-btn {
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        
        .modal-btn-confirm {
            background: #dc3545;
            color: white;
        }
        
        .modal-btn-cancel {
            background: #6c757d;
            color: white;
        }
        
        .modal-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Müşteri İşlemleri</h2>
        
        <!-- Uyarı ve mesajlar -->
        <?php
        if (isset($_GET['eklendi'])) {
            echo '<div class="alert alert-success">✅ Müşteri başarıyla eklendi.</div>';
        }
        if (isset($_GET['silindi'])) {
            echo '<div class="alert alert-warning">🗑️ Müşteri silindi.</div>';
        }
        if (isset($_GET['guncellendi'])) {
            echo '<div class="alert alert-info">✏️ Müşteri güncellendi.</div>';
        }
        if (isset($_GET['hata']) && $_GET['hata'] == "tcno") {
            echo '<div class="alert alert-error">❌ TC No 11 haneli ve sadece rakamlardan oluşmalıdır.</div>';
        }
        if (isset($_GET['hata']) && $_GET['hata'] != "tcno") {
            echo '<div class="alert alert-error">❌ Hata oluştu.</div>';
        }
        ?>
        
        <div class="form-container">
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $duzenlenecek['id'] ?? ''; ?>">
                
                <div class="form-group">
                    <label for="ad">Ad:</label>
                    <input type="text" name="ad" id="ad" value="<?php echo $duzenlenecek['ad'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="soyad">Soyad:</label>
                    <input type="text" name="soyad" id="soyad" value="<?php echo $duzenlenecek['soyad'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="tc_no">TC No:</label>
                    <input type="text" 
                           name="tc_no" 
                           id="tc_no"
                           maxlength="11" 
                           minlength="11" 
                           pattern="[0-9]{11}" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                           value="<?php echo $duzenlenecek['tc_no'] ?? ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="sehir">Şehir:</label>
                    <input type="text" name="sehir" id="sehir" value="<?php echo $duzenlenecek['sehir'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="ilce">İlçe:</label>
                    <input type="text" name="ilce" id="ilce" value="<?php echo $duzenlenecek['ilce'] ?? ''; ?>" required>
                </div>
                
                <div class="buttons">
                    <?php if ($duzenlenecek): ?>
                        <button type="submit" name="guncelle" class="btn btn-primary">Müşteri Güncelle</button>
                        <a href="index.php?sayfa=<?php echo $sayfa; ?>" class="btn btn-secondary-1">İptal</a>
                    <?php else: ?>
                        <button type="submit" name="ekle" class="btn btn-primary">Müşteri Ekle</button>
                    <?php endif; ?>
                    <a href="anasayfa.php" class="btn btn-secondary">Ana Sayfa</a>
                </div>
            </form>
        </div>
        
        <div class="result-count">
            Toplam <?php echo $toplam_kayit; ?> kayıttan, <?php echo $baslangic + 1; ?> - <?php echo min($baslangic + $sayfada_goster, $toplam_kayit); ?> arası gösteriliyor.
        </div>
        
        <?php if ($toplam_kayit > 0): ?>
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
                        <a class="btn-edit" href='?duzenle=<?php echo $satir['id']; ?>&sayfa=<?php echo $sayfa; ?>'>Düzenle</a>
                        <button class="btn-delete" onclick="showConfirmModal(<?php echo $satir['id']; ?>, '<?php echo htmlspecialchars($satir['ad'] . ' ' . $satir['soyad']); ?>')">Sil</button>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        
        <!-- Sayfalama linkleri -->
        <div class="pagination">
            <?php if ($sayfa > 1): ?>
                <a href="?sayfa=1">İlk</a>
                <a href="?sayfa=<?php echo $sayfa - 1; ?>">Önceki</a>
            <?php endif; ?>
            
            <?php
            // Sayfa numaralarını göster
            $baslangic_sayfa = max(1, $sayfa - 2);
            $bitis_sayfa = min($toplam_sayfa, $sayfa + 2);
            
            for ($i = $baslangic_sayfa; $i <= $bitis_sayfa; $i++): 
                if ($i == $sayfa): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?sayfa=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif;
            endfor; 
            ?>
            
            <?php if ($sayfa < $toplam_sayfa): ?>
                <a href="?sayfa=<?php echo $sayfa + 1; ?>">Sonraki</a>
                <a href="?sayfa=<?php echo $toplam_sayfa; ?>">Son</a>
            <?php endif; ?>
        </div>
        
        <div class="pagination-info">
            Sayfa <?php echo $sayfa; ?> / <?php echo $toplam_sayfa; ?>
        </div>
        
        <?php else: ?>
        <div style="text-align: center; padding: 20px; color: #888;">
            Henüz hiç müşteri kaydı bulunmamaktadır.
        </div>
        <?php endif; ?>
    </div>

    <!-- Silme Onay Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-bottom: 20px;">Silmek İstediğinize Emin Misiniz?</h3>
            <p id="modalCustomerName" style="font-size: 18px; margin-bottom: 25px;"></p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="hideConfirmModal()">İptal</button>
                <button class="modal-btn modal-btn-confirm" onclick="confirmDelete()">Sil</button>
            </div>
        </div>
    </div>

    <script>
        let customerIdToDelete = null;
        let currentPage = <?php echo $sayfa; ?>;

        function showConfirmModal(id, customerName) {
            customerIdToDelete = id;
            document.getElementById('modalCustomerName').textContent = customerName + ' müşterisini silmek istediğinize emin misiniz?';
            document.getElementById('confirmModal').style.display = 'flex';
        }

        function hideConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
            customerIdToDelete = null;
        }

        function confirmDelete() {
            if (customerIdToDelete) {
                window.location.href = '?sil=' + customerIdToDelete + '&sayfa=' + currentPage;
            }
        }

        // ESC tuşuna basıldığında modalı kapat
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideConfirmModal();
            }
        });

        // Modal dışına tıklandığında modalı kapat
        document.getElementById('confirmModal').addEventListener('click', function(event) {
            if (event.target === this) {
                hideConfirmModal();
            }
        });
    </script>
</body>
</html>

<?php
$baglanti->close();
?>