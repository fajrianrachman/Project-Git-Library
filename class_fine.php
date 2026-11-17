<?php
class Fine {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getMemberFines($memberId) {
        $stmt = $this->db->prepare("SELECT f.FineID, f.Amount, f.Status AS FineStatus, b.Title, br.DueDate 
                                    FROM fine f
                                    JOIN borrow br ON f.BorrowID = br.BorrowID
                                    JOIN borrowdetail bd ON br.BorrowID = bd.BorrowID
                                    JOIN book b ON bd.BookID = b.BookID
                                    WHERE br.MemberID = :m_id");
        $stmt->execute([':m_id' => $memberId]);
        return $stmt->fetchAll();
    }

    public function payFine($fineId, $methodId, $amount) {
        try {
            $this->db->beginTransaction();

            // 1. Catat transaksi pembayaran ke tabel finepayment
            $stmt = $this->db->prepare("INSERT INTO finepayment (FineID, PaymentMethodID, Amount, PaymentDate) 
                                        VALUES (:f_id, :m_id, :amt, NOW())");
            $stmt->execute([
                ':f_id' => $fineId,
                ':m_id' => $methodId,
                ':amt'  => $amount
            ]);

            // 2. Ubah status tagihan pada tabel fine menjadi 'Paid'
            $update = $this->db->prepare("UPDATE fine SET Status = 'Paid' WHERE FineID = :f_id");
            $update->execute([':f_id' => $fineId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getPaymentMethods() {
        return $this->db->query("SELECT * FROM paymentmethod")->fetchAll();
    }
}
?>