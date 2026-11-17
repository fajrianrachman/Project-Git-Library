<?php
class Book {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Mengambil semua buku + Mengisi ringkasan otomatis di PHP jika DB masih kosong
     */
    public function getAll($search = '') {
        $sql = "SELECT b.*, c.CategoryName FROM book b LEFT JOIN bookcategory c ON b.CategoryID = c.CategoryID ";
        if (!empty($search)) {
            $sql .= "WHERE b.Title LIKE :s OR b.ISBN LIKE :s OR b.Author LIKE :s OR b.Publisher LIKE :s ";
        }
        $sql .= "ORDER BY b.BookID DESC";
        
        $stmt = $this->db->prepare($sql);
        if (!empty($search)) {
            $stmt->execute([':s' => "%$search%"]);
        } else {
            $stmt->execute();
        }
        $books = $stmt->fetchAll();

        // Pemetaan Ringkasan Otomatis via PHP (Fallback jika di DB NULL / Kosong)
        $defaultSummaries = [
            1  => 'Clean Code memberikan panduan praktis dan prinsip-prinsip penulisan kode yang bersih, efisien, serta mudah dipelihara oleh pengembang software.',
            2  => 'Design Patterns berisi katalog solusi berulang untuk masalah-masalah umum dalam desain arsitektur perangkat lunak berbasis Objek (OOP).',
            3  => 'Laskar Pelangi menceritakan kisah perjuangan 10 anak di Gantong, Belitung, dalam menempuh pendidikan di tengah keterbatasan fasilitas.',
            4  => 'Bumi Manusia menceritakan kisah Minke di era kolonial Belanda, menyoroti perjuangan hak asasi, kebebasan, hukum, dan percintaan.',
            5  => 'Cosmos menjelajahi sains, alam semesta, sejarah peradaban, dan hubungan eksistensi manusia dengan jagat raya dalam sudut pandang ilmiah.',
            6  => 'A Brief History of Time membahas konsep asal-usul alam semesta, waktu, lubang hitam (black hole), dan teori relativitas secara ilmiah populer.',
            7  => 'Sapiens menceritakan riwayat panjang perjalanan evolusi spesies manusia (Homo sapiens) dari zaman batu hingga era modern.',
            8  => 'Nusantara menyajikan rekaman sejarah komprehensif kepulauan Indonesia dari masa kerajaan purba hingga era penjajahan kolonial.',
            9  => 'Atomic Habits menjelaskan cara membuat perubahan-perubahan kecil 1% setiap hari yang konsisten untuk membentuk kebiasaan luar biasa.',
            10 => 'Deep Work mengajarkan metodologi fokus tanpa gangguan pada tugas-tugas berat untuk mencapai produktivitas dan hasil karya berkualitas tinggi.',
            11 => 'Buku motivasi dan refleksi diri tentang kesalahan-kesalahan umum yang dilakukan manusia beserta langkah perbaikannya.'
        ];

        // Format data buku di PHP
        foreach ($books as &$b) {
            if (empty($b['Summary'])) {
                $b['Summary'] = $defaultSummaries[$b['BookID']] 
                    ?? 'Buku berjudul "' . $b['Title'] . '" karangan ' . $b['Author'] . ' ini diterbitkan oleh ' . $b['Publisher'] . '.';
            }
        }

        return $books;
    }

    /**
     * Tambah Buku Baru beserta Ringkasan
     */
    public function add($isbn, $title, $categoryId, $author, $publisher, $stock, $coverImage = null, $summary = null) {
        $stmt = $this->db->prepare("INSERT INTO book (ISBN, Title, CategoryID, Author, Publisher, Edition, Status, Stock, CoverImage, Summary) 
                                    VALUES (:isbn, :title, :cat_id, :author, :publisher, '1st', 'Available', :stock, :cover, :summary)");
        return $stmt->execute([
            ':isbn'      => $isbn,
            ':title'     => $title,
            ':cat_id'    => $categoryId,
            ':author'    => $author,
            ':publisher' => $publisher,
            ':stock'     => $stock,
            ':cover'     => $coverImage,
            ':summary'   => $summary
        ]);
    }

    /**
     * Edit Data Buku & Ringkasan (Summary)
     */
    public function update($bookId, $isbn, $title, $categoryId, $author, $publisher, $stock, $coverName = null, $summary = null) {
        if ($coverName) {
            $sql = "UPDATE book SET ISBN = :isbn, Title = :title, CategoryID = :cat_id, Author = :author, Publisher = :publisher, Stock = :stock, CoverImage = :cover, Summary = :summary WHERE BookID = :id";
            $params = [
                ':isbn'      => $isbn,
                ':title'     => $title,
                ':cat_id'    => $categoryId,
                ':author'    => $author,
                ':publisher' => $publisher,
                ':stock'     => $stock,
                ':cover'     => $coverName,
                ':summary'   => $summary,
                ':id'        => $bookId
            ];
        } else {
            $sql = "UPDATE book SET ISBN = :isbn, Title = :title, CategoryID = :cat_id, Author = :author, Publisher = :publisher, Stock = :stock, Summary = :summary WHERE BookID = :id";
            $params = [
                ':isbn'      => $isbn,
                ':title'     => $title,
                ':cat_id'    => $categoryId,
                ':author'    => $author,
                ':publisher' => $publisher,
                ':stock'     => $stock,
                ':summary'   => $summary,
                ':id'        => $bookId
            ];
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Khusus Unggah / Ganti Sampul Gambar
     */
    public function updateCover($bookId, $coverName) {
        $stmt = $this->db->prepare("UPDATE book SET CoverImage = :cover WHERE BookID = :id");
        return $stmt->execute([
            ':cover' => $coverName,
            ':id'    => $bookId
        ]);
    }

    public function updateStatus($bookId, $status) {
        $stmt = $this->db->prepare("UPDATE book SET Status = :status WHERE BookID = :id");
        return $stmt->execute([':status' => $status, ':id' => $bookId]);
    }

    public function delete($bookId) {
        $stmt = $this->db->prepare("DELETE FROM book WHERE BookID = :id");
        return $stmt->execute([':id' => $bookId]);
    }

    public function addCategory($categoryName) {
        $stmt = $this->db->prepare("INSERT INTO bookcategory (CategoryName) VALUES (:name)");
        return $stmt->execute([':name' => $categoryName]);
    }

    public function getAllCategories() {
        $stmt = $this->db->query("SELECT * FROM bookcategory ORDER BY CategoryName ASC");
        return $stmt->fetchAll();
    }
}
?>