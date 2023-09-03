<?php
require_once 'ParserNames.php';
require_once 'persons_array.php';

echo '<pre>';
print_r(getPerfectPartner('БЕзводинских', 'Ирина', 'Александровна', $example_persons_array));
echo '</pre>';
