<?php
class Db {

    /**
     * Данные для соединения с БД
     */
    private $host = "localhost";
    private $dbName = "name";
    private $username = "name";
    private $password = "password";
    private $redisPort = 6379;

    public $conn;
    public $redisConn;

    /**
     * Подключение к базе данных PostgreSQL
     * @return pgsql_link
     */
    public function getConnect() {
        try {
            $this->conn = pg_connect("host=$this->host dbname=$this->dbName user=$this->username password=$this->password");
        } catch(PDOException $exception){
            echo "PostgeSQL connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

    /**
     * Подключение к базе данных Redis
     * @return Redis
     */
    public function getConnectRedis() {
        try {
            $this->redisConn = new Redis();
            $this->redisConn->connect($this->host, $this->redisPort);
        } catch(PDOException $exception){
            echo "Redis connection error: " . $exception->getMessage();
        }

        return $this->redisConn;
    }
    
    /**
     * Ограничение запросов (тайм-аут 59 секунд)
     * @return void
     */
    public function setRateLimit(Redis $redis, $currentKey) {
        $redis->multi();
        $redis->incr($currentKey);
        $redis->expire($currentKey, 59);
        $redis->exec();
    }
}
