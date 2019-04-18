1、php形参标量声明类型
```php
<?php    
    function sumNums(int ...$ints){
        return array_nums($ints);
    }
    var_dump(sumNums(2,'3',4.1))
```
2、新增了返回值类型声明
```php
<?php   
   function arraysum(array ...$array):array{
           return array_map(function(array $array): int {
               return array_sum($array);
           }, $arrays);
   }
```
3、合并运算符
```php
<?php 
   $_GET['user'] ?? 'nobody';
```
4、太空船操作符
```php
 <?php 
    echo 1 <=> 1 ; // 0
    echo 1 <=> 2 ; // -1
    echo 2 <=> 1 ; // 1
```
5、通过 define() 定义常量数组
```php
<?php 
   define('ANIMALS',['dog','cat','bird'])
```
6、new实例化一个匿名类
```php
<?php 
    interface Logger{
        public function log(string $msg);
    }
    
    class Application {
        private $logger;
        public function getLogger():logger{
            return $this->$logger;
        }
        
        public function setLogger(Logger $logger){
            $this->logger = $logger;
        }
    }
    $app = new Application();
    $app->setLogger(new class implements Logger{
        public function log(string $msg){
            echo $msg;
        }
    });
    var_dump($app->getLogger());
```
7、unicode codepoint 转义语法
```php
<?php 
    echo "\u{aa}";
```
8、closure::call
> 该方法拥有更好的性能，绑定一个方法到对象上，并闭包调用他。
```php
<?php
    class A { private $x = 1;}
    
    $getX = function () {return $this->x;};
    echo $getX->call(new A);
```
9、为unserialize()提过过滤
```php
<?php
    // 将所有的对象都转换为 __PHP_Incomplete_Class 对象
    $data = unserialize($foo, ["allowed_classes" => false]);
    
    // 将除 MyClass 和 MyClass2 之外的所有对象都转换为 __PHP_Incomplete_Class 对象
    $data = unserialize($foo, ["allowed_classes" => ["MyClass", "MyClass2"]);
    
    // 默认情况下所有的类都是可接受的，等同于省略第二个参数
    $data = unserialize($foo, ["allowed_classes" => true]);
```
10、IntlChar 
>新增的intlchar 类为了操作多字符集的 unicode 字符
```php
<?php
    printf('%x', IntlChar::CODEPOINT_MAX);
    echo IntlChar::charName('@');
    var_dump(IntlChar::ispunct('!'));
```
11、预期增加assert断言功能
```php
<?php
    ini_set('assert.exception',1);
    class CustomerError extends AssertionError{}
    assert(false,new CustomerError('some error message'));
```
12、use加强
```php
<?php
    use some\namespace\{ClassA,ClassB};
```
13、Generator加强
```php
    <?php
    
    function gen()
    {
        yield 1;
        yield 2;
    
        yield from gen2();
    }
    
    function gen2()
    {
        yield 3;
        yield 4;
    }
    
    foreach (gen() as $val)
    {
        echo $val, PHP_EOL;
    }
```
13、整除 新增了整除函数 intdiv(),使用实例：
````php
<php 
    var_dump(intdiv(10, 3));
````