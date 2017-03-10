<?php
    require 'vendor/autoload.php';
    # Переменные окружения
    require 'env.var.php';
   
    use Core\DB;
    use Monolog\Logger;
    use Monolog\Handler\SlackHandler;
    use ElephantIO\Client as Elephant;
    use ElephantIO\Engine\SocketIO\Version1X;


    $log = new Logger('RSS');
    $log->pushHandler(new SlackHandler(getenv('SLACK-KEY'), '#monolog', 'alexsot1545', false, null , Monolog\Logger::DEBUG));


    $feed = new SimplePie();
    $feed->set_feed_url([
        'https://rss.unian.net/site/news_ukr.rss',
        'https://habrahabr.ru/rss/feed/posts/6266e7ec4301addaf92d10eb212b4546/'
    ]);
    $feed->enable_cache(FALSE);
    $feed->init();
    $items = $feed->get_items();

    # Добавленных записей
    $addedToDb = 0;
    foreach ($items as $k => $item) {

        $currentData = [
            $item->get_description(),
            $item->get_title(),
            $item->get_date('Y-m-d H:i:s'),
            $item->get_link(),
            0,
        ];


        if ($enclosure = $item->get_enclosure()){
            if($enclosure->link)
                $currentData[4] = $enclosure->link;
            else
                $currentData[4] = '';
        }



        # Проверка
        try {
            $existInDb = DB::query("SELECT * FROM `rss` WHERE `link` = ?", [$item->get_link()]);
            if ($existInDb->rowCount() == 0) {
                $addedToDb++;
                $result = DB::query("INSERT INTO rss (description, title, date_created, link , img_link) VALUES (?, ?, ?, ?, ?)", $currentData);
            }


        } catch (PDOException $e) {
            $log->error($e->getmessage(), ['line' => __LINE__ , 'file' => __FILE__ , 'time' => date('d-m-Y H:i:s'),] , []);
        }
    }
    
    # Отправка в логи
        if($addedToDb > 0) {
            $log->info("Добавлено {$addedToDb} записей." , [] , []);

            # Подключение к серверу Socket.io
            try{
                $elephant = new Elephant(new Version1X("http://localhost:3009"));
                $elephant->initialize();
                $elephant->emit('addedArticle', ['count' => $addedToDb]);
                $elephant ->close();
            } catch (Exception $e){
                $log->error($e->getmessage(), ['line' => __LINE__ , 'file' => __FILE__ , 'time' => date('d-m-Y H:i:s'),] , []);
            }

        }







