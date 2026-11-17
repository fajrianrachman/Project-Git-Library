<?php
require_once 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit;
}

$librarian_id = $_SESSION['user_id'];
$msg = '';

if (isset($_POST['add_book'])) {
    $coverName = null;

    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['cover_image']['tmp_name'];
        $fileName      = $_FILES['cover_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $coverName     = time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/uploads/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            move_uploaded_file($fileTmpPath, $uploadFileDir . $coverName);
        }
    }

    $res = $bookObj->add(
        $_POST['isbn'], 
        $_POST['title'], 
        (int)$_POST['category_id'], 
        $_POST['author'], 
        $_POST['publisher'], 
        (int)$_POST['stock'],
        $coverName,
        $_POST['summary'] ?? null
    );

    if ($res) {
        $msg = "<div class='alert success'>Buku berhasil ditambahkan beserta ringkasannya!</div>";
    } else {
        $msg = "<div class='alert err'>Gagal menambahkan buku. ISBN mungkin sudah ada.</div>";
    }
}

if (isset($_POST['change_cover'])) {
    $bookId = (int)$_POST['book_id'];

    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['cover_image']['tmp_name'];
        $fileName      = $_FILES['cover_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $coverName     = time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/uploads/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            if (move_uploaded_file($fileTmpPath, $uploadFileDir . $coverName)) {
                if ($bookObj->updateCover($bookId, $coverName)) {
                    $msg = "<div class='alert success'>Foto sampul buku berhasil diperbarui!</div>";
                } else {
                    $msg = "<div class='alert err'>Gagal memperbarui database sampul buku.</div>";
                }
            } else {
                $msg = "<div class='alert err'>Gagal mengunggah berkas gambar ke folder uploads.</div>";
            }
        } else {
            $msg = "<div class='alert err'>Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau WEBP.</div>";
        }
    } else {
        $msg = "<div class='alert err'>Silakan pilih berkas gambar terlebih dahulu.</div>";
    }
}

if (isset($_POST['edit_book'])) {
    $bookId = (int)$_POST['book_id'];
    $coverName = null;

    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['cover_image']['tmp_name'];
        $fileName      = $_FILES['cover_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $coverName     = time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/uploads/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            move_uploaded_file($fileTmpPath, $uploadFileDir . $coverName);
        }
    }

    $res = $bookObj->update(
        $bookId,
        $_POST['isbn'], 
        $_POST['title'], 
        (int)$_POST['category_id'], 
        $_POST['author'], 
        $_POST['publisher'], 
        (int)$_POST['stock'],
        $coverName,
        $_POST['summary'] ?? null
    );

    if ($res) {
        $msg = "<div class='alert success'>Data buku & ringkasan berhasil diperbarui!</div>";
    } else {
        $msg = "<div class='alert err'>Gagal memperbarui data buku.</div>";
    }
}

if (isset($_POST['add_category'])) {
    if ($bookObj->addCategory($_POST['category_name'])) {
        $msg = "<div class='alert success'>Kategori berhasil ditambahkan!</div>";
    }
}

if (isset($_POST['approve_borrow'])) {
    if ($borrowObj->approveBorrow((int)$_POST['borrow_id'], $librarian_id)) {
        $msg = "<div class='alert success'>Pengajuan peminjaman berhasil di-ACC!</div>";
    }
}

if (isset($_POST['reject_borrow'])) {
    if ($borrowObj->rejectBorrow((int)$_POST['borrow_id'], (int)$_POST['book_id'])) {
        $msg = "<div class='alert success'>Pengajuan peminjaman ditolak dan stok dikembalikan!</div>";
    }
}

if (isset($_POST['update_member_status'])) {
    if ($memberObj->updateStatus((int)$_POST['member_id'], $_POST['status'])) {
        $msg = "<div class='alert success'>Status anggota berhasil diperbarui!</div>";
    }
}

// Fetch Master Data via Objek
$search_lib      = $_GET['search_lib'] ?? '';
$pendingRequests = $borrowObj->getPendingRequests();
$activeBorrows   = $borrowObj->getActiveBorrows();
$books           = $bookObj->getAll($search_lib);
$categories      = $bookObj->getAllCategories();
$members         = $memberObj->getAll();

