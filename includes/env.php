<?php

// .env dosyasını yüklemek için kullanılan fonksiyon
function loadEnv($path) {

    // Dosya mevcut değilse işlemi durdurur
    if (!file_exists($path)) {
        return;
    }

    // Dosyadaki satırları okur
    $lines = file(
        $path,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    // Her satırı döngüye alır
    foreach ($lines as $line) {

        // Satır yorum satırıysa geçer
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Satırı "=" karakterine göre ayırır
        list($key, $value) = explode('=', $line, 2);

        // Anahtar kısmındaki boşlukları temizler
        $key = trim($key);

        // Değer kısmındaki boşlukları temizler
        $value = trim($value);

        // Veriyi $_ENV dizisine kaydeder
        $_ENV[$key] = $value;
    }
}