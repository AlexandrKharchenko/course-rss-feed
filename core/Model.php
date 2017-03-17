<?php

    namespace Core;
    use Doctrine\DBAL\Configuration;
    use Doctrine\DBAL\DriverManager;



    class Model {

        public $db;
        protected $queryBuilder;

        public function __construct()
        {
            $config = new Configuration();

            $connectionParams = array(
                'dbname' => getenv('DBNAME'),
                'user' => getenv('DBUSER'),
                'password' => getenv('DBPSW'),
                'host' => 'localhost',
                'driver' => 'pdo_mysql',
                'charset'   => 'UTF8'
            );
            $this->db = DriverManager::getConnection($connectionParams, $config);
            $this->queryBuilder = $this->db->createQueryBuilder();
        }


    }