<?php
require_once 'class_user.php';

class Librarian extends User {

    public function login($employeeId, $password) {
        $stmt = $this->db->prepare("SELECT * FROM librarian WHERE EmployeeID = :employee_id AND Password = :password AND Status = 'Active'");
        $stmt->execute([':employee_id' => $employeeId, ':password' => $password]);
        return $stmt->fetch();
    }
}
?>