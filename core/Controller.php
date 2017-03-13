<?php

    namespace Core;

    use Twig_Environment;
    use Twig_Loader_Filesystem;




    class Controller
    {

        protected $view;


        public function __construct()
        {
            $loader = new Twig_Loader_Filesystem(__DIR__ . '/../views');
            $this->view = new Twig_Environment($loader);
        }


    }