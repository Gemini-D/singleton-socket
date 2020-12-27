# 适配于单进程的协程安全 Socket 单例

基于 [#3026](https://github.com/hyperf/hyperf/discussions/3026) 实现

一般来说 Swoole 下一个 socket 只能在一个协程写，一个协程读。

当然大多数情况下直接使用连接池就可以了，不用太关心这个限制。

但是，在需要保持事务、http2等特殊情况下，是必须使用一个连接的。

通过一层封装我们可以使单一连接变得协程安全。

## 安装

```
composer require gemini/singleton-socket
```
