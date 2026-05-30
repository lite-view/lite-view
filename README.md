# lite-view

> 基于 PHP-FPM 架构的超轻量 Web 开发框架。核心文件极少，性能接近原生 PHP，却能复用几乎所有 Composer 生态。

---

## 特点

- **高性能** — 框架核心文件极少，无冗余抽象层，性能接近原生 PHP。
- **高复用** — 无需修改，即可复用几乎所有 Composer 组件及类库。
- **极简易用** — 学习成本极低，代码书写与传统框架没有区别。限制少，约定少，可定制性极佳。

---

## 安装

```bash
composer create-project lite-view/lite-view <project-name>
```

### Nginx 配置

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 启动本地调试

```bash
cd public && php -S 127.0.0.1:8880
```

---

## 目录结构

```
├── app
│   ├── Common          # 公共方法
│   ├── Console         # 命令行
│   │   ├── Commands    # 命令类
│   │   └── Kernel.php  # 命令调度
│   ├── Demo            # 入门示例（可删除）
│   └── Http
│       ├── Middleware  # 中间件
│       ├── Kernel.php    # HTTP 请求调度与响应
│       └── ExceptionManager.php
├── config              # 配置文件
│   ├── cors.php
│   └── logging.php
├── public
│   ├── .htaccess
│   └── index.php       # 入口文件
├── resources
│   └── views           # Twig / PHP 视图
├── routes
│   └── web.php         # 路由定义
├── composer.json
└── assist              # CLI 入口脚本
```

---

## 请求

框架会自动将 HTTP 请求封装为 `Visitor` 对象，注入到控制器方法的第一个参数：

```php
public function hello(Visitor $visitor)
{
    $name = $visitor->get('name', 'lite-view');
    return ['name' => $name];
}
```

### 常用方法

| 方法 | 说明 |
|---|---|
| `$visitor->get()` | 获取整个 `$_GET` 数组 |
| `$visitor->get('key', $default)` | 获取 GET 参数 |
| `$visitor->post()` | 获取整个 `$_POST` 数组 |
| `$visitor->post('key', $default)` | 获取 POST 参数 |
| `$visitor->input()` | 合并 `$_GET` + `$_POST` + JSON Body |
| `$visitor->input('key', $default)` | 从合并数组中获取值 |
| `$visitor->only(['a', 'b'])` | 仅保留指定字段 |
| `$visitor->except(['a', 'b'])` | 排除指定字段 |
| `$visitor->currentPath()` | 当前请求路径 |
| `$visitor->currentUri()` | 当前完整 URI（含 query） |

---

## 响应

框架会自动判断 Action 返回值并输出：

- `string` / `numeric` — 直接输出
- `array` / `object` — 自动 `json_encode` 并设置 `Content-Type: application/json`
- `null` / `bool` — 无输出

---

## 路由与控制器

路由文件放在 `routes/` 目录下，入口文件会自动加载。

### 闭包路由

```php
Route::any('test', function (Visitor $visitor) {
    return 'test';
});
```

### 控制器路由

```php
Route::get('/test', [app\controller\IndexController::class, 'test']);
Route::post('/test', [app\controller\IndexController::class, 'test']);
Route::any('/test', [app\controller\IndexController::class, 'test']);
```

### 路由分组

```php
Route::group(['prefix' => 'group', 'middleware' => []], function () {
    Route::get('exception', function () {
        throw new \Exception('my exception');
    });

    Route::group(['prefix' => 'test', 'middleware' => []], function () {
        Route::any('curl', 'App\Demo\Controllers\DemoController@curl');
    });
});
```

### 快捷路由

```php
// RESTful API 资源路由
Route::apiResource('users', UserController::class, $middleware);

// 自动映射控制器所有公有方法
Route::quick('admin', AdminController::class, $middleware);
```

---

## 中间件

中间件用于拦截请求或响应，例如统一鉴权、追加 Header 等。

在路由中指定，按添加顺序执行：

```php
Route::get('/', function () {
    return 'index';
}, ['SayHello']);
```

中间件是一个普通类，需实现 `handle(Visitor $visitor, \Closure $next)` 方法：

```php
namespace App\Http\Middleware;

use LiteView\Kernel\Visitor;

class SayHello
{
    public function handle(Visitor $visitor, \Closure $next)
    {
        // 前置逻辑
        $response = $next($visitor);
        // 后置逻辑
        return $response;
    }
}
```

