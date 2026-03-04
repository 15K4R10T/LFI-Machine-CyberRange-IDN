<?php
function getDB() {
    $conn = new mysqli('127.0.0.1', 'labuser', 'labpass123', 'lablfi', 3306);
    if ($conn->connect_error) {
        die("<div style='background:#1a0a0a;color:#e63946;padding:16px;border-radius:8px;font-family:monospace;border:1px solid rgba(230,57,70,.3)'>
            Database connection failed: " . $conn->connect_error . "<br>
            <small style='color:#3d5168'>Tunggu 5-10 detik lalu refresh — MySQL mungkin masih starting.</small>
        </div>");
    }
    return $conn;
}
?>
