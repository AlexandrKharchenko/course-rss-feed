<?php

    namespace Models;

    use Core\Model;

    /**
     * Class Rss
     * @package Models
     */
    class Rss extends Model {

        public function __construct()
        {
            parent::__construct();
        }


        /**
         * Возвращает элементы RSS для главной странциы
         */
        public function getItemsForMainPage()
        {

        }
    }