<?php


    require 'vendor/autoload.php';
    require 'env.var.php';

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Matcher\UrlMatcher;
    use Symfony\Component\Routing\RequestContext;
    use Symfony\Component\Routing\RouteCollection;
    use Symfony\Component\Routing\Route;



    $request = Request::createFromGlobals();

    # Коллекция маршрутов
    $routes = new RouteCollection();
    $context = new RequestContext();
    $context->fromRequest($request);



    # Добавление роутов
    $routes->add('show.login' , new Route('/login', ['action' => 'MainController@login']));
    $routes->add('show.index' , new Route('/', ['action' => 'MainController@index']));
    $routes->add('cron.parse-items' , new Route('/parser', ['action' => 'MainController@parseItem']));
    $routes->add('ajax.load-items' , new Route('/ajax-load-item', ['action' => 'MainController@ajaxNewItem']));



    $matcher = new UrlMatcher($routes, $context);
    $parameters = $matcher->matchRequest($request);

    # Вызов страницы если существует
    if($parameters && $parameters['action']){
        $actionData = explode('@' , $parameters['action']);
        $controllerName = '\Controllers\\' . $actionData[0];
        $action = $actionData[1];

        $controller = new  $controllerName;
        $response = $controller->{$action}($request);
        print_r($response);
        die;

    } else {
        die('Скоро будет 404');
    }



