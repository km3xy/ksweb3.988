PHP 8.2 新特性 — 新增 Random 扩展

PHP 8.2 引入一个新的 PHP 扩展叫做 Random 扩展, 这个扩展合并了已有的随机数生成功能，并引入一些新的 PHP 类和异常类，用来提供随机数生成器和细粒度异常处理。

Random 扩展与 PHP 捆绑，不能使用编译时和运行时配置禁用此扩展。Random 扩展在 PHP 8.2 以上版本将会一直可用。

现有的随机数生成功能的修改不会导致跨版本的向下兼容性问题。不过，需要注意的是，检测随机数函数的 PHP 应用和工具(比如，反射API 中的 ReflectionFunction 或 ReflectionFunction )在PHP8.2会产生不同结果。不过这种场景比较罕见，不太可能在常规的 PHP 应用中出现问题。

现有随机数函数移动到了新增的 Random 扩展中
PHP 在它的标准库中有多个生成随机数的函数。在 PHP 8.2 中，一些函数被移到了 random 扩展。所有这些函数继续常驻在全局命名空间。因为编译PHP时 random 扩展始终包含于其中，因此在实际使用中应该不会有不同。

以下这些函数和厂商会被移到 random 扩展。

random_bytes 函数
random_int 函数
rand 函数
getrandmax 函数
srand 函数
lcg_value 函数
mt_rand 函数
mt_getrandmax 函数
mt_srand 函数
MT_RAND_PHP 厂商
MT_RAND_MT19937 常数
虽然以上这些函数的功能并没有改变(除了使用新的 random 方面的 Exception 和 Error 异常之外)，这些改变在反射 API 中是可以观察到的。从 PHP 8.2 开始，ReflectionFunction::getExtension 返回 random 扩展而非之前的 standard 标准扩展：

 $reflector = new ReflectionFunction('random_int');
 $reflector->getExtension()->getName();
- // "standard"
+ // "random"
新增 \Random 命名空间
按照 PHP 采用的命名空间政策，所有在 random 扩展引入的新功能被添加到名 \Random  的命名空间中。

\Random 命名空间为该扩展所保留。已有的 PHP 项目中如果已经用了 \Random 命名空间，只要不和 PHP 8.2 中新增的类、接口、异常产生冲突即可继续使用。

新增 Random\Randomizer 类
Random\Randomizer 是 random 扩展新增的类中最重要的一个。 它提供了一个面向对象的 API，用来获取所有随机数生成功能，它使用了伪随机数生成器算法。

下面是使用新类生成随机数的一个例子。

$r = new Random\Randomizer();
echo $r->getInt(1, 100); // 42
echo $r->shuffleBytes('lorem ipsum'); // "ols mpeurim"
更详细的例子，查看用例区。

Random\Randomizer 概要

namespace Random;

final class Randomizer {

    public readonly Engine $engine;

    public function __construct(?Engine $engine = null) {}

    public function nextInt(): int {}

    public function getInt(int $min, int $max): int {}

    public function getBytes(int $length): string {}

    public function shuffleArray(array $array): array {}

    public function shuffleBytes(string $bytes): string {}

    public function pickArrayKeys(array $array, int $num): array {}

    public function __serialize(): array {}

    public function __unserialize(array $data): void {}

}
随机数生成器引擎
PHP 8.2 添加了对多种伪随机生成器算法(PRNG 引擎)的支持。Random\Randomize 接收所有 PRNG 实例，提供了面向对象接口用来使用给定的 PRNG 引擎去生成随机数和随机字节、洗牌数组及从数组中随机选择元素。

Random 扩展提供了所有 PRNG 引擎类都必须执行的 \Random\Engine 接口。此外，还有一个 \Random\CryptoSafeEngine 接口也继承了 \Random\Engine 接口，用于加密操作的安全。

namespace Random;

interface Engine {
    public function generate(): string;
}

interface CryptoSafeEngine extends Engine {
}
Random 扩展提供了4个 \Random\Engine 内建实例. 所有这些实现都是 final 类。

Random\Engine\Mt19937
Random\Engine\Mt19937 是 PHP 内建类，实现了 Random\Engine 接口。它于 mt_rand 函数一样都使用了同一个生成器 Mersenne Twister Random Number Generator。它同时也能在类初始化时使用随机值提供算法种子。该引擎不适合于加密安全应用，因为 RNG 的内部状态可以用连续观察输出值获得。 

Random\Engine\Mt19937 概要

namespace Random\Engine;

final class Mt19937 implements \Random\Engine {
    public function __construct(int|null $seed = null, int $mode = MT_RAND_MT19937) {}
    public function generate(): string {}
    public function __serialize(): array {}
    public function __unserialize(array $data): void {}
    public function __debugInfo(): array {}
}
Random\Engine\PcgOneseq128XslRr64
Random\Engine\PcgOneseq128XslRr64 类提供了一个置换同余的生成器实现。该引擎不适用于加密安全应用。

Random\Engine\PcgOneseq128XslRr64 概要

namespace Random\Engine;

final class PcgOneseq128XslRr64 implements \Random\Engine {
    public function __construct(string|int|null $seed = null) {}
    public function generate(): string {}
    public function jump(int $advance): void {}
    public function __serialize(): array {}
    public function __unserialize(array $data): void {}
    public function __debugInfo(): array {}
}
Random\Engine\Xoshiro256StarStar
Random\Engine\Xoshiro256StarStar 类提供了一个 Xoshiro PRNG 的 PHP 实现。虽然 Xoshiro256StarStar 引擎不适用于加密安全应用，因为简单它是提供的这些引擎中最快的，使之成为需要随机数据池的应用的理想候选类(比如随机幻灯片)。

