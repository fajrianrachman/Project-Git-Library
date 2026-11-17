<?php
session_start();

require_once __DIR__ . '/class_database.php';
require_once __DIR__ . '/class_user.php'; 
require_once __DIR__ . '/class_member.php';
require_once __DIR__ . '/class_librarian.php';
require_once __DIR__ . '/class_book.php';
require_once __DIR__ . '/class_borrow.php';
require_once __DIR__ . '/class_fine.php';

$dbObj = new Database();
$db = $dbObj->getConnection();

$memberObj    = new Member($db);
$librarianObj = new Librarian($db);
$bookObj      = new Book($db);
$borrowObj    = new Borrow($db);
$fineObj      = new Fine($db);
?>