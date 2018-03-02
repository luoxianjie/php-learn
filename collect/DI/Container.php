<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/12
// | Time  : 15:40
// +----------------------------------------------------------------------



/*
class A
{
    public function __construct()
    {
        $b = new B();
        echo __METHOD__."<br/>";
    }
}

class B
{
    public function __construct()
    {
        echo __METHOD__."<br/>";
    }
}

$a = new A();
*/

/*****************************************************************************************************/

/*
class A
{
    public function __construct(C $c)
    {
        $c->hello();
        echo __METHOD__."<br>";
    }
}


class B implements C
{
    public function __construct()
    {
        echo __METHOD__."<br>";
    }

    public function hello()
    {
        echo "hello<br>";
    }
}

interface C
{
    public function hello();
}

$b = new B();

$a = new A($b);

*/

/******************************************************************************************************/



class A
{
    public function __construct(C $c)
    {
        $c->hello();
    }
}


class B implements C
{
    public function __construct()
    {
    }

    public function hello()
    {
        return "hello";
    }
}

interface C
{
    public function hello();
}




/*class Container
{
    protected $binds = [];

    protected $instances = [];

    public function bind($abstract,$concrete)
    {
        if($concrete instanceof Closure){
            $this->binds[$abstract] = $concrete;
        }else{
            $this->instances[$abstract] = $concrete;
        }
    }

    public function make($abstract,$parameters = [])
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        array_unshift($parameters, $this);

        return call_user_func_array($this->binds[$abstract], $parameters);
    }

}

$container = new Container();

$container->bind('A',function ($container,$concrete){
   return new A($container->make($concrete));
});

//$container->bind('B',function ($containter){
//   return new B();
//});

//$b = new B();
//
//$container->bind('B',$b);

//$a = $container->make('A',['B']);

class D
{
    public $name1;

    public $name2;

    public static function fun2()
    {
        return 234;
    }

    public function test()
    {
        $data = [];
        array_unshift($data,$this);
        var_dump($data);
    }

}

$d = new D();

call_user_func_array(function($b){
    return new A($b);
},[new B()]);*/


class Container
{

    public $binds = [];
    public $instances = [];

    public function bind($abstract,$concrete)
    {
        if($concrete instanceof Closure){
            $this->binds[$abstract] = $concrete;
        }else{
            $this->instances[$abstract] = $concrete;
        }
    }

    public function make($abstract,$parameters = [])
    {
        if(isset($this->instances[$abstract])){
            return $this->instances[$abstract];
        }
        array_unshift($parameters,$this);

        return call_user_func_array($this->binds[$abstract],$parameters);
    }

}

$container = new Container();

$container->bind('B',function(){
   return new B();
});

$container->bind('A',function($container,$abstract){
    return new A($container->make($abstract));
});

$container->make('A',['B']);

abstract class Facade
{
    public static $instance = [];
    public static function getFacadeAccessor(){
       return ;
    }

    public static function __callStatic($method,$args){

        $instance = static::getFacadeRoot();

        if(! $instance){
            die('error');
        }

        return $instance->$method($args);
    }

    public static function getFacadeRoot()
    {
        $name = static::getFacadeAccessor();

        if(isset(static::$instance[$name])){
            return static::$instance[$name];
        }

        return static::$instance[$name] = new $name();
    }

}


class BFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return "B";
    }
}

class_alias('BFacade','BF');

$res = BF::hello();

var_dump($res);