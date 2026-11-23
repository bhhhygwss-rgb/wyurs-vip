<?php require_once 'sorgu_bekcisi.php'; ?>
<?php
function getDB() {
    static $db = null;
    if ($db === null) {
        try {
            $db = new PDO("mysql:host=localhost;dbname=101m;charset=utf8mb4", "root", "");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->exec("SET NAMES utf8mb4");
        } catch (Exception $e) {
            return null;
        }
    }
    return $db;
}
function dbError() {
    try {
        new PDO("mysql:host=localhost;dbname=101m;charset=utf8mb4", "root", "");
        return "";
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
?>

