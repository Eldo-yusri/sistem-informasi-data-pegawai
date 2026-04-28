<?php

// GANTI 'operator123' DENGAN PASSWORD YANG ANDA INGINKAN UNTUK PENGGUNA BARU
$password_untuk_login = 'operator123'; 

// Menghasilkan hash yang aman dari password di atas
$hash_yang_aman = password_hash($password_untuk_login, PASSWORD_DEFAULT);

echo "<h1>Gunakan Hash Ini di Database Anda</h1>";
echo "<p><strong>Password Plaintext:</strong> " . htmlspecialchars($password_untuk_login) . "</p>";
echo "<p><strong>Hash untuk disalin ke phpMyAdmin:</strong></p>";
// Menggunakan textarea agar mudah disalin
echo '<textarea rows="4" cols="80" readonly style="font-size: 16px; padding: 10px;">' . htmlspecialchars($hash_yang_aman) . '</textarea>';
echo "<hr>";
echo "<h3>Langkah Selanjutnya:</h3>";
echo "<ol>";
echo "<li>Salin seluruh teks hash di atas (yang ada di dalam kotak).</li>";
echo "<li>Buka phpMyAdmin dan masuk ke tabel 'pengguna'.</li>";
echo "<li>Edit baris pengguna yang ingin Anda gunakan untuk login.</li>";
echo "<li>Tempelkan hash ini ke dalam kolom 'password'.</li>";
echo "<li>Simpan perubahan.</li>";
echo "<li>Coba login kembali dengan username Anda dan password: <strong>" . htmlspecialchars($password_untuk_login) . "</strong></li>";
echo "</ol>";

?>