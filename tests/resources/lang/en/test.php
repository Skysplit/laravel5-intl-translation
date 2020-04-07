<?php

declare(strict_types=1);

return [
    'hello' => 'Hello, {name}!',
    'apples' => '{n, plural, =0{no apples} =1{# apple} other{# apples}}',
    'offset' => 'You {n, plural, offset:1 =0{do not like this yet} =1{liked this} one{and one other person liked this} other{and # others liked this}}',
    'select' => '{gender, select, male{He} female{She} other{It}} has two legs and is {gender}!',
];
