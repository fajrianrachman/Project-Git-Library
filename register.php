<?php
require_once 'koneksi.php';
$msg = '';

if (isset($_POST['register'])) {
    $res = $memberObj->register($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['password']);
    if ($res) {
        $msg = "<div class='alert success'>Pendaftaran berhasil! Silakan <a href='login.php'>Login</a></div>";
    } else {
        $msg = "<div class='alert err'>Email sudah terdaftar atau terjadi kesalahan!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Member</title>
    <style>
        body { font-family: Arial; background: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 25px; border-radius: 8px; width: 380px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 12px; }
        input, textarea { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #2ecc71; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .alert { padding: 10px; font-size: 13px; border-radius: 4px; text-align: center; margin-bottom: 10px; }
        .err { background: #e74c3c; color: white; } .success { background: #2ecc71; color: white; }
    </style>
</head>
<body>
<div class="card">
    <h2 style="text-align:center;">Daftar Member Baru</h2>
    <?= $msg; ?>
    <form method="POST">
        <div class="form-group"><label>Nama Lengkap:</label><input type="text" name="name" required></div>
        <div class="form-group"><label>Email:</label><input type="email" name="email" required></div>
        <div class="form-group"><label>No. HP:</label><input type="text" name="phone" required></div>
        <div class="form-group"><label>Alamat:</label><textarea name="address" rows="2" required></textarea></div>
        <div class="form-group"><label>Password:</label><input type="password" name="password" required></div>
        <button type="submit" name="register">Daftar Sekarang</button>
    </form>
    <p style="text-align:center; font-size: 13px;"><a href="login.php">Kembali ke Login</a></p>
</div>
</body>
</html>