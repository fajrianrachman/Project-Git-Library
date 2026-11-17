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
        $stmt = $this->db->prepare("INSERT INTO finepayment (FineID, PaymentMethodID, Amount, PaymentDate) 
                                    VALUES (:f_id, :m_id, :amt, NOW())");
        return $stmt->execute([':f_id' => $fineId, ':m_id' => $methodId, ':amt' => $amount]);
    }

    public function getPaymentMethods() {
        return $this->db->query("SELECT * FROM paymentmethod")->fetchAll();
    }
}
?>