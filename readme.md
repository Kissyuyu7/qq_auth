### QQ互联网页授权
- 配置
```php
$config = [
    'appId'       => '你的APPID',
    'appKey'      => '你的APPKEY',
    'callbackUrl' => '你的授权回调地址'
]; 
```
- 使用
```php
use kissyuyu7\QQAuth\QQAuth;
$authObj = new QQAuth($config);
```

- 获取授权链接
```php
$authObj->getAuthPageUrl('验证字符串[null]','移动端[false]');
```

- 在用户链接到授权链接 且 授权完成之后 会GET一个'code'参数到回调地址[验证字符串也会一起GET]
- 获取用户信息
```php
$userInfo = $authObj->getUserInfoByAuthCode(回调回来的code);
```

- kissyuyu7@live.cn