<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/12
 * Time: 10:20
 */
class Kyapi_Model_redisInit {

    private $redis; //redis对象

    /**
     * 初始化Redis
     * $config = array(
     *  'server' => '127.0.0.1' 服务器
     *  'port'   => '6379' 端口号
     * )
     */
    public function __construct() {
        $this->redis = new Redis();
        return $this->redis;
    }

    public function connect($config) {
        $this->redis->connect($config['server'], $config['port']);
        $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);    // 使用 built-in 序列化
    }

    /**
     * 设置值
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param int $timeOut 时间
     * @return bool
     */
    public function set($key, $value, $timeOut = 0) {
        $value = json_encode($value, TRUE);
        $retRes = $this->redis->set($key, $value);
        if ($timeOut > 0) $this->redis->setTimeout($key, $timeOut);
        return $retRes;
    }

    /**
     * 通过KEY获取数据
     * @param string $key KEY名称
     * @return mixed
     */
    public function getR($key) {
       $result = $this->redis->get($key);
       return json_decode($result, TRUE);
    }

    /**
     * 删除一条数据
     * @param string $key KEY名称
     * @return int
     */
    public function delete($key) {
        return $this->redis->delete($key);
    }

    /**
     * 清空数据
     */
    public function flushAll() {
        return $this->redis->flushAll();
    }

    /**
     * 数据入队列
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param bool $right 是否从右边开始入
     * @return bool|int
     */
    public function push($key, $value ,$right = true) {
        $value = json_encode($value);
        return $right ? $this->redis->rPush($key, $value) : $this->redis->lPush($key, $value);
    }

    /**
     * 数据出队列
     * @param string $key KEY名称
     * @param bool $left 是否从左边开始出数据
     * @return mixed
     */
    public function pop($key , $left = true) {
        $val = $left ? $this->redis->lPop($key) : $this->redis->rPop($key);
        return json_decode($val);
    }

    /**
     * 数据自增
     * @param string $key KEY名称
     * @return int
     */
    public function increment($key) {
        return $this->redis->incr($key);
    }

    /**
     * 数据自减
     * @param string $key KEY名称
     * @return int
     */
    public function decrement($key) {
        return $this->redis->decr($key);
    }

    /**
     * key是否存在，存在返回ture
     * @param string $key KEY名称
     * @return bool
     */
    public function exists($key) {
        return $this->redis->exists($key);
    }

    /**
     * 返回redis对象
     * redis有非常多的操作方法，我们只封装了一部分
     * 拿着这个对象就可以直接调用redis自身方法
     */
    public function redis() {
        return $this->redis;
    }
}
