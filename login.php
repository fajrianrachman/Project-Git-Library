<?php
require_once 'koneksi.php';

$error = '';

if (isset($_POST['login'])) {
    $identifier = trim($_POST['identifier']);
    $password   = trim($_POST['password']);
    $role       = $_POST['role'];

    if ($role === 'member') {
        $user = $memberObj->login($identifier, $password);
        if ($user) {
            $_SESSION['role']      = 'member';
            $_SESSION['user_id']   = $user['MemberID'];
            $_SESSION['user_name'] = $user['Name'];
            header("Location: member_dashboard.php");
            exit;
        } else {
            $error = "Email atau Password Member salah!";
        }
    } else {
        $user = $librarianObj->login($identifier, $password);
        if ($user) {
            $_SESSION['role']         = 'librarian';
            $_SESSION['user_id']      = $user['LibrarianID'];
            $_SESSION['employee_id']  = $user['EmployeeID'];
            $_SESSION['user_name']    = $user['Name'];
            header("Location: librarian_dashboard.php");
            exit;
        } else {
            $error = "Employee ID atau Password Librarian salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { font-family: 'Segoe UI', Arial; background: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 360px; }
        h2 { text-align: center; color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .alert { background: #e74c3c; color: white; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
<div class="card">
    <h2>Welcome to The Library Of The Elation</h2>
    <?php if($error): ?><div class="alert"><?= $error; ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Login Sebagai:</label>
            <select name="role" id="roleSelect" onchange="updatePlaceholder()">
                <option value="member">Member (Anggota)</option>
                <option value="librarian">Librarian (Petugas)</option>
            </select>
        </div>
        <div class="form-group">
            <label id="idLabel">Email Member:</label>
            <input type="text" name="identifier" id="idInput" placeholder="andi.pratama@gmail.com" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" placeholder="123456" required>
        </div>
        <button type="submit" name="login">Masuk</button>
    </form>
    <p style="text-align:center; font-size:13px; margin-top:15px;"><a href="register.php">Daftar Member Baru</a></p>
</div>
<script>
function updatePlaceholder() {
    var role = document.getElementById('roleSelect').value;
    document.getElementById('idLabel').innerText = role === 'member' ? 'Email Member:' : 'Employee ID:';
    document.getElementById('idInput').placeholder = role === 'member' ? 'andi.pratama@gmail.com' : 'LIB001';
}
</script>
</body>
</html>