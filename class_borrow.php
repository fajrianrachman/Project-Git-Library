<?php
class Borrow {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Ajukan Peminjaman Buku (Oleh Member)
     * Mengurangi stok buku secara otomatis & mengecek batas maksimal 5 pinjaman.
     */
    public function requestBorrow($memberId, $bookId, $dueDate) {
        // 1. Cek total pinjaman aktif + pending (maksimal 5 buku)
        $stmtLimit = $this->db->prepare("
            SELECT COUNT(*) FROM borrow 
            WHERE MemberID = :m_id AND Status IN ('Pending', 'Borrowed')
        ");
        $stmtLimit->execute([':m_id' => $memberId]);
        $activeCount = $stmtLimit->fetchColumn();

        if ($activeCount >= 5) {
            return "Batas peminjaman penuh! Anda sudah memiliki 5 transaksi aktif/pending.";
        }

        // 2. Cek ketersediaan stok buku
        $stmtStock = $this->db->prepare("SELECT Stock FROM book WHERE BookID = :b_id");
        $stmtStock->execute([':b_id' => $bookId]);
        $stock = $stmtStock->fetchColumn();

        if ($stock <= 0) {
            return "Stok buku sedang habis.";
        }

        try {
            $this->db->beginTransaction();

            // Kurangi stok buku
            $stmtDec = $this->db->prepare("UPDATE book SET Stock = Stock - 1 WHERE BookID = :b_id");
            $stmtDec->execute([':b_id' => $bookId]);

            // Insert ke tabel borrow dengan status 'Pending'
            $stmtBorrow = $this->db->prepare("
                INSERT INTO borrow (MemberID, IssueDate, DueDate, Status) 
                VALUES (:m_id, CURDATE(), :due, 'Pending')
            ");
            $stmtBorrow->execute([
                ':m_id' => $memberId,
                ':due'  => $dueDate
            ]);
            $borrowId = $this->db->lastInsertId();

            // Insert detail buku ke tabel borrowdetail
            $stmtDetail = $this->db->prepare("
                INSERT INTO borrowdetail (BorrowID, BookID, Quantity) 
                VALUES (:borrow_id, :book_id, 1)
            ");
            $stmtDetail->execute([
                ':borrow_id' => $borrowId,
                ':book_id'   => $bookId
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }

    /**
     * ACC / Disetujui (Oleh Librarian)
     * Mengubah status menjadi 'Borrowed' & mencatat ID Pustakawan.
     */
    public function approveBorrow($borrowId, $librarianId) {
        $stmt = $this->db->prepare("
            UPDATE borrow 
            SET Status = 'Borrowed', LibrarianID = :lib_id 
            WHERE BorrowID = :id
        ");
        return $stmt->execute([
            ':lib_id' => $librarianId,
            ':id'     => $borrowId
        ]);
    }

    /**
     * Tolak Pengajuan (Oleh Librarian)
     * Mengubah status menjadi 'Returned' & mengembalikan stok buku (+1).
     */
    public function rejectBorrow($borrowId, $bookId) {
        try {
            $this->db->beginTransaction();

            $stmt1 = $this->db->prepare("UPDATE borrow SET Status = 'Returned' WHERE BorrowID = :id");
            $stmt1->execute([':id' => $borrowId]);

            $stmt2 = $this->db->prepare("UPDATE book SET Stock = Stock + 1 WHERE BookID = :book_id");
            $stmt2->execute([':book_id' => $bookId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Pengembalian Buku Mandiri (Oleh Member / Pustakawan)
     * Mengembalikan stok, mencatat ReturnDate, dan menerbitkan denda jika terlambat.
     */
    public function returnBook($borrowId, $bookId, $memberId) {
        try {
            $this->db->beginTransaction();

            // Ambil DueDate untuk cek keterlambatan
            $stmtCheck = $this->db->prepare("SELECT DueDate FROM borrow WHERE BorrowID = :b_id");
            $stmtCheck->execute([':b_id' => $borrowId]);
            $dueDate = $stmtCheck->fetchColumn();

            $today  = date('Y-m-d');
            $isLate = ($today > $dueDate);

            // Update status transaksi menjadi Returned
            $stmtBorrow = $this->db->prepare("UPDATE borrow SET Status = 'Returned' WHERE BorrowID = :b_id");
            $stmtBorrow->execute([':b_id' => $borrowId]);

            // Catat tanggal pengembalian di detail
            $stmtDetail = $this->db->prepare("
                UPDATE borrowdetail 
                SET ReturnDate = CURDATE() 
                WHERE BorrowID = :b_id AND BookID = :book_id
            ");
            $stmtDetail->execute([':b_id' => $borrowId, ':book_id' => $bookId]);

            // Restorasi stok buku (+1)
            $stmtStock = $this->db->prepare("UPDATE book SET Stock = Stock + 1 WHERE BookID = :book_id");
            $stmtStock->execute([':book_id' => $bookId]);

            // Terbitkan denda jika melebihi DueDate
            if ($isLate) {
                $stmtFine = $this->db->prepare("
                    INSERT INTO fine (BorrowID, Amount, Status) 
                    VALUES (:b_id, 10000.00, 'Unpaid')
                ");
                $stmtFine->execute([':b_id' => $borrowId]);
            }

            $this->db->commit();
            return $isLate ? 'late' : 'success';
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Mengambil daftar pengajuan status 'Pending' untuk Pustakawan
     */
    public function getPendingRequests() {
        $sql = "SELECT b.BorrowID, m.Name AS MemberName, bk.Title, bk.BookID, b.IssueDate, b.DueDate 
                FROM borrow b
                JOIN member m ON b.MemberID = m.MemberID
                JOIN borrowdetail bd ON b.BorrowID = bd.BorrowID
                JOIN book bk ON bd.BookID = bk.BookID
                WHERE b.Status = 'Pending'
                ORDER BY b.BorrowID ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil daftar buku yang sedang dipinjam (Status 'Borrowed')
     */
    public function getActiveBorrows() {
        $sql = "SELECT b.BorrowID, m.Name AS MemberName, bk.Title, bk.BookID, b.IssueDate, b.DueDate 
                FROM borrow b
                JOIN member m ON b.MemberID = m.MemberID
                JOIN borrowdetail bd ON b.BorrowID = bd.BorrowID
                JOIN book bk ON bd.BookID = bk.BookID
                WHERE b.Status = 'Borrowed'
                ORDER BY b.BorrowID ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil riwayat & daftar pinjaman milik member tertentu
     */
    public function getMemberBorrows($memberId) {
        $sql = "SELECT b.BorrowID, bk.BookID, bk.Title, b.IssueDate, b.DueDate, b.Status, bd.ReturnDate 
                FROM borrow b
                JOIN borrowdetail bd ON b.BorrowID = bd.BorrowID
                JOIN book bk ON bd.BookID = bk.BookID
                WHERE b.MemberID = :m_id
                ORDER BY b.BorrowID DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':m_id' => $memberId]);
        return $stmt->fetchAll();
    }
}
?>