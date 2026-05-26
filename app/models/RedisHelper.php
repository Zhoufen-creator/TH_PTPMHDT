<?php

class RedisService {
    private $redis;
    private $host = "127.0.0.1";
    private $port = 6379;
    private $timeout = 0;

    public function __construct($host = null, $port = null) {
        $this->host = $host ?? $this->host;
        $this->port = $port ?? $this->port;
        $this->connect();
    }

    private function connect() {
        $this->redis = new Redis();
        try {
            $this->redis->connect($this->host, $this->port, $this->timeout);
            $this->redis->ping();
        } catch (Exception $e) {
            throw new Exception("Redis connection failed: " . $e->getMessage());
        }
    }

    public function getRedis() {
        return $this->redis;
    }

    public function set($key, $value, $expiry = null) {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        
        if ($expiry) {
            return $this->redis->setex($key, $expiry, $value);
        }
        return $this->redis->set($key, $value);
    }

    public function get($key) {
        $value = $this->redis->get($key);
        if ($value !== false) {
            $decoded = json_decode($value, true);
            return $decoded !== null ? $decoded : $value;
        }
        return null;
    }

    public function delete($key) {
        return $this->redis->del($key);
    }

    public function exists($key) {
        return $this->redis->exists($key);
    }

    public function flush() {
        return $this->redis->flushDB();
    }

    public function isConnected() {
        try {
            return $this->redis->ping() === '+PONG';
        } catch (Exception $e) {
            return false;
        }
    }
}