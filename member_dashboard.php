<?php
require_once 'koneksi.php';

// Cek autentikasi hak akses Member
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit;
}

$member_id = $_SESSION['user_id'];
$msg = '';

if (isset($_POST['request_borrow'])) {
    $res = $borrowObj->requestBorrow($member_id, (int)$_POST['book_id'], $_POST['due_date']);
    if ($res === true) {
        $msg = "<div class='alert success'>Pengajuan peminjaman berhasil dikirim! Menunggu ACC Pustakawan.</div>";
    } else {
        $msg = "<div class='alert err'>$res</div>";
    }
}

if (isset($_POST['return_book'])) {
    $res = $borrowObj->returnBook((int)$_POST['borrow_id'], (int)$_POST['book_id'], $member_id);
    if ($res === 'late') {
        $msg = "<div class='alert err'>Buku berhasil dikembalikan, namun Anda terlambat! Denda Rp 10.000 diterbitkan.</div>";
    } elseif ($res === 'success') {
        $msg = "<div class='alert success'>Buku berhasil dikembalikan tepat waktu!</div>";
    }
}

if (isset($_POST['pay_fine'])) {
    if ($fineObj->payFine((int)$_POST['fine_id'], (int)$_POST['payment_method_id'], (float)$_POST['amount'])) {
        $msg = "<div class='alert success'>Pembayaran denda berhasil dikirim & diproses!</div>";
    }
}

// Fetch Master Data melalui Objek OOP
$profile        = $memberObj->getProfile($member_id);
$search         = $_GET['search'] ?? '';
$books          = $bookObj->getAll($search);
$my_borrows     = $borrowObj->getMemberBorrows($member_id);
$fines          = $fineObj->getMemberFines($member_id);
$paymentMethods = $fineObj->getPaymentMethods();

