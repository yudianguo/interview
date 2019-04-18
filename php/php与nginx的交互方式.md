1、连接方式
>unix socket 和 tcp socket

2、解析过程
>step1:用户将http请求发送给nginx服务器(用户和nginx服务器进行三次握手进行TCP连接)
step2:nginx会根据用户访问的URI和后缀对请求进行判断
setp3:通过第二步可以看出，用户请求的是动态内容，nginx会将请求交给fastcgi客户端，通过fastcgi_pass
将用户的请求发送给php-fpm
Nginx -> FastCGI -> php-fpm -> FastCGI Wrapper -> php解析器