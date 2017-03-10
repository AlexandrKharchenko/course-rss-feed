<?php
    use Core\DB;

    require 'vendor/autoload.php';

    $offset = 0;
    $pdo = DB::getPdo();
    $stm = $pdo->prepare("SELECT * FROM `rss` ORDER BY `id` DESC LIMIT ? , ?");
    $stm->bindValue(1, $offset , PDO::PARAM_INT);
    $stm->bindValue(2, 50, PDO::PARAM_INT);
    $stm->execute();
    $lenta = $stm->fetchAll();

    include 'tpls/lenta.php';
?>


