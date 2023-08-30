<?php
require_once 'ParserNames.php';
require_once 'persons_array.php';

echo '<pre>';
print_r(getPerfectPartner('БЕзводинских', 'Максим', 'Евгеньевич', $example_persons_array));
echo '</pre>';
