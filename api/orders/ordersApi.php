<?php
require_once '../api.php';
require_once '../db.php';

class OrdersApi extends Api
{
    public $apiName = 'orders';

    /**
     * Метод GET
     * Получение списка заказов
     * Пример запроса - http://127.0.0.1/api/orders/
     * @return string
     */
    public function indexAction()
    {
        $redis = (new Db())->getConnectRedis();
        
        if(count($redis->scan($iterator, $this->ip.':*')) < 10) {
            $db = (new Db())->getConnect();
            Db::setRateLimit($redis, $this->currentKey);

            $orders = pg_fetch_all(pg_query($db, "SELECT * FROM orders;"), PGSQL_ASSOC);
            
            if($orders) {
                return $this->response($orders, 200);
            }
            return $this->response('Data not found', 404);
        }
        return $this->response('Too many requests', 429);
    }

    /**
     * Метод GET
     * Метод получения информации о заказе по id
     * Пример запроса - http://127.0.0.1/api/orders/?id=1
     * @return string
     */
    public function viewAction()
    {   
        $redis = (new Db())->getConnectRedis();

        if(count($redis->scan($iterator, $this->ip.':*')) < 10) {
            $id = $this->requestParams['id'];
            
            if($id){
                $db = (new Db())->getConnect();
                Db::setRateLimit($redis, $this->currentKey);
                
                $order = pg_fetch_row(pg_query($db, "SELECT * FROM orders WHERE \"ID\" = '$id';"), 0, PGSQL_ASSOC);
                if($order) {
                    return $this->response($order, 200);
                }
            }
            return $this->response('Wrong id', 404);
        }
        return $this->response('Too many requests', 429);
    }

    /**
     * Метод POST
     * Создание новой записи
     * Пример запроса - http://127.0.0.1/api/orders/?name=Test&street=TestStreet&home=100&phone=123456789&product=testProduct
     * @return string
     */
    public function createAction()
    {
        $redis = (new Db())->getConnectRedis();

        if(count($redis->scan($iterator, $this->ip.':*')) < 10) {
            $arParams = [
                'name' => $this->requestParams['name'] ?? '',
                'street' => $this->requestParams['street'] ?? '',
                'home' => $this->requestParams['home'] ?? '',
                'phone' => $this->requestParams['phone'] ?? '',
                'product' => $this->requestParams['product'] ?? ''
            ];

            if($arParams['name'] && $arParams['street'] && $arParams['home'] && $arParams['phone'] && $arParams['product']) {
                $db = (new Db())->getConnect();
                Db::setRateLimit($redis, $this->currentKey);
                
                $query = "INSERT INTO orders (\"ID\", \"NAME\", \"STREET\", \"HOME\", \"PHONE\", \"PRODUCT\") 
                VALUES (default, '{$arParams['name']}', '{$arParams['street']}', '{$arParams['home']}', '{$arParams['phone']}', '{$arParams['product']}');";
                $order = pg_query($db['postgres'], $query);
                if($order) {
                    return $this->response('Data saved.', 200);
                }
            }
            return $this->response("Saving error", 500);
        }
        return $this->response('Too many requests', 429);
    }
}