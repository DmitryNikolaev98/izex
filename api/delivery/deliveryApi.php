<?php
require_once '../api.php';
require_once '../db.php';

class DeliveryApi extends Api
{
    public $apiName = 'delivery';

    /**
     * Метод GET
     * Расчёт стоимости доставки
     * Пример запроса - http://127.0.0.1/api/delivery/?street=Huzangaya&home=1
     * @return string
     */
    public function viewAction()
    {
        $redis = (new Db())->getConnectRedis();

        if(count($redis->scan($iterator, $this->ip.':*')) < 10) {
            $street = $this->requestParams['street'] ?? '';
            $home = $this->requestParams['home'] ?? '';

            if($street && $home) {
                $db = (new Db())->getConnect();
                Db::setRateLimit($redis, $this->currentKey);
                
                $orders = pg_fetch_row(pg_query($db, "SELECT \"COST\" FROM addresses 
                                                        WHERE \"STREET\" = '$street' AND \"HOME\" = '$home';"), 0, PGSQL_ASSOC);
                
                if($orders){
                    return $this->response($orders, 200);
                }
            }
            return $this->response('Data not found', 404);
        }
        return $this->response('Too many requests', 429);
    }

    public function indexAction()
    {
    }

    public function createAction()
    {
    }
}