Random\Engine\Xoshiro256StarStar 概要

namespace Random\Engine;

final class Xoshiro256StarStar implements \Random\Engine {
    public function __construct(string|int|null $seed = null) {}
    public function generate(): string {}
    public function jump(): void {}
    public function jumpLong(): void {}
    public function __serialize(): array {}
    public function __unserialize(array $data): void {}
    public function __debugInfo(): array {}
}
Random\Engine\Secure
Random\Engine\Secure 类提供了与 random_bytes 及 random_int 函数相同的实现，推荐用于包含加密操作安全的所有随机数生成。

Random\Engine\Secure 概要

namespace Random\Engine;

final class Secure implements \Random\CryptoSafeEngine {
    public function generate(): string {}
}
新增 Exception 和 Error 类型
作为 ranom 扩展引入和更新的一部分，同时引入了新的 Exception 和 Error 类型。PRNG 引擎和 Random\Randomizer 类，以及现有的随机数生成函数在 PHP 8.2 及以后的版本中都会抛出更细粒度的异常和错误。

namespace Random;

class RandomError extends \Error {}

class BrokenRandomEngineError extends RandomError {}

class RandomException extends \Exception {}
继承自 PHP 异常
Lists all the current PHP exceptions and errors in a hierarchical order.

已有的 PHP 函数抛出细粒度异常
从 PHP 8.2 起，random_int、random_bytes、及其他现有函数可以抛出 RandomError、 BrokenRandomEngineError 和 RandomException 异常。不过，这不会引入任何向下兼容性问题，因为这些新的异常和错误类型继承自基础的错误和异常类。

用例
下例中使用了  \Random\Randomizer 类及多个引擎，以及一些替代模式

生成 1 到 100 之间的随机数

$randomizer = new Random\Randomizer();
$randomizer->getInt(1, 100); // 42
打乱字符

$randomizer = new Random\Randomizer();
$randomizer->shuffleBytes('abcdef'); // baecfd
请注意 shuffleBytes 方法并非完全如其名所示的那样: 打乱字节(shuffle the bytes)。对多字节字符，会产生乱码/扭曲文本。

打乱数组

$randomizer = new Random\Randomizer();
$randomizer->shuffleArray(['apple', 'banana', 'orange']); // ['orange', 'apple', 'banana']
使用 Mt19937 引擎

$randomizer = new Random\Randomizer(new Random\Engine\Mt19937());
$randomizer->getInt(1, 100); // 68
携带种子使用 Mt19937 引擎

$randomizer = new Random\Randomizer(new Random\Engine\Mt19937(42));
$randomizer->getInt(1, 100); // 43
携带种子使用 Xoshiro256StarStar 引擎

$randomizer = new Random\Randomizer(new Random\Engine\Xoshiro256StarStar(hash("sha256", "some seed value")));
$randomizer->getInt(1, 100); // 43
携带种子使用 PcgOneseq128XslRr64 引擎

$randomizer = new Random\Randomizer(new Random\Engine\PcgOneseq128XslRr64(hash("md5", "some seed value")));
$randomizer->getInt(1, 100); // 43
使用一个返回相同值的模拟引擎，用于单元测试 

class XKCDRandomEngine implements \Random\Engine {
    public function generate(): string {
        return \pack('V', 4); // Chosen by fair dice roll.
                              // Guaranteed to be random.
    }
}
$randomizer = new Random\Randomizer(new XKCDRandomEngine());
$randomizer->getInt(0, 100); // 4
替换 random_bytes 函数调用

- $randomValue = random_bytes(32); // Retrieves 32 random bytes.
+ $randomizer = new Random\Randomizer();
+ $randomizer->getBytes(32);
向前兼容性影响
这是一个新的扩展。现有的应用在 PHP 8.2 以上的版本种应该不会因为 random 扩展出现问题。

不过，使用新的 Exception 和 Error 类时请慎重，因为老版本在某些情况下会抛出更通用的异常。

可以将部分老版本的功能暂时放在 polyfill 中，不过 PcgOneseq128XslRr64 和 Xoshiro256StarStar 将很难使用这样的方法安全更新，因为依赖的 PRNG 模仿起来十分困难。比如，PcgOneseq128XslRr64 是128 位的无符号引擎，使用 64 位整数很难效仿。 

 

PHP 8.2
PHP
相关推荐：
支持Laravel TALL 栈的 Toast 通知扩展包
控制器和闭包路由中的原始类型
PHP 8.0 新特性： WeakMap 类
PHP 8.2: Mbstring: Base64、Uuencode、QPrint 和 HTML Entity 编码弃用
PHP 7.4 新语法：箭头函数 Arrow Functions




ksweb  3.988   composer & php  控制台

使用php  命令  查看所有的模块

-m     命令


KSWEB: ---进程开始---

[PHP Modules]
bcmath
calendar
Core
ctype
curl
date
dom
exif
fileinfo
filter
ftp
gd
gettext
hash
iconv
intl
json
ldap
libxml
mbstring
mysqli
mysqlnd
openssl
pcre
PDO
pdo_mysql
pdo_sqlite
Phar
random
Reflection
session
SimpleXML
soap
sockets
SPL
sqlite3
standard
tokenizer
xml
xmlreader
xmlwriter
zip
zlib

[Zend Modules]


KSWEB: ---进程结束---


PHP 8.2 新特性 — 新增 Random 扩展

 
PHP 8.2 引入一个新的 PHP 扩展叫做 Random 扩展, 

这个扩展合并了已有的随机数生成功能，

并引入一些新的 PHP 类和异常类



，用来提供随机数生成器和细粒度异常处理。



http://www.tubring.cn/articles/186

















