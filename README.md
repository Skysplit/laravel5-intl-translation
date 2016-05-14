#Laravel5 Intl Translator

**Laravel5 Intl Translator** uses php-intl extension to provide translation for your application.

Please mind that this package **breaks framework default behaviour for validators**.

Due to `MessageFormatter` structure and methods `Validator::replacer` method should return **array of parameters as *key-value* pair**, instead replacing placeholders in message.

Besides that `app('translator')->get($key)` always returns message in raw format (unparsed). Translated messages are returned by:

```php
app('translator')->trans($key)
app('translator')->formatMessage($locale, $message, $params)
```

# Requirements
- Laravel 5.*
- php-intl extension installed

# Installation

If you do not have **php-intl** extension you can install it by following command (Ubuntu, Debian)
```bash
$ sudo apt-get install php-intl
```

If you have other OS, you can use it's respective package manage


---

```bash
composer require skysplit/laravel5-intl-translation
```

In your `config/app.php` providers  
Remove line
```php
Illuminate\Translation\TranslationServiceProvider::class,
```

And add line:
```php
Skysplit\Laravel\Translation\ServiceProvider::class,
```


## Publishing config and language files
> Be careful! This will override your existing `resources/lang/{lang}` files!
```bash
php artisan vendor:publish --provider="Skysplit\Laravel\Translation\ServiceProvider" --force
```

If you would like to publish only config
```bash
php artisan vendor:publish --provider="Skysplit\Laravel\Translation\ServiceProvider" --tag=config
```

If you would like to publish only one language files set
```bash
php artisan vendor:publish --provider="Skysplit\Laravel\Translation\ServiceProvider" --force --tag="lang.{locale}[,lang.{other_locale}]"
```
---
### Currently available locales
|Locale|Published files|
|-|-|
|**en**|`auth.php`, `validation.php`|

# Usage examples

Both `trans()` and `transChoice()` helper functions use this translator, so the only thing you have to change is your language files.

For detailed documentation please visit php's [MessageFormatter](http://php.net/manual/en/class.messageformatter.php) docs

## Placeholders
`app/resources/lang/en/custom.php`
```php
return [
	'placeholder' => 'Hello there, {username}!'
]
```

`view.blade.php`
```php
{{ trans('custom.placeholder', ['username' => 'Jane']); }}
```

Returns

```text
Hello there, Jane!
```

## Select
`app/resources/lang/en/custom.php`
```php
return [
	'select' => '{gender, select, male{He} female{She} other{It}} has two legs and is {gender}!'
]
```

`view.blade.php`
```php
{{ trans('custom.select', ['gender' => 'male']); }}
{{ trans('custom.select', ['gender' => 'female']); }}
{{ trans('custom.select', ['gender' => 'penguin']); }}
```

Returns

```text
He has two legs and is male!
She has two legs and is female!
It has two legs and is penguin!
```

## Plurals
`app/resources/lang/en/custom.php`
```php
return [
	'plural' => 'Jon has {n, plural, =0{no apples} one{# apple} other{# apples}}'
]
```

`view.blade.php`
```php
{{ transChoice('custom.plural', 0); }}
{{ transChoice('custom.plural', 1); }}
{{ transChoice('custom.plural', 2); }}
```

Returns
```
Jon has no apples
Jon has 1 apples
Jon has 2 apples
```

Instead of `transChoice()` you can you use `trans()` helper as well.

`resources/lang/en/custom.php`
```
return [
	'custom.plural' => 'Jon has {0, plural, =0{no apples} one{# apple} other{# apples}}, {grapes, plural, =0{no grapes} one{# grape} other{# grapes} and {oranges, plural, =0{no oranges} one{# orange} other{# oranges}}'
];
```

`view.blade.php`
```php
{{ trans('custom.plural', [3, 'grapes' => 1, 'oranges' => 0]) }}
```

As you can see, the only thing `transChoice()` do is passing first argument as `n` parameter to `trans()` helper.


## Plural offset
TBD

## Ordinal
TBD

## Spellout
TBD

For more details about pluralization please visit [CLDR Plural Rules](http://cldr.unicode.org/index/cldr-spec/plural-rules) specificaton and [CLDR Language plural rules](http://www.unicode.org/cldr/charts/latest/supplemental/language_plural_rules.html).

## Escaping characters
TBD

## Number formatting
TBD

## Date and time formatting
TBD
