<?php

    namespace Models;

    use Core\Model;

    /**
     * Class Rss
     * @package Models
     */
    class Rss extends Model
    {

        public function __construct()
        {
            parent::__construct();
        }


        /**
         * Возвращает элементы RSS для главной странциы
         */
        public function getItemsForMainPage($perPage = 50 , $offset = 0)
        {
            $result = $this->queryBuilder->select("*")
                ->from('rss')
                ->setFirstResult($offset)
                ->orderBy('id', 'ASC')
                ->setMaxResults($perPage)
                ->execute()
                ->fetchAll();


            return $result;
        }

        /**
         * Добавляет новые записи
         */
        public function addNewItems()
        {
            
        }
    }