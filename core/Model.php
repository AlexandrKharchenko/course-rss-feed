<?php

    use Doctrine\DBAL\Configuration;
    use Doctrine\DBAL\DriverManager;

    class Model {

        public $db;

        public function __construct()
        {
            $config = new Configuration();

            $connectionParams = array(
                'dbname' => getenv('DBNAME'),
                'user' => getenv('DBUSER'),
                'password' => getenv('DBPSW'),
                'host' => 'localhost',
                'driver' => 'pdo_mysql',
            );
            $this->db = DriverManager::getConnection($connectionParams, $config);
        }


    }