---

## 视图

使用 Twig 模板引擎，视图文件放在 `resources/views/`：

```php
use LiteView\Kernel\View;

return View::renderTwig('welcome.twig', ['name' => 'lite-view']);
```

也支持原生 PHP 视图：

```php
return View::renderFile('test/test.php', ['time' => time()]);
```

---

## 日志

使用 `monolog/monolog`。默认提供 `main` 通道，也可在 `config/logging.php` 中自定义。

```php
use LiteView\Utils\Log;

Log::info('user login');
Log::employ('default')->error('db connection failed');
```

---

## 配置文件

### 1. `config.json`

项目根目录的主配置文件。支持环境覆盖：若 `app_env` 为 `test`，会自动加载同目录的 `config.test.json` 并合并。

> 注意：合并后字段不能重名。

### 2. `config/*.php`

不变的配置可写为 PHP 文件，文件名自动作为配置键名：

```php
// config/logging.php
return [
    'default' => [
        'handlers' => [
            new \Monolog\Handler\RotatingFileHandler(root_path('storage/logs/run.log'), 7, Logger::INFO),
        ],
    ],
];
```

### 读取配置

```php
cfg();                // 获取全部配置
cfg('logging');       // 获取 logging 配置
cfg('logging.default'); // 点号访问数组内部
cfg('key', $default); // 带默认值
```

---

## 数据库

在 `config.json` 或 `config/database.php` 中添加：

```json
{
    "database": {
        "mysql": {
            "driver": "mysql",
            "host": "localhost",
            "port": 3306,
            "username": "root",
            "password": "secret",
            "dbname": "test",
            "charset": "utf8mb4"
        }
    }
}
```

### CRUD 查询

```php
use LiteView\SQL\Crud;
use LiteView\SQL\Connect;

// 查询
Crud::db()->select('users', 'id > 0')->prep()->one();
Crud::db()->select('users', 'id > 0')->prep()->all();
Crud::db()->select('users', 'id > 0')->prep()->paginate();
Crud::db()->select('users', 'id > 0')->prep()->column();

// 原生 SQL
Connect::db()->prepare($sql)->fetchAll();
```

---

## Redis

```php
use LiteView\Redis\RedisCli;

RedisCli::select();     // 无配置前缀，返回原生 Redis 对象
RedisCli::usePrefix();  // 使用配置前缀，返回原生 Redis 对象
```

---

## CORS

在 `config/cors.php` 中配置：

```php
return [
    'paths' => ['*'],
    'allow_origins' => '*',
    'allow_methods' => 'POST, GET, OPTIONS',
    'allow_headers' => '*',
];
```

框架会在 `Kernel::dispatch()` 中自动根据当前路径调用 `cors()` 发送响应头。

---

## 异常处理

自定义异常处理器需实现 `handle(array $msg, \Throwable $exception = null)` 方法，并在 `Kernel.php` 中注册：

```php
Dispatcher::$exceptionManager = new ExceptionManager();
```

通过设置 `$exceptionManager->use = true` 来启用自定义处理。

---

## CLI 命令行

入口为项目根目录的 `assist` 文件：

```bash
php assist list          # 列出所有命令
php assist init          # 初始化配置
php assist version       # 查看版本
php assist hello:aa      # 执行自定义命令
```

自定义命令放在 `app/Console/Commands/`，继承 `App\Console\Command`：

```php
class Hello extends Command
{
    public $signature = 'hello:(aa|bb|cc)';
    public $brief = '使用示例';

    public function handle()
    {
        list($nil, $fun) = explode(':', $this->args(1));
        echo 'hello ' . $fun;
        return 0;
    }
}
```

---

## 命名约定

| 目录 | 用途 |
|---|---|
| `app/Common` | 不依赖第三方包的公共方法 |
| `app/Helpers` | 需要引入第三方依赖的辅助函数 |

---

## 业务分层建议

- **Controller** — 只做三件事：接收参数、查询数据、返回数据。
- **Service** — 业务逻辑、参数验证。
- **Searches** — 搜索条件组装。
- **Formats** — 返回数据格式化。

> 只要职责清晰、易于维护，就是好的分层。

---

## License

MIT