// Deteksi jika user memilih untuk melihat detail buku via URL parameter detail_id
$selectedBook = null;
if (isset($_GET['detail_id'])) {
    $detailId = (int)$_GET['detail_id'];
    foreach ($books as $b) {
        if ($b['BookID'] === $detailId) {
            $selectedBook = $b;
            if (empty($selectedBook['Summary'])) {
                $selectedBook['Summary'] = 'Belum ada ringkasan cerita untuk buku ini.';
            }
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Member Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #eef2f5; margin: 0; padding: 20px; }
        .navbar { background: #2c3e50; color: white; padding: 15px 20px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; }
        .container { display: flex; gap: 20px; margin-top: 20px; }
        .sidebar { flex: 1; background: white; padding: 15px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .main-content { flex: 3; background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; font-size: 13px; text-align: left; }
        th { background: #34495e; color: white; }
        .btn { padding: 6px 12px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; text-decoration: none; display: inline-block; }
        .btn-return { background: #e67e22; } .btn-pay { background: #2ecc71; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 13px; }
        .err { background: #e74c3c; color: white; } .success { background: #2ecc71; color: white; }
        .badge { padding: 3px 7px; border-radius: 3px; font-size: 11px; font-weight: bold; color: white; }
        .bg-green { background: #2ecc71; } .bg-red { background: #e74c3c; } .bg-yellow { background: #f39c12; } .bg-orange { background: #e67e22; }
        
        /* Link Judul Buku */
        .book-link { color: #2980b9; text-decoration: none; font-weight: bold; }
        .book-link:hover { text-decoration: underline; color: #16a085; }

        /* Pure PHP Modal Overlay */
        .modal-overlay { position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); display: flex; justify-content: center; align-items: center; }
        .modal-box { background-color: #fff; padding: 25px; border-radius: 8px; width: 80%; max-width: 620px; position: relative; box-shadow: 0 5px 25px rgba(0,0,0,0.4); }
        .close-btn { position: absolute; right: 20px; top: 15px; font-size: 26px; font-weight: bold; text-decoration: none; color: #888; }
        .close-btn:hover { color: #000; }
        .modal-body { display: flex; gap: 20px; margin-top: 15px; }
        .modal-left img { width: 160px; height: 230px; object-fit: cover; border-radius: 6px; box-shadow: 0 3px 10px rgba(0,0,0,0.2); }
        .modal-right { flex: 1; }
        .modal-right h2 { margin-top: 0; color: #2c3e50; font-size: 20px; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>Member Dashboard</h2>
    <div>Halo, <strong><?= htmlspecialchars($profile['Name']); ?></strong> | <a href="logout.php" style="color:#ff7675; text-decoration:none;">Logout</a></div>
</div>

<br>
<?= $msg; ?>

<!-- POP-UP MODAL OVERLAY DETAIL BUKU -->
<?php if ($selectedBook): ?>
<div class="modal-overlay">
    <div class="modal-box">
        <!-- Tombol Close -->
        <a href="member_dashboard.php<?= !empty($search) ? '?search=' . urlencode($search) : ''; ?>" class="close-btn">&times;</a>
        <div class="modal-body">
            <div class="modal-left">
                <?php 
                    $coverUrl = 'https://via.placeholder.com/150x220?text=No+Cover';
                    if (!empty($selectedBook['CoverImage'])) {
                        $coverUrl = str_starts_with($selectedBook['CoverImage'], 'http') 
                            ? $selectedBook['CoverImage'] 
                            : 'uploads/' . $selectedBook['CoverImage'];
                    }
                ?>
                <img src="<?= $coverUrl; ?>" alt="Cover Buku">
            </div>
            <div class="modal-right">
                <h2><?= htmlspecialchars($selectedBook['Title']); ?></h2>
                <p style="margin:4px 0; font-size:13px;"><strong>ISBN:</strong> <?= htmlspecialchars($selectedBook['ISBN']); ?></p>
                <p style="margin:4px 0; font-size:13px;"><strong>Penulis:</strong> <?= htmlspecialchars($selectedBook['Author']); ?></p>
                <p style="margin:4px 0; font-size:13px;"><strong>Penerbit:</strong> <?= htmlspecialchars($selectedBook['Publisher']); ?></p>
                <p style="margin:4px 0; font-size:13px;"><strong>Kategori:</strong> <?= htmlspecialchars($selectedBook['CategoryName'] ?? 'Uncategorized'); ?></p>
                <p style="margin:4px 0; font-size:13px;"><strong>Stok Tersedia:</strong> <?= $selectedBook['Stock']; ?> Unit</p>
                <hr style="margin:12px 0; border:0; border-top:1px solid #eee;">
                <h4 style="margin:0 0 6px 0; color:#34495e;">Ringkasan Buku (Short Summary):</h4>
                <p style="font-size:13px; line-height:1.6; color:#555; margin:0;"><?= htmlspecialchars($selectedBook['Summary']); ?></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="container">
    <div class="sidebar">
        <h3>Profil Saya</h3>
        <p><strong>Email:</strong> <?= htmlspecialchars($profile['Email']); ?></p>
        <p><strong>No HP:</strong> <?= htmlspecialchars($profile['Phone']); ?></p>
        <p><strong>Buku Dipinjam:</strong> <span class="badge <?= $profile['CurrentBorrowedCount'] >= 5 ? 'bg-red' : 'bg-yellow'; ?>"><?= $profile['CurrentBorrowedCount']; ?> / 5 Buku</span></p>
        <p><strong>Status Akun:</strong> <span class="badge bg-green"><?= htmlspecialchars($profile['Status']); ?></span></p>
    </div>

    <div class="main-content">
        <h3>Search Book</h3>
        <p style="font-size:12px; color:#7f8c8d; margin-top:-5px;">*Klik pada <strong>Judul Buku</strong> untuk melihat sampul & ringkasan cerita (*short summary*).</p>
        
        <form method="GET" style="display:flex; gap:10px; margin-bottom:15px;">
            <input type="text" name="search" placeholder="Cari Judul, ISBN, Penulis..." value="<?= htmlspecialchars($search); ?>" style="flex:1; padding:8px;">
            <button type="submit" class="btn">Cari</button>
        </form>

        <table>
            <thead>
                <tr><th>ISBN</th><th>Judul Buku</th><th>Kategori</th><th>Penulis</th><th>Stok</th><th>Pilih Tgl Kembalikan & Ajukan</th></tr>
            </thead>
            <tbody>
                <?php foreach($books as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['ISBN']); ?></td>
                    <td>
                        <a href="member_dashboard.php?detail_id=<?= $b['BookID']; ?><?= !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="book-link">
                            <?= htmlspecialchars($b['Title']); ?> 🔍
                        </a>
                    </td>
                    <td><?= htmlspecialchars($b['CategoryName'] ?? 'Uncategorized'); ?></td>
                    <td><?= htmlspecialchars($b['Author']); ?></td>
                    <td><?= $b['Stock']; ?></td>
                    <td>
                        <?php if($b['Stock'] > 0 && $profile['CurrentBorrowedCount'] < 5): ?>
                            <form method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="book_id" value="<?= $b['BookID']; ?>">
                                <input type="date" name="due_date" min="<?= date('Y-m-d', strtotime('+1 day')); ?>" max="<?= date('Y-m-d', strtotime('+14 days')); ?>" value="<?= date('Y-m-d', strtotime('+7 days')); ?>" required style="padding:4px; font-size:11px;">
                                <button type="submit" name="request_borrow" class="btn">Ajukan Pinjam</button>
                            </form>
                        <?php elseif($profile['CurrentBorrowedCount'] >= 5): ?>
                            <span class="badge bg-red">Batas Pinjam Penuh</span>
                        <?php else: ?>
                            <span class="badge bg-red">Stok Habis</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr style="margin: 30px 0;">

        <h3>Status Pengajuan & Buku Dipinjam</h3>
        <table>
            <thead>
                <tr><th>ID Pinjam</th><th>Judul Buku</th><th>Tgl Pengajuan</th><th>Jatuh Tempo</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php if(empty($my_borrows)): ?>
                    <tr><td colspan="6" style="text-align:center; color:#888;">Belum ada transaksi peminjaman.</td></tr>
                <?php endif; ?>
                <?php foreach($my_borrows as $mb): ?>
                <tr>
                    <td>#<?= $mb['BorrowID']; ?></td>
                    <td><?= htmlspecialchars($mb['Title']); ?></td>
                    <td><?= $mb['IssueDate']; ?></td>
                    <td><strong><?= $mb['DueDate']; ?></strong></td>
                    <td>
                        <span class="badge <?= $mb['Status']=='Pending'?'bg-orange':($mb['Status']=='Borrowed'?'bg-yellow':($mb['Status']=='Returned'?'bg-green':'bg-red')); ?>">
                            <?= $mb['Status']=='Pending' ? 'Menunggu ACC' : $mb['Status']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if($mb['Status'] === 'Borrowed'): ?>
                            <form method="POST" onsubmit="return confirm('Kembalikan buku ini?');">
                                <input type="hidden" name="borrow_id" value="<?= $mb['BorrowID']; ?>">
                                <input type="hidden" name="book_id" value="<?= $mb['BookID']; ?>">
                                <button type="submit" name="return_book" class="btn btn-return">Kembalikan</button>
                            </form>
                        <?php elseif($mb['Status'] === 'Pending'): ?>
                            <em style="color:#e67e22; font-size:12px;">Menunggu Verifikasi</em>
                        <?php else: ?>
                            <span style="color:#7f8c8d; font-size:12px;">Selesai (<?= $mb['ReturnDate']; ?>)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr style="margin: 30px 0;">

        <h3>Tagihan Denda Keterlambatan</h3>
        <table>
            <thead>
                <tr><th>Buku</th><th>Jatuh Tempo</th><th>Jumlah Denda</th><th>Status</th><th>Bayar Online</th></tr>
            </thead>
            <tbody>
                <?php if(empty($fines)): ?>
                    <tr><td colspan="5" style="text-align:center; color:#888;">Tidak ada denda. Anda sangat disiplin!</td></tr>
                <?php endif; ?>
                <?php foreach($fines as $f): ?>
                <tr>
                    <td><?= htmlspecialchars($f['Title']); ?></td>
                    <td><?= $f['DueDate']; ?></td>
                    <td>Rp <?= number_format($f['Amount'], 0, ',', '.'); ?></td>
                    <td><span class="badge <?= $f['FineStatus']=='Paid'?'bg-green':'bg-red'; ?>"><?= $f['FineStatus']; ?></span></td>
                    <td>
                        <?php if($f['FineStatus'] === 'Unpaid'): ?>
                            <form method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="fine_id" value="<?= $f['FineID']; ?>">
                                <input type="hidden" name="amount" value="<?= $f['Amount']; ?>">
                                <select name="payment_method_id" required style="font-size:11px;">
                                    <?php foreach($paymentMethods as $m): ?>
                                        <option value="<?= $m['PaymentMethodID']; ?>"><?= htmlspecialchars($m['MethodName']); ?> (<?= htmlspecialchars($m['Provider']); ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="pay_fine" class="btn btn-pay">Bayar</button>
                            </form>
                        <?php else: ?>
                            <em>Lunas</em>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>