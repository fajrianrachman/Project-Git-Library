<?php
require_once 'class_user.php';

class Member extends User {

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM member WHERE Email = :email AND Password = :password AND Status = 'Active'");
        $stmt->execute([':email' => $email, ':password' => $password]);
        return $stmt->fetch();
    }

    public function register($name, $email, $phone, $address, $password) {
        $check = $this->db->prepare("SELECT MemberID FROM member WHERE Email = :email");
        $check->execute([':email' => $email]);
        if ($check->rowCount() > 0) {
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO member (Name, Email, Phone, Address, Status, CurrentBorrowedCount, Password) 
                                    VALUES (:name, :email, :phone, :address, 'Active', 0, :password)");
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':address' => $address,
            ':password' => $password
        ]);
    }

    public function getProfile($memberId) {
        $stmt = $this->db->prepare("SELECT * FROM member WHERE MemberID = :id");
        $stmt->execute([':id' => $memberId]);
        return $stmt->fetch();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM member ORDER BY MemberID DESC");
        return $stmt->fetchAll();
    }

    public function updateStatus($memberId, $status) {
        $stmt = $this->db->prepare("UPDATE member SET Status = :status WHERE MemberID = :id");
        return $stmt->execute([':status' => $status, ':id' => $memberId]);
    }
}
?>