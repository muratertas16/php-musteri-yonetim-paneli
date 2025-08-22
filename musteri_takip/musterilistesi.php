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

// Tüm müşterileri çek
$sql = "SELECT * FROM musteriler ORDER BY id DESC";
$sonuc = $baglanti->query($sql);
$kayit_sayisi = $sonuc->num_rows;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tüm Müşteriler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f7f7f7;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        h2 {
            color: #333;
            margin: 0;
            text-align: center;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #888;
            color: white;
        }
        
        .btn-secondary {
            background: #28a745;
            color: white;
        }
        
        .btn-danger {
            background: #ff3333;
            color: white;
        }
        
        .btn-print {
            background: #4da6ff;
            color: white;
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
            text-align: center;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .btn-logout {
            background: #ff3333;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                width: 100%;
            }
            
            table {
                width: 100%;
                font-size: 12px;
            }
            
            th, td {
                padding: 8px;
            }
        }
    </style>
    <script>
        function printPage() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="container">
        <!-- BAŞLIK İLK SIRAYA ALINDI -->
        <h2>Tüm Müşteriler</h2>
        

        <!-- BUTONLAR İKİNCİ SIRAYA ALINDI -->
        <div class="action-buttons no-print">

            <button onclick="printPage()" class="btn btn-print">Yazdır</button>
            <a href="anasayfa.php" class="btn btn-primary">Ana Sayfar</a>
        </div>
        
        <div class="result-count">
            <?php echo $kayit_sayisi; ?> kayıt listeleniyor.
        </div>
        
        <?php if ($kayit_sayisi > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>TC No</th>
                <th>Şehir</th>
                <th>İlçe</th>
            </tr>
            
            <?php while ($satir = $sonuc->fetch_assoc()): ?>
            <tr>
                <td><?php echo $satir['id']; ?></td>
                <td><?php echo htmlspecialchars($satir['ad']); ?></td>
                <td><?php echo htmlspecialchars($satir['soyad']); ?></td>
                <td><?php echo $satir['tc_no']; ?></td>
                <td><?php echo htmlspecialchars($satir['sehir']); ?></td>
                <td><?php echo htmlspecialchars($satir['ilce']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <div style="text-align: center; padding: 20px; color: #888;">
            Henüz hiç müşteri kaydı bulunmamaktadır.
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$baglanti->close();
?>