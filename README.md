# lite-view

# 介绍
PHP 轻量级 web 框架

# 安装
1. `composer create-project lite-view/lite-view <myapp>`

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
    1. 参数比较多时的参数验证
    2. 参数处理比较多时，比如查询条件组装
    3. 查询比较复杂
    4. 返回数据格式化

- aide 层
    1. 参数过于多或者过于复杂
    2. 返回数据处理过于复杂
  
> 总的来说只要满足职责逻辑清晰，后续维护容易，就是好的分层。
