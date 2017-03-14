<?php

    namespace Controllers;

    use Core\Controller;
    use Core\DB;
    use ElephantIO\Engine\SocketIO\Version1X;
    use ElephantIO\Client as Elephant;
    use Monolog\Handler\SlackHandler;
    use Monolog\Logger;
    use PDO;
    use PDOException;
    use SimplePie;
    use Symfony\Component\HttpFoundation\Request;


    /**
     * Class MainController
     * @package Controllers
     */
    class MainController extends Controller
    {

        public function __construct()
        {
            parent::__construct();
        }

        /** HomePage
         * @param Request $request
         * @return string
         */
        public function index(Request $request)
        {

            $offset = 0;
            $pdo = DB::getPdo();
            $stm = $pdo->prepare("SELECT * FROM `rss` ORDER BY `id` DESC LIMIT ? , ?");
            $stm->bindValue(1, $offset, PDO::PARAM_INT);
            $stm->bindValue(2, 50, PDO::PARAM_INT);
            $stm->execute();

            $lentaItems = $this->view->render('tpl-parts/items.php.twig', ['lenta' => $stm->fetchAll()]);
            $content = $this->view->render('pages/home.php.twig', ['lenta' => $lentaItems]);

            return $this->view->render('template/app.php.twig', ['content' => $content]);
        }

        /**
         * Парсит новые записи
         */
        public function parseItem(Request $request)
        {
            // TODO: Сделать досутп по Паролю
            $log = new Logger('RSS');
            $log->pushHandler(new SlackHandler(getenv('SLACK-KEY'), '#monolog', 'alexsot1545', false, null , Logger::DEBUG));


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
                try {
                    $elephant = new Elephant(new Version1X("http://localhost:3009"));
                    $elephant->initialize();
                    $elephant->emit('addedArticle', ['count' => $addedToDb]);
                    $elephant ->close();
                } catch (Exception $e){
                    $log->error($e->getmessage(), ['line' => __LINE__ , 'file' => __FILE__ , 'time' => date('d-m-Y H:i:s'),] , []);
                }

            }
        }


        /**
         * Возвращает новые записи, по запросу AJAX
         * @param Request $request
         * @return string
         */
        public function ajaxNewItem(Request $request)
        {
            $pdo = DB::getPdo();
            $stm = $pdo->prepare("SELECT * FROM `rss` ORDER BY `id` DESC LIMIT 0 , :limit");
            $stm->bindValue('limit', intval($request->request->get('getNew')) , PDO::PARAM_INT);
            $stm->execute();
            $lenta = $stm->fetchAll();

            return $this->view->render('tpl-parts/items.php.twig', ['lenta' => $lenta]);
        }


    }