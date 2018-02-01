<?php
return [

    //路由接口状态
    'url_route_on' => true,
    'url_route_must'  =>  false,

    //模板参数替换
    'view_replace_str' => array(
        '__CSS__' => '/FlowProject/Food_test/public/static/admin/css',
        '__JS__'  => '/FlowProject/Food_test/public/static/admin/js',
        '__IMG__' => '/FlowProject/Food_test/public/static/admin/images',
        '__CS__'  => '/FlowProject/Food_test/public/static/css',
        '__JSS__'  => '/FlowProject/Food_test/public/static/js',
        '__IMAGE__'  => '/FlowProject/Food_test/public/static/images',
        '__PLUG__'  => '/FlowProject/Food_test/public/static/plugins',
        '__js__'   => '/FlowProject/Food_test/public/statisc/js',
        '__jss__'   => '/FlowProject/Food_test/public/javascript/js',
        '__css__'   => '/FlowProject/Food_test/public/statisc/css',
        '__img__'   => '/FlowProject/Food_test/public/statisc/img',
        '__lay__'   => '/FlowProject/Food_test/public/statisc/layui',
        '__font__'   => '/FlowProject/Food_test/public/statisc/Font-Awesome/css',
        '__common__'   => '/FlowProject/Food_test/public/statisc/layui/lay/modules/extendplus',
        '__PAY__'  =>   '/FlowProject/Food_test/public/javascript/alipay',
        '__UPDATE__' => '/FlowProject/Food_test/public/javascript/update',
    ),

    //微信 开发配置
    'WEIXINPAY_CONFIG'  => array(
        'APPID'              => 'wxdxxxxxxx89', // 公众号APPID 微信支付APPID
        'MCHID'              => '1xxxxxxx2', // 微信支付MCHID 商户收款账号
        'KEY'                => 'MCxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxlW', // 微信支付KEY
        'APPSECRET'          => 'exxxxxxxxxxxxxxxxxxxxxxxxx7', // 公众帐号secert (公众号支付专用)
        'NOTIFY_URL'         => 'http://www.xxx.com/m/cartpay/notify_wx', // 接收支付状态的连接
        'TOKEN'             => 'zmxxx',//公众号设置的token值
    ),

    //支付宝PC端 配置文件
    'ALIPAY_CONFIG' => array (
        'gatewayUrl'            => 'https://openapi.alipay.com/gateway.do',//支付宝网关（固定)'
        'appId'                 => '2017071407755988',//APPID即创建应用后生成
        //由开发者自己生成: 请填写开发者私钥去头去尾去回车，一行字符串
        'rsaPrivateKey'         =>  'MIIEpAIBAAKCAQEAuWGULK44w16nbkHvhIZRdTxXPb/EoB4Lp/AK+7Si9imnA5+auVjUg+g479D16j3t3bKuuEK0RA/j4PU9GaQTaudH9YUVZe9GbGuePLi0IjinfZ+cMEGrt652nl0V5olYcUOunMIAE3tgZJ3SDpNpPa/JC7vdyNWb2ThcOpaE7VBl7AMdoqLnybPOLZr75pCmVFr0aVx8THqRjVzI4dNvoFtm0gSRb9dPIkuq0zWwDi7HaUKaiAiHorhSngWK/+GvaFVfFaGzcGQPyX/Yu5yGe+v62Ckb/aPeglwV/VWwKWHrdyLOWjADz/TJAMpGBfi8WynoBMCWu3yISUcidWzTwQIDAQABAoIBAQCV5sOshYy83lNBIuwNG5fO1uYstl7GosUGHWKkOmLz7qZwlaTFb4EY0LUJpszThxRLCuuhb77YsoiwJV4s17Ij5bUYYW72KE+n6DXiH/D5Ixq8yE3B77htJRQh5l+JNVJg9DvD4lOxbTh64+sxgh29leFaLaWfeqs1MQ/TCFeglNqX+FgwBEiuAAdegYJWfg4JVJduaWc41IO6Yt3ybDKt6Mzrm2QU/R8yY+KcB6PccG4duFjngWwtvQxlAmNlHIklIUj488nZHI/j8lR2B1IzvxDMoFctzqbpaiTfcAJ2NrWjz/Uf8JZE+LyQ6PB9MhTIN1fz7APfEQDD0o7q+peBAoGBAO9IbeU0HsADbh877506dZ01vCyzxlXP/w44/tB2XZNoaqySIhdlU6L9DMuEk/4jganSUA/CuLTJ3I2c7A+JtpRnYNcPicbuXLL0R6TkwP6HcgkcgIJrgOoEY43H18fJCzUNSj3DScJZjUKMFB5KTnrpdfCBugNG1lCg4TjOaphJAoGBAMZVHhf/FBug79Wnqv+kXWhJvvpl8Weae4yIV8kShCTrqmDcEg0imDY6xpn6Er6QLQLu03BT+LUpVSVeFmEFQ8eWlJA+nACAuPR3m8WKzbHaBGF/mO4PSEsMgtWqv7PsUXAnGMey8DwpFqRA6zWRUOqHMvxCV6CINaSMWqdMtI+5AoGBAKE7rNFEzXOU3B8vw8WuHpJQIvv8JyirfrGWthzBpUwuj7K4xT/+htzMplJ8gzw7sKM9k7ULAGrRmkvR8T6HDP/yqM5BKhVhiBUNhTkW6vSXT7/T9MPsrHEDhBGFF2EHlwWobt4vOQQ8U3MD8Ip4h2lK5q2ovC55DPBHd3tPm4QBAoGAOX43KRGijJfMrozaPx0wjqjCGegvYK8hnY1wlTMu510BmO5ytSgQI7hKFhyuDlvREIaW+Hr/H9UJf7mUly9lhJgcHOxpYKPv+b4qg8tG8YWXu4O3m1sGw+quiq3iwI+C1BYhUtKGXYozrA29oVusry/eEc5RhJG3qymcpd7QgNECgYBzYhxDVjyxXg3fuZf7qPVoMPrNpZ2TMZL8sUAVHm3B2SGvxO+PPeKTYiGR7qNovi35Y/Cm8oeY94TwEeNxLLy3LwH54eBm97oKMUbIsKDwpPnDeSf+QpTahxHew1amEcJblXTEEWOy8RQiI0JdhWz74c+q//WAKaEHAJM8WNhQHA==',
        //支付宝公钥，由支付宝生成: 请填写支付宝公钥，一行字符串
        'alipayrsaPublicKey'    =>  'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0d8F7BszxGGIwkp446D0Y1HBC0keFDyFkRgGmFVfy5iCRAryfJ/YMPI08PMV4SREWzp5VIJEX7CIBp+BT+vzXvTnIpZ52dJHGYwN2HtCCikxBFwUKnnsZLKGlnkXHRT958Fk9ohOfnQzvpdpqJP4ZvZdZOaS+Ce3uYzSIKsVtl3nuAp/tWi1H2ZIw4ce6fJuA9+xJBRchrfe/YXxbihqhnzMY+zHw/uPILcCRoWHGE7oGOCKuORSmUo+PEC/ctlSI/kbxc4OfmXYhk98L+VFpUELOmcQRhTndZsPHkGEwi+1fwy6pvoTtsnbUTr/0lRrcoGPsAq8v9DgRBXmCW20uQIDAQAB',
        'notifyUrl'             => 'https://h5php.xingyuanauto.com/Flow/public/index.php/admin/pay/notify_url', // 支付成功异步通知地址
        'returnUrl'             => 'http://www.zhifu.com', // WEB 支付后跳转同步地址
        'returnPcUrl'           => 'https://h5php.xingyuanauto.com/Flow/public/index.php/admin/pay/return_url', // PC端扫码支付后跳转同步地址
        'charset' => 'UTF-8',//编码格式
        'sign_type' => 'RSA2' //签名方式
    ),
];
