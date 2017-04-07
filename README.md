### Payment

> 对阿里、银联以及后期将集成的微信支付接口的抽象

## 下载使用

在您的composer.json文件中，添加以下代码

    "require": {
        "liumapp/payment":"dev-master"
    },

然后执行:

    composer require liumapp/payment

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

### 2.使用方法

#### 支付宝扫码支付
    
    $orderNo = time() . rand(1000, 9999);
    $fileName = $this->prepareFile(); //使用绝对路径
    $data = [
        'config' => [
            'use_sandbox' => \Yii::$app->settingParam->get('use_sandbox'),
            'partner' => \Yii::$app->settingParam->get('partner'),
            'app_id' => \Yii::$app->settingParam->get('app_id'),
            'notify_url' => \Yii::$app->settingParam->get('notify_url'),
            'return_url' => \Yii::$app->settingParam->get('return_url'),
            'return_raw' => \Yii::$app->settingParam->get('return_raw'),
            'ali_public_key' => \Yii::$app->settingParam->get('ali_public_key_rsa2'),
            'rsa_private_key' => \Yii::$app->settingParam->get('rsa_private_key_rsa2'),
        ],
        'data' => [
            'body' => 'pay body',
            'subject' => 'pay subject',
            'order_no' => $orderNo,
            'timeout_express' => (time() + 600),
            'amount' => 0.01,
            'scene' => 'bar_code',
            'auth_code' => '1231212232323123123',
        ],
        'fileName' => $fileName,
    ];
    $qrCode = new QrCode();
    $data['config']['sign_type'] = 'RSA2';
    $url = Charge::run('ali_qr' , $data['config'] , $data['data']);
    $qrCode
        ->setText($url)
        ->setSize(200)
        ->setPadding(10)
        ->setErrorCorrection('high')
        ->setBackgroundColor(['r' => 255 , 'g' => 255 , 'b' => 255 , 'a' => 0])
        ->setForegroundColor(['r' => 0 , 'g' => 0 , 'b' => 0 , 'a' => 0])
        ->setLabelFontSize(16)
        ->setImageType(QrCode::IMAGE_TYPE_PNG);
    $qrCode->render($fileName);
    return $this->render('ali-pay' , ['picture' => $this->codeUrl]);
    
> 上述代码中，$data表示生成订单的配置信息及订单信息，$url为通过Charge的run方法获得支付扫码支付的链接，最后在利用qrCode将$url生成一个二维码，从头到尾，用户需要调用payment项目的代码只有一句："$url = Charge::run('ali_qr' , $data['config'] , $data['data']);"

#### 支付宝支付通知回调

    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    use frontend\models\PayCallBack;
    
    $log = new Logger('ali');
    $log->pushHandler(new StreamHandler(Yii::getAlias('@frontend') . '/log/aliLog' , Logger::INFO));
    $log->addInfo('this is ali notify :' );
    
    $callback = new PayCallBack();
    $type = 'ali_charge';// xx_charge
    $config = [
        'use_sandbox' => \Yii::$app->settingParam->get('use_sandbox'),
        'partner' => \Yii::$app->settingParam->get('partner'),
        'app_id' => \Yii::$app->settingParam->get('app_id'),
        'notify_url' => \Yii::$app->settingParam->get('notify_url'),
        'return_url' => \Yii::$app->settingParam->get('return_url'),
        'return_raw' => \Yii::$app->settingParam->get('return_raw'),
        'ali_public_key' => \Yii::$app->settingParam->get('ali_public_key_rsa2'),
        'rsa_private_key' => \Yii::$app->settingParam->get('rsa_private_key_rsa2'),
        'sign_type' => 'RSA2',
    ];
    
    try {
        $ret = Notify::run($type, $config, $callback);
    } catch (\ErrorException $e) {
        $log->addError($e->getMessage());
        exit;
    }
    $log->addInfo(json_encode($ret));
    

> 上述代码中，类PayCallBack的代码如下：

    class PayCallBack extends Model implements PayNotifyInterface
    {
        public function notifyProcess(array $data)
        {
            // 执行业务逻辑，成功后返回true
            return true;
        }
    }
    
> 上述两段代码中，$ret = Notify::run($type, $config , $callback);将会根据支付类型和支付宝的相关配置，来处理回调的参数，如果monolog记录的日志为success，那就表明用户支付成功，同时调用callback的业务逻辑。

**请注意，您的订单参数中的notify_url，必须跟支付宝开放平台后台设置授权回调地址的一样，并且不能带有r=site/index这样的参数**

#### 银联支付

    use liumapp\payment\client\Charge;
     $data = [
        'config' => [
            'merId' => \Yii::$app->settingParam->get('unionMerId'),
            'sdk_sign_cert_path' => \Yii::$app->settingParam->get('unionSignCertPath'),
            'sdk_sign_cert_pwd' => \Yii::$app->settingParam->get('unionSignCertPwd'),
            'sdk_encrypt_cert_path' => \Yii::$app->settingParam->get('unionEncryptCertPath'),
            'sdk_verify_cert_dir' => \Yii::$app->settingParam->get('unionCertDir'),
        ],
        'data' => [
            'orderId' => time(),
            'txnTime' => date('YmdHmi' , time()),
            'txnAmt' => 1,
        ],
    ];
    echo Charge::run('uni_con' , $data['config'] , $data['data']);

> 封装好银联需要的配置和订单数据后，直接调用Charge的run方法并输出，即可。


#### 银联支付的通知回调
    
    use liumapp\payment\client\Notify;
    
    $log = new Logger('union');
    $log->pushHandler(new StreamHandler(Yii::getAlias('@frontend') . '/log/unionLog' , Logger::INFO));
    $log->addInfo('this is union notify:');
    
    $callback = new PayCallBack();
    $type = 'union_charge';
    $config = [
        'merId' => \Yii::$app->settingParam->get('unionMerId'),
        'sdk_sign_cert_path' => \Yii::$app->settingParam->get('unionSignCertPath'),
        'sdk_sign_cert_pwd' => \Yii::$app->settingParam->get('unionSignCertPwd'),
        'sdk_encrypt_cert_path' => \Yii::$app->settingParam->get('unionEncryptCertPath'),
        'sdk_verify_cert_dir' => \Yii::$app->settingParam->get('unionCertDir'),
        'frontUrl' => \Yii::$app->urlManager->createAbsoluteUrl(['order/index']),
        'backUrl' => \Yii::$app->urlManager->createAbsoluteUrl(['site/union-notify']),
    ];
    try {
        $ret = Notify::run($type , $config , $callback);
    } catch (\ErrorException $e) {
        $log->addError($e->getMessage());
    }
    $log->addInfo($ret);
    echo $ret;
    
> 上述代码中的PayCallBack类的代码为：

    class PayCallBack extends Model implements PayNotifyInterface
    {
        public function notifyProcess(array $data)
        {
            // 执行业务逻辑，成功后返回true
            return true;
        }
    }
    
> 创建订单的参数中，设置backUrl指向上述代码的地址即可。同时处理订单交易成功后的逻辑倾泻在notifyProcess内。