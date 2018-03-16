<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/8
// | Time  : 11:13
// +----------------------------------------------------------------------

class Config implements ArrayAccess
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function get($key = null, $default = null)
    {
        $config = $this->config;
        if(is_null($key)){
            return $config;
        }

        if(is_array($config) && array_key_exists($key, $config)){
            return $config[$key];
        }

        foreach (explode('.', $key) as $segment){
            if(!is_array($config) || !array_key_exists($segment,$config)){
                return $default;
            }
            $config = $config[$segment];
        }

        return $config;
    }


    public function set($key, $value)
    {
        if($key == ''){
            throw new \Exception('Invalid config key.');
        }

        $keys = explode('.', $key);

        if(count($keys) == 1){
            $this->config[$keys[0]] = $value;
        }

        if(count($keys) == 2){
            $this->config[$keys[0]][$keys[1]] = $value;
        }

        if(count($keys) == 3){
            $this->config[$keys[0]][$keys[1]][$keys[2]] = $value;
        }

        return $this->config;
    }

    public function offsetExists($key)
    {
        return isset($this->config[$key]);
    }

    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function offsetUnset($key)
    {
        $this->set($key,null);
    }

}
