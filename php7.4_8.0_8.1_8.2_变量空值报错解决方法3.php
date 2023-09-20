<?php

//php7.4  8.0   8.1    8.2

//变量空值报错，假空值变量报错解决方法

//变量空值，假空值变量，解决方案

//用empty()判断执行

//先声明空值变量，或假空值变量

//如果报错就用



//用empty()判断执行



$ivar1=0;  //0或空值
$istr1='Runoob';
if (empty($ivar1))
{
    echo '$ivar1' . " 为空或为 0。" . PHP_EOL;
}
else
{
    echo '$ivar1' . " 不为空或不为 0。" . PHP_EOL;
}


//输出结果

//  输出结果  $ivar1 为空或为 0。



echo  "<br>";



//先声明空值变量，或假空值变量

//如果报错就用


//用empty()判断执行





$ivar1="假空值变量";//可变值
$istr1='Runoob';
if (empty($ivar1))
{
    echo '$ivar1' . " 为空或为 0。" . PHP_EOL;
}
else
{
    echo '$ivar1' . " 不为空或不为 0。" . PHP_EOL;
}




 //  输出结果   $ivar1 不为空或不为 0。
 
 
 
/ *
 php7.4 假空值变量报错，但可以往下执行
 
 程序没有被终止，
 
 
 php8.0及以上8.1___   8.2.6，假空值变量报错，无法向下执行，程序终止。
 
 
 https://www.runoob.com/php/php-empty-function.html
 
 */


 
 ?>





