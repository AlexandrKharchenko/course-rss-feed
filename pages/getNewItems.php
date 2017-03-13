<?php
    require 'vendor/autoload.php';
    use Core\DB;


    if(isset($_POST['getNew']) ){

        $pdo = DB::getPdo();
        $stm = $pdo->prepare("SELECT * FROM `rss` ORDER BY `id` DESC LIMIT 0 , :limit");
        $stm->bindValue('limit', intval($_POST['getNew']) , PDO::PARAM_INT);
        $stm->execute();
        $lenta = $stm->fetchAll();


        if($stm->rowCount() > 0)
            include 'tpls/items.php.twig';
        else
            return 'notFound';


    }