<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'Pole {attribute} musi zostać zaakceptowane.',
    'active_url'           => 'Pole {attribute} jest nieprawidłowym adresem URL.',
    'after'                => 'Pole {attribute} musi być datą późniejszą od {date,date}.',
    'alpha'                => 'Pole {attribute} może zawierać jedynie litery.',
    'alpha_dash'           => 'Pole {attribute} może zawierać jedynie litery, cyfry i myślniki.',
    'alpha_num'            => 'Pole {attribute} może zawierać jedynie litery i cyfry.',
    'array'                => 'Pole {attribute} musi być tablicą.',
    'before'               => 'Pole {attribute} musi być datą wcześniejszą od {date}.',
    'between'              => [
        'numeric' => 'Pole {attribute} musi zawierać się w granicach {min} - {max}.',
        'file'    => 'Pole {attribute} musi zawierać się w granicach {min} - {max} kilobajtów.',
        'string'  => 'Pole {attribute} musi zawierać się w granicach {min} - {max} znaków.',
        'array'   => 'Pole {attribute} musi składać się z {min} - {max} elementów.',
    ],
    'boolean'              => '{attribute} musi mieć wartość prawda albo fałsz',
    'confirmed'            => 'Potwierdzenie {attribute} nie zgadza się.',
    'date'                 => '{attribute} nie jest prawidłową datą.',
    'date_format'          => '{attribute} nie jest w formacie {format}.',
    'different'            => '{attribute} oraz {other} muszą się różnić.',
    'digits'               => '{attribute} musi składać się z {digits, plural, one{# cyfry} few{# cyfr} many{# cyfr} other{# cyfry}} .',
    'digits_between'       => '{attribute} musi mieć od {min} do {max} cyfr.',
    'distinct'             => 'Pole {attribute} nie może mieć powtarzających się wartości.',
    'email'                => 'Format pola {attribute} jest nieprawidłowy.',
    'exists'               => 'Zaznaczona opcja w {attribute} jest nieprawidłowa.',
    'filled'               => 'Pole {attribute} jest wymagane.',
    'image'                => '{attribute} musi być obrazkiem.',
    'in'                   => 'Zaznaczona opcja w {attribute} jest nieprawidłowa.',
    'in_array'             => '{attribute} nie występuje w {other}.',
    'integer'              => '{attribute} musi być liczbą całkowitą.',
    'ip'                   => '{attribute} musi być prawidłowym adresem IP.',
    'json'                 => 'Pole {attribute} musi być poprawnym JSON\'em.',
    'max'                  => [
        'numeric' => 'Pole {attribute} nie może być większe niż {max}.',
        'file'    => 'Pole {attribute} nie może być większe niż {max, plural, one{# kilobajt} few{# kilobajty} many{# kilobajtów} other{# kilobajta}}.',
        'string'  => 'Pole {attribute} nie może być dłuższe niż {max, plural, one{# znak} few{# znaki} many{# znaków} other{# znaku}}.',
        'array'   => '{attribute} nie może mieć więcej niż {max, plural, one{# element} few{# elementy} many{# elementów} other{# elementu}}.',
    ],
    'mimes'                => '{attribute} musi być plikiem typu {values}.',
    'min'                  => [
        'numeric' => 'Pole {attribute} musi być nie mniejsze od {min}.',
        'file'    => '{attribute} musi mieć przynajmniej {min, plural, one{# kilobajt} few{# kilobajty} many{# kilobajtów} other{# kilobajta}}.',
        'string'  => '{attribute} musi mieć przynajmniej {min, plural, one{# znak} few{# znaki} many{# znaków} other{# znaku}}.',
        'array'   => '{attribute} musi mieć przynajmniej {min, plural, one{# element} few{# elementy} many{# elementów} other{# elementu}}.',
    ],
    'not_in'               => 'Zaznaczone pole {attribute} jest nieprawidłowe.',
    'numeric'              => '{attribute} musi być liczbą.',
    'present'              => 'Pole {attribute} musi być obecne.',
    'regex'                => 'Format pola {attribute} jest nieprawidłowy.',
    'required'             => 'Pole {attribute} jest wymagane.',
    'required_if'          => 'Pole {attribute} jest wymagane gdy pole {other} jest równe {value}.',
    'required_unless'      => 'Pole {attribute} jest wymagane jeżeli pole {other} ma wartość wśród {values}.',
    'required_with'        => 'Pole {attribute} jest wymagane gdy pole {values} jest obecne.',
    'required_with_all'    => 'Pole {attribute} jest wymagane gdy pole {values} jest obecne.',
    'required_without'     => 'Pole {attribute} jest wymagane gdy pole {values} nie jest obecne.',
    'required_without_all' => 'Pole {attribute} jest wymagane gdy żadne z pól {values} nie są obecne.',
    'same'                 => 'Pola {attribute} i {other} muszą być takie same.',
    'size'                 => [
        'numeric' => '{attribute} musi mieć {size}.',
        'file'    => '{attribute} musi mieć {size, plural, one{# kilobajt} few{# kilobajty} many{# kilobajtów} other{# kilobajta}}.',
        'string'  => '{attribute} musi mieć {size, plural, one{# znak} few{# znaki} many{# znaków} other{# znaku}}.',
        'array'   => '{attribute} musi zawierać {size, plural, one{# element} few{# elementy} many{# elementów} other{# elementu}}.',
    ],
    'string'               => '{attribute} musi być łańcuchem znaków.',
    'timezone'             => '{attribute} musi być prawidłową strefą czasową.',
    'unique'               => 'Taka wartość pola {attribute} już występuje.',
    'url'                  => 'Format pola {attribute} jest nieprawidłowy.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'password' => "hasło",
        'email' => "adres e-mail"
    ],

];
