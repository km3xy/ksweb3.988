php8.2教程例子  Random例子 .php


https://www.php.net/manual/zh/random-randomizer.pickarraykeys.php


输入框里搜索  Random     关键词
Random      


http://php.p2hp.com/manual/en/random-randomizer.pickarraykeys.php

输入框里搜索  Random     关键词
Random





<?php
$r = new \Random\Randomizer();

$fruits = [ 'red' => '🍎', 'green' => '🥝', 'yellow' => '🍌', 'pink' => '🍑', 'purple' => '🍇' ];

// Pick 2 random array keys:
echo "Keys: ", implode(', ', $r->pickArrayKeys($fruits, 2)), "\n";

// Pick another 3:
echo "Keys: ", implode(', ', $r->pickArrayKeys($fruits, 3)), "\n";
?>





https://www.w3cschool.cn/php/dict.html




<?php
$input = array("Neo", "Morpheus", "Trinity", "Cypher", "Tank");
$rand_keys = array_rand($input, 2);
echo $input[$rand_keys[0]] . "\n";
echo $input[$rand_keys[1]] . "\n";
?>




https://www.php.net/manual/zh/function.array-rand.php







