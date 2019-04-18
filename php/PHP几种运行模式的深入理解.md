1、PHP运行模式有4种
>1、cgi 通用网关接口（Common Gateway Interface）
>2、fast-cgi 常驻（long-live）型的CGI
>3、cli 命令运行 （Command Line Interface）
>4、web 模块模式（apache等web服务器运行的模块模式）
>5、ISAPI（Internet Server Application Program Interface）
1、CGI即通用网关接口（Common Gateway Interface），他是一段程序，通俗的讲CGI就是一座桥，把网页和WEB服务器中的执行程序连接起来，
它把HTML接收的指令传递给服务器执行，再把服务器执行返回的结果返还给HTML页。很少用了。
2、FastCGI模式
FastCGI是CGI的升级版本，FastCGI像是一个常驻（long-live）型的CGI，他可以一直执行者，只要激活后，不会每次都要花费时间去Fork一次。
FastCGI是一个可伸缩的、告诉地在HTTP server和动态脚本语言间的通信的接口。多数流行的HTTP server都支持FastCGI，包括Apache、Nginx
和lighhttpd等，同事，FastCGI也被许多其他脚本🔐支持，其中就有PHP。
FastCGI接口采用C/S结构，可以将HTTP服务器和脚本解析服务器分开，同事在脚本解析服务器上启动一个或者多个脚本解析守护进程。当HTTP服务器
每次遇到动态程序时，可以将其直接交付给FastCGI进程来执行，然后将得到的结果返回给浏览器。
原理
1）Web Server启动时载入FastCGI进程管理器
2）FastCGI（Common Gateway Interface）进程管理器自身初始化，启动多个CGI解释器进程并登台来自Web Server的连接；
3）当客户端请求到达Web Server时，FastCGI进程管理器选择并连接到一个CGI解释器。Web server将CGI环境变量和标准输入发送到FastCGI子进程php-cgi
4）FastCGI子进程完成处理后将标准输出和错误信息从同一连接返回Web Server。当FastCGI进程关闭连接时，请求便告诉处理器完成。FastCGI
子进程接着等待并处理来自FastCGI进程管理器的下一个连接
备注：PHP的FastXGI进程管理器是PHP-FPM（PHP-FastXGI Process Manager）
2、CLI模式（Command Line Interface）
3、模块模式
>PHP模块通过注册apache2的ap_hook_post_config挂钩
4、ISAPI（Internet Server Application Program Interface）是微软提供的一套面向Internet服务的API接口，一个ISAPI的DLL，
可以被用户请求激活后常驻内存，等待用的另一个请求，还可以在一个DLL里设置多个用去请求处理函数，此外，ISAPI的DLL应用程序和WWW服务器
处于同一个进程中，效率高于CGI，PHP作为Apache模块，Apache服务器在系统启动后，预先生成多个进程副本驻留在内存中，一旦有请求出现，
就立即使用这些空余的子进程进行处理，这样就不存在生成子进程造成的延迟了。这些服务器副本在处理完一次HTTP请求之后并不立即退出，
而是停留在计算机中等待下次请求。对于客户浏览器的请求反应更快，性能较高。
