### Payment

> 对阿里、银联以及后期将集成的微信支付接口的抽象

## 使用方法

### 1.开发环境

如果您想对payment项目进行新功能的添加并测试，那么您需要熟悉使用phpunit。

在命令行下：

    composer install;
    
    ./vendor/bin/phpunit tests/MathTest.php

如何成功出现以下信息：

    sh-3.2# ./vendor/bin/phpunit tests/MathTest.php 
    PHP Warning:  PHP Startup: Unable to load dynamic library '/Library/Php_extension/xdebug.so' - dlopen(/Library/Php_extension/xdebug.so, 9): image not found in Unknown on line 0
    PHPUnit 4.8.35 by Sebastian Bergmann and contributors.
    
    .
    
    Time: 43 ms, Memory: 4.00MB
    
    OK (1 test, 1 assertion)
    
那么恭喜您可以开始使用phpunit进行测试。