// Deteksi Pustakawan memilih Ganti Cover atau Edit
$editBook = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    foreach ($books as $b) {
        if ($b['BookID'] === $editId) {
            $editBook = $b;
            break;
        }
    }
}

$coverBook = null;
if (isset($_GET['cover_id'])) {
    $coverId = (int)$_GET['cover_id'];
    foreach ($books as $b) {
        if ($b['BookID'] === $coverId) {
            $coverBook = $b;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Librarian Dashboard - Sistem Perpustakaan</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #eef2f5; margin: 0; padding: 20px; }
        .navbar { background: #2c3e50; color: white; padding: 15px 20px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; }
        .container { margin-top: 20px; }
        .card { background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; font-size: 13px; text-align: left; }
        th { background: #34495e; color: white; }
        .btn { padding: 6px 12px; background: #2ecc71; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; text-decoration: none; display: inline-block; }
        .btn-danger { background: #e74c3c; } .btn-blue { background: #3498db; } .btn-warning { background: #f39c12; } .btn-purple { background: #9b59b6; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 13px; }
        .err { background: #e74c3c; color: white; } .success { background: #2ecc71; color: white; }
        .form-group { margin-bottom: 10px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .form-group input, .form-group select, .form-group textarea { padding: 8px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; }
        .thumb-img { width: 45px; height: 60px; object-fit: cover; border-radius: 3px; }

        /* Modal Overlay */
        .modal-overlay { position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); display: flex; justify-content: center; align-items: center; }
        .modal-box { background-color: #fff; padding: 25px; border-radius: 8px; width: 80%; max-width: 550px; position: relative; box-shadow: 0 5px 25px rgba(0,0,0,0.4); }
        .close-btn { position: absolute; right: 20px; top: 15px; font-size: 26px; font-weight: bold; text-decoration: none; color: #888; }
        .close-btn:hover { color: #000; }
        .form-field { display: flex; flex-direction: column; gap: 10px; margin-top: 15px; }
        .form-field label { font-size: 12px; font-weight: bold; color: #34495e; }
        .form-field input, .form-field select, .form-field textarea { padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; font-family: inherit; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>Dashboard Pustakawan</h2>
    <div>Pustakawan ID: <strong><?= $_SESSION['employee_id']; ?></strong> | <a href="logout.php" style="color:#ff7675; text-decoration:none;">Logout</a></div>
</div>

<br>
<?= $msg; ?>

<!-- 1. MODAL KHUSUS GANTI SAMPUL GAMBAR -->
<?php if ($coverBook): ?>
<div class="modal-overlay">
    <div class="modal-box">
        <a href="librarian_dashboard.php" class="close-btn">&times;</a>
        <h3>Unggah Sampul Baru: <?= htmlspecialchars($coverBook['Title']); ?></h3>
        <form method="POST" enctype="multipart/form-data" class="form-field">
            <input type="hidden" name="book_id" value="<?= $coverBook['BookID']; ?>">
            
            <div style="text-align:center; margin-bottom:10px;">
                <?php 
                    $currCover = !empty($coverBook['CoverImage']) && file_exists(__DIR__ . '/uploads/' . $coverBook['CoverImage'])
                        ? 'uploads/' . $coverBook['CoverImage']
                        : 'https://via.placeholder.com/150x220?text=No+Cover';
                ?>
                <p style="font-size:12px; color:#7f8c8d; margin-bottom:5px;">Sampul Saat Ini:</p>
                <img src="<?= $currCover; ?>" style="width:100px; height:140px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">
            </div>

            <label>Pilih File Gambar Baru (JPG, PNG, WEBP):</label>
            <input type="file" name="cover_image" accept="image/*" required>
            
            <div style="margin-top:15px; display:flex; gap:10px;">
                <button type="submit" name="change_cover" class="btn btn-purple" style="flex:1;">Unggah & Simpan Sampul</button>
                <a href="librarian_dashboard.php" class="btn btn-danger" style="text-align:center;">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- 2. MODAL EDIT DATA BUKU & RINGKASAN -->
<?php if ($editBook): ?>
<div class="modal-overlay">
    <div class="modal-box">
        <a href="librarian_dashboard.php" class="close-btn">&times;</a>
        <h3>Edit Data Buku #<?= $editBook['BookID']; ?></h3>
        <form method="POST" enctype="multipart/form-data" class="form-field">
            <input type="hidden" name="book_id" value="<?= $editBook['BookID']; ?>">
            
            <label>ISBN:</label>
            <input type="text" name="isbn" value="<?= htmlspecialchars($editBook['ISBN']); ?>" required>
            
            <label>Judul Buku:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($editBook['Title']); ?>" required>
            
            <label>Kategori:</label>
            <select name="category_id" required>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['CategoryID']; ?>" <?= $c['CategoryID'] == $editBook['CategoryID'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($c['CategoryName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label>Penulis:</label>
            <input type="text" name="author" value="<?= htmlspecialchars($editBook['Author']); ?>" required>
            
            <label>Penerbit:</label>
            <input type="text" name="publisher" value="<?= htmlspecialchars($editBook['Publisher']); ?>" required>
            
            <label>Stok Buku:</label>
            <input type="number" name="stock" min="0" value="<?= $editBook['Stock']; ?>" required>

            <label>Ringkasan Cerita Buku (Short Summary):</label>
            <textarea name="summary" rows="4" placeholder="Tuliskan ringkasan cerita buku..."><?= htmlspecialchars($editBook['Summary'] ?? ''); ?></textarea>
            
            <label>Ganti Sampul Gambar (Opsional):</label>
            <input type="file" name="cover_image" accept="image/*">
            
            <div style="margin-top:15px; display:flex; gap:10px;">
                <button type="submit" name="edit_book" class="btn btn-blue" style="flex:1;">Simpan Perubahan</button>
                <a href="librarian_dashboard.php" class="btn btn-danger" style="text-align:center;">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="container">
    
    <!-- ACC Pengajuan Pinjam -->
    <div class="card">
        <h3>1. Persetujuan Peminjaman Buku (Pending Request)</h3>
        <table>
            <thead>
                <tr><th>ID Pinjam</th><th>Nama Member</th><th>Judul Buku</th><th>Tgl Pengajuan</th><th>Jatuh Tempo</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php if(empty($pendingRequests)): ?>
                    <tr><td colspan="6" style="text-align:center; color:#888;">Tidak ada pengajuan pending.</td></tr>
                <?php endif; ?>
                <?php foreach($pendingRequests as $pr): ?>
                <tr>
                    <td>#<?= $pr['BorrowID']; ?></td>
                    <td><?= htmlspecialchars($pr['MemberName']); ?></td>
                    <td><?= htmlspecialchars($pr['Title']); ?></td>
                    <td><?= $pr['IssueDate']; ?></td>
                    <td><?= $pr['DueDate']; ?></td>
                    <td>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="borrow_id" value="<?= $pr['BorrowID']; ?>">
                            <button type="submit" name="approve_borrow" class="btn">ACC / Setujui</button>
                        </form>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="borrow_id" value="<?= $pr['BorrowID']; ?>">
                            <input type="hidden" name="book_id" value="<?= $pr['BookID']; ?>">
                            <button type="submit" name="reject_borrow" class="btn btn-danger">Tolak</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Form Tambah Buku Baru -->
    <div class="card">
        <h3>2. Tambah Koleksi Buku Baru</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="isbn" placeholder="ISBN (misal: ISBN012)" required>
                <input type="text" name="title" placeholder="Judul Buku" required>
                <select name="category_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach($categories as $c): ?>
                        <option value="<?= $c['CategoryID']; ?>"><?= htmlspecialchars($c['CategoryName']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="author" placeholder="Penulis" required>
                <input type="text" name="publisher" placeholder="Penerbit" required>
                <input type="number" name="stock" placeholder="Stok" min="1" value="1" style="width:70px;" required>
            </div>
            <div class="form-group" style="width:100%;">
                <textarea name="summary" placeholder="Ringkasan Cerita Buku (Short Summary)..." style="width:100%; height:60px; resize:vertical; font-family:inherit;"></textarea>
            </div>
            <div class="form-group">
                <label style="font-size:12px;">Upload Sampul Buku (JPG/PNG):</label>
                <input type="file" name="cover_image" accept="image/*">
                <button type="submit" name="add_book" class="btn btn-blue">Simpan Buku</button>
            </div>
        </form>

        <hr style="margin:20px 0; border:0; border-top:1px solid #eee;">

        <form method="POST" class="form-group">
            <input type="text" name="category_name" placeholder="Nama Kategori Baru" required>
            <button type="submit" name="add_category" class="btn btn-blue">+ Tambah Kategori</button>
        </form>
    </div>

    <!-- Katalog Buku & Sampul -->
    <div class="card">
        <h3>3. Katalog Buku Perpustakaan</h3>
        <form method="GET" style="margin-bottom:15px; display:flex; gap:10px;">
            <input type="text" name="search_lib" placeholder="Cari judul, ISBN, penulis..." value="<?= htmlspecialchars($search_lib); ?>" style="padding:8px; flex:1;">
            <button type="submit" class="btn btn-blue">Cari</button>
        </form>

        <table>
            <thead>
                <tr><th>Sampul</th><th>ISBN</th><th>Judul Buku</th><th>Kategori</th><th>Penulis</th><th>Penerbit</th><th>Stok</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php foreach($books as $b): 
                    $coverSrc = !empty($b['CoverImage']) && file_exists(__DIR__ . '/uploads/' . $b['CoverImage'])
                        ? 'uploads/' . $b['CoverImage']
                        : 'https://via.placeholder.com/150x220?text=No+Cover';
                ?>
                <tr>
                    <td><img src="<?= $coverSrc; ?>" class="thumb-img" alt="Cover"></td>
                    <td><?= htmlspecialchars($b['ISBN']); ?></td>
                    <td><strong><?= htmlspecialchars($b['Title']); ?></strong></td>
                    <td><?= htmlspecialchars($b['CategoryName'] ?? 'Uncategorized'); ?></td>
                    <td><?= htmlspecialchars($b['Author']); ?></td>
                    <td><?= htmlspecialchars($b['Publisher']); ?></td>
                    <td><?= $b['Stock']; ?></td>
                    <td>
                        <!-- Tombol Unggah Sampul -->
                        <a href="librarian_dashboard.php?cover_id=<?= $b['BookID']; ?>" class="btn btn-purple">Ganti Sampul</a>
                        <!-- Tombol Edit Buku & Ringkasan -->
                        <a href="librarian_dashboard.php?edit_id=<?= $b['BookID']; ?>" class="btn btn-warning">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Kelola Anggota -->
    <div class="card">
        <h3>4. Kelola Anggota / Member</h3>
        <table>
            <thead>
                <tr><th>ID</th><th>Nama</th><th>Email</th><th>No HP</th><th>Buku Dipinjam</th><th>Status</th><th>Ubah Status</th></tr>
            </thead>
            <tbody>
                <?php foreach($members as $m): ?>
                <tr>
                    <td>#<?= $m['MemberID']; ?></td>
                    <td><?= htmlspecialchars($m['Name']); ?></td>
                    <td><?= htmlspecialchars($m['Email']); ?></td>
                    <td><?= htmlspecialchars($m['Phone']); ?></td>
                    <td><?= $m['CurrentBorrowedCount']; ?> / 5</td>
                    <td><strong><?= $m['Status']; ?></strong></td>
                    <td>
                        <form method="POST" style="display:flex; gap:5px;">
                            <input type="hidden" name="member_id" value="<?= $m['MemberID']; ?>">
                            <select name="status" style="font-size:12px; padding:3px;">
                                <option value="Active" <?= $m['Status']=='Active'?'selected':''; ?>>Active</option>
                                <option value="Inactive" <?= $m['Status']=='Inactive'?'selected':''; ?>>Inactive</option>
                                <option value="Suspended" <?= $m['Status']=='Suspended'?'selected':''; ?>>Suspended</option>
                            </select>
                            <button type="submit" name="update_member_status" class="btn btn-blue" style="padding:3px 8px;">Ubah</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>