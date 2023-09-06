# lite-view是什么
> lite-view是基于php-fpm架构开发的一款超轻量的PHP web开发框架，它学习了laravel，YII，thinkPHP等框架易用，快速，灵活等优点。

### 为什么不直用laravel，YII等框架 
- 对于类似h5等营销活动等小项目，可能会达到上千的并发访问数，在服务器有数量只有一台的情况下laravel基本办不到，如果对性能要求更高。推荐[webman](https://github.com/walkor/webman)
- 接近原生，限制少，约定少，可定制性好


# lite-view具有以下特点

- 高性能。框架核心文件极少，接近原生PHP书写的性能
- 高复用。无需修改，可以复用绝大部分composer组件及类库。
- 超级简单易用，学习成本极低，代码书写与传统框架没有区别。

# 安装

1. `composer create-project lite-view/lite-view <项目名称>`

2. nginx 添加配置

```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

3. 初始化项目，生成配置文件

```
php assist init
```

# 启动本地调试服务

`cd public && php -S 127.0.0.1:888`

# 目录结构

```
├── app
│       ├── Common # 公共方法
│       ├── Console # 命令行
│       ├── Demo # 入门示例，可删除
│       └── Http
│               ├── Middleware # 中件间都放这里
│               └── Kernel.php # 框架核，分发请求，返回响应
├── config # 配置文件
├── public
│       ├── .htaccess # apache 服务器配置
│       └── index.php # 入口文件
├── resources
│       └── views # 视图都放这里
├── routes # 路由都都这里
│       └── web.php # 路由示例
├── composer.json # composer 配置
└── assist # 命令行助手入口
 ```

# 入门示例

参考：App\Demo\Controllers\DemoController.php

# 基础功能

## 请求

lite-view 会自动将请求 封装成一个 Visitor 对象注入到 action 方法第一个参数中，例如

```
    public function hello(Visitor $visitor)
    {
        $name = $visitor->get('name', 'lite-view');
        return ['name' => $name];
    }
```

- 通过`$visitor`对象我们能获取到请求相关的任何数据。 获取整个get数组
`$visitor->get();`
- 获取get数组的某一个值
`$visitor->get('name',$default_value);`
- 获取整个get数组 $visitor->post(); 从post get 的集合中获取某个值。 $visitor->input(); 从post get的集合中获取部分数据。

```
// 获取 username 和 password 组成的数组，如果对应的key没有则忽略
$only = $request->only(['username', 'password']);
// 获得除了avatar 和 age 以外的所有输入
$except = $request->except(['avatar', 'age']);
```

- 获取请求路径 $visitor->currentPath(); 获取请求uri $visitor->currentUri();

## 响应

lite-view 会自动判断 action 返回的数据并输出，参考：App\Http\Kernel@response

```
    public function response()
    {
        $rsp = $this->data;
        if (!is_null($rsp) && !is_bool($rsp)) {
            if (is_string($rsp) || is_numeric($rsp)) {
                echo $rsp;
            } else {
                echo json_encode($rsp, JSON_UNESCAPED_UNICODE);
            }
        }
    }
```

## 路由和控制器

路由文件放在 routes 目录下，入口文件会自动加载路由文件

- 闭包路由

```
Route::any('test', function (Visitor $visitor) {
    return 'test';
});
```

- 类路由

```
// get 路由
Route::get('/testclass', [app\controller\IndexController::class, 'test']);
//post 路由
Route::post('/testclass', [app\controller\IndexController::class, 'test']);
// 同时指定 get 和 post 路由
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```

- 路由分组

```
Route::group(['prefix' => 'group', 'middleware' => []], function () {
    Route::get('exception', function () {
        throw new \Exception('my exception');
    });
    Route::get('error', function () {
        echo $no;
    });

    Route::group(['prefix' => 'test', 'middleware' => []], function () {
        Route::any('curl', 'App\Demo\Controllers\DemoController@curl');
        Route::any('render', 'App\Demo\Controllers\DemoController@render');
    });
});
```

控制器就是一个普通的类，如果在路由中添加了控制器的方法，lite-view 会自动将请求 封装成一个 Visitor 对象注入到控制器方法的第一个参数，例如

```
Route::get('hello', [\App\Demo\Controllers\DemoController::class, 'hello']);

# hello 方法
public function hello(Visitor $visitor)
{
    $name = $visitor->get('name', 'lite-view');
    return ['name' => $name];
}
```

## 中间件
- 中间件一般用于拦截请求或者响应。例如执行控制器前统一验证用户身份，如用户未登录时跳转到登录页面，例如响应中增加某个header头。
- 中间件的指定需要添加到路由中，中件间按添加时的顺序执行，示例
```
Route::get('/', function () {
    return 'index';
}, ['SayHello']);
```
- 中间件分为前置和后置，前置是指执行控制器方法前执行，后置是指执行控制器方法后执行
- 中间件就是一个普通的类，，只是做了一些约定如下：
  - 前置中间件约定方法名称为：handle，lite-view 会自动将请求封装成一个 Visitor 作为第一个参数，如果返回不为0，请求将会终止并输出返回信息
  - 后置中间件约定方法名称为：after，lite-view 会自动将请求封装成一个 Visitor 作为第一个参数，将响应作为第二个参数
- 示例：
```
class SayHello
{
    public function handle()
    {
        echo '<div style="text-align: center;height: 100px;">hello LiteView</div>';
        return 0;
    }

    public function after()
    {
        echo '<div style="text-align: center;height: 100px;">处理完成后！</div>';
    }
}
```

## 视图
视图文件是一个普通PHP代码，约定会从 resources/views 目录中加载，示例：
```
lite_view('welcome.php');
lite_view('test/test.php');
```

## 日志
lite-view 使用 monolog/monolog 处理日志。默认有一个 main 配置，如果需要自定义日志请在 config/logging.php 中添加配置 
```
# 默认的 main 
Log::employ('main')->info('info');

# 自定义配置
return [
    "default" => [
        "Handler" => \Monolog\Handler\RotatingFileHandler::class,  # 处理器
        "path" => root_path("storage/logs/run.log"),  # 日志文件的位置
        "level" => Logger::INFO, # 日志输出等级
        "processors" => [   # 为日志记录添加额外的信息
            \Monolog\Processor\MemoryUsageProcessor::class # 当前内存使用情况信息
        ]
    ]
];

# 调用
Log::info('info);
```

## 配置文件
> 配置文件分为两种
1. config.json
- 如果在 config.json 有环境配置如 `"app_env": "test"`
- 那么如果存在 config.test.json 的话会自动加载该配置文件，会自动合并，注意，配置项的字段不在重名

2. 在config/目录下PHP文件
- 一些不会根据环境变化的配置信息可以写在这，自动将文件名作为配置字段名
- 注意，配置项的字段不在重名，

- 获取所有配置
cfg()
- 获取config/logging.php里的所有配置
cfg('logging')
- 如果配置是数组，可以通过.来获取数组内部元素的值，例如
cfg('file.key1.key2');
- 默认值
cfg($key, $default);

# 数据库
> 添加配置项
在json文件中
```
    "database": {
        "mysql": {
            "driver": "mysql",
            "host": "host",
            "port": 3306,
            "username": "username",
            "password": "password",
            "dbname": "dbname",
            "charset": "utf8mb4"
        }
    },
```
在config/目录中
新建database.php 文件，添加
```
return [
    "mysql" => [
       "driver"=> "mysql",
       "host"=> "host",
       "port"=> 3306,
    ]
];
```

## 查询
```
use LiteView\SQL\Crud;
use LiteView\SQL\Connect;

# 查一条
Crud::db()->select('users', 'id > 0')->prep()->one();
# 查全部
Crud::db()->select('users', 'id > 0')->prep()->all();
# 分面
Crud::db()->select('users', 'id > 0')->prep()->paginate();
# 单个字段 
Crud::db()->select('users', 'id > 0')->prep()->column();

# 执行原生SQL
Connect::db()->prepare($sql)->fetchAll();
```

## Redis
```
use LiteView\Redis\RedisCli;

RedisCli::select(); # 不使前配置前缀，返回redis对象，和原生redis的API一至
RedisCli::usePrefix(); # 使前配置前缀，返回redis对象，和原生redis的API一至
```

# 常用组件

## 验证器
```
$rules = [
    ['type' => 'number', 'field' => 'a', 'label' => '数字', 'required' => true],
    ['type' => 'string', 'field' => 'b', 'label' => '字符串', 'required' => true],
    ['type' => 'phone', 'field' => 'c', 'label' => '电话号', 'required' => true],
    ['type' => 'enum', 'field' => 'd', 'label' => '枚举', 'required' => true, 'list'=>[1,2]],
    ['type' => 'ascii', 'field' => 'e', 'label' => 'ASCII 码', 'required' => true],
    ['type' => 'date', 'field' => 'f', 'label' => '日期', 'required' => true],
    ['type' => 'IDCard', 'field' => 'h', 'label' => '身份证', 'required' => true],
];
Validate::work($visitor->input(),$rules,$data)
```
## Excel
- 安装
`composer require lite-view/excel`
- 使用
```
$title = ['id' => '编号', 'name' => '姓名'];
$data = [['id' => '1', 'name' => '张三'], ['id' => '2', 'name' => '李四']];
Excel::export($title, $data)->down(); //浏览器中下载
Excel::export($title, $data)->save($path); //保存到本地
```


## 单元测试
- 安装
composer require --dev phpunit/phpunit
- 使用
新建文件 tests/TestConfig.php，用于测试数据库配置
```
<?php
use PHPUnit\Framework\TestCase;

class TestConfig extends TestCase
{
    public function testAppConfig()
    {
        $config = cfg();
        self::assertIsArray($config);
        self::assertArrayHasKey('debug', $config);
        self::assertIsBool($config['debug']);
        self::assertArrayHasKey('default_timezone', $config);
        self::assertIsString($config['default_timezone']);
    }
}
```

## 微信SDK
- 安装
composer require req-tencent/third-party
- 使用
```
# 实现配置类
use App\Demo\tps\LzljQdTp;
use LiteView\Curl\Lite;
use ReqTencent\Weixin\Official\Contracts\GzhApiInterface;
use ReqTencent\Weixin\ThirdParty\ThirdParty;


class GzhConfig implements GzhApiInterface
{

    private $appid;

    public function __construct($appid)
    {
        $this->appid = $appid;
    }

    public function appid()
    {
        return $this->appid;
    }

    public function secret()
    {
        // TODO: Implement secret() method.
    }

    public function get_access_token()
    {
        // token 需要存起来
    }

    public function get($api)
    {
        $rsp = Lite::request()->get($api);
        return json_decode($rsp, true);
    }

    public function post($api, $json)
    {
        $rsp = Lite::request()->post($api, $json);
        return json_decode($rsp, true);//
    }

    public function get_jsapi_ticket()
    {
        // TODO: Implement get_jsapi_ticket() method.
    }
}

# 调用
use ReqTencent\Weixin\Official\Gzh;
$userinfo = Gzh::base(new GzhConfig($appid))->user_info($openid);
```

## 微信支付SDK（V3）
- 安装
composer require req-tencent/weixin-pay
- 使和
```
# 在新建 config 目录下新建 wxpay.php
return [
    'appid' => '',
    'mchid' => '',
    'v3Key' => '',
    'sn' => '',
    'notify_url' => '',
    'private_key_path' => root_path('resources/wx_cert/001/apiclient_key.pem'),
    'wechatpay_certificate_path' => root_path('resources/wx_cert/001/wechatpay_6A88078CFF6EE0113C4166D3B61937D958D1AECB.pem'),
];

# jsapi支付下单
$pc = new PayCaller(cfg('wxpay'));
$rsp = $pc->order('jsapi', $data); // 下单，参考微信文档
$jsonString = $rsp->getBody()->getContents(); // 下单后的返回
$data = $pc->jsApiPaySign(json_decode($jsonString)->prepay_id); // 支付签名
```

# 命名的依据

- app/Common 不需要引入第三方依赖

- app/Helpers 需要引入第三方依赖

# 业务分层建议

- controller 层，在一个最简单的请求中，需要做以下动作：
    1. 接收参数
    2. 跟据参数查询数据
    3. 返回数据

> 建议 controller 层只处理以上3件事

- service 层
    1. 查询
    2. 参数验证

- Searches 层
    1. 搜索条件组装

- Formats 层
    1. 返回数据格式化

> 总的来说只要满足职责逻辑清晰，后续维护容易，就是好的分层。
