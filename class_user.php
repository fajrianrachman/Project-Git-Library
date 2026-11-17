<?php
abstract class User {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    abstract public function login($identifier, $password);
}
?>