<?php

    namespace Core;
    require __DIR__ . '/../env.var.php';

    use PDO;

    class DB
    {
        protected static $instance = NULL;

        private static $user;
        private static $psw;

        public function __construct()
        {
        }

        public function __clone()
        {
        }

        public static function instance()
        {
            if (self::$instance === NULL) {
                self::$user = getenv('DBNAME');
                self::$psw = getenv('DBPSW');
                $params = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => TRUE,
                ];
                $dsn = 'mysql:dbname=rss;host=localhost;charset=UTF8';
                self::$instance = new PDO($dsn, self::$user, self::$psw, $params);
            }

            return self::$instance;
        }

        public static function __callStatic($method, $args)
        {
            return call_user_func_array([self::instance(), $method], $args);
        }

        public static function query($sql, $args = [])
        {
            $stmt = self::instance()->prepare($sql);
            $stmt->execute($args);
            return $stmt;
        }

        public static function  getPdo()
        {
            return self::instance();
        }
    }