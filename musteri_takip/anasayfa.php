<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Yönetim Sistemi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        h1 {
            color: #333;
            margin-bottom: 40px;
            font-size: 2.5rem;
        }
        
        .button-container {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            justify-content: center; /* Ortaya hizalar */
            margin-top: 20px;
        }
        
        .big-button {
            background-color: #4da6ff;
            color: white;
            padding: 30px 20px;
            border: none;
            border-radius: 20px;
            font-size: 1.4rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 150px;
            width: 300px; 
            height: 150px; 
        }
        
        .big-button:hover {
            background-color: #3a8cdb;
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        footer {
            text-align: center;
            margin-top: 50px;
            padding: 20px;
            color: #666;
        }
        
        a {
            text-decoration: none;
            display: block; 
            height: 100%; 
        }
        
        @media (max-width: 768px) {
            .button-container {
                flex-direction: column;
                align-items: center;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .big-button {
                font-size: 1.2rem;
                min-height: 120px;
                height: 120px; 
                width: 100%;
                max-width: 400px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>MÜŞTERİ YÖNETİM SİSTEMİ</h1>
        
        <div class="button-container">
            <a href="index.php"><button class="big-button">MÜŞTERİ İŞLEMLERİ</button></a>
            <a href="sorgulama.php"><button class="big-button">MÜŞTERİ SORGULA</button></a>
            <a href="musterilistesi.php"><button class="big-button">MÜŞTERİ LİSTESİ</button></a>
        </div>
    </div>
    
    <footer>
        <p>© 2025 Murat Ertaş Tarafından Tasarlanmıştır - Tüm Hakları Saklıdır</p>
    </footer>
</body>
</html>
