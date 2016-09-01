# Laravel5 Intl Translator

[![Build Status](https://travis-ci.org/Skysplit/laravel5-intl-translation.svg?branch=master)](https://travis-ci.org/Skysplit/laravel5-intl-translation)
[![Latest Stable Version](https://poser.pugx.org/skysplit/laravel5-intl-translation/v/stable)](https://packagist.org/packages/skysplit/laravel5-intl-translation)
[![Latest Unstable Version](https://poser.pugx.org/skysplit/laravel5-intl-translation/v/unstable)](https://packagist.org/packages/skysplit/laravel5-intl-translation)

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
    - [Publishing config and language files](#publishing-config-and-language-files)
        - [Currently adapted locales](#currently-adapted-locales)
- [Usage examples](#usage-examples)
    - [Placeholders](#placeholders)
    - [Select](#select)
    - [Plurals](#plurals)
        - [Plural offset](#plural-offset)
- [Formatting in details](#formatting-in-details)

# Introduction
**Laravel5 Intl Translator** uses php-intl extension to provide translation for your application.

Please mind that this package **breaks framework default behaviour for validators**.

Due to `MessageFormatter::formatMessage` method, `Validator::replacer` method should return **array of parameters as *key-value* pair**, instead replacing placeholders in message.

Besides that `app('translator')->get($key)` always returns message in raw format (unparsed). Translated messages are returned by:

```php
app('translator')->trans($key)
app('translator')->formatMessage($locale, $message, $params)
```

# Requirements
- Laravel **5.2** or **5.3**
- php-intl extension installed

Please feel free to contribute to this package for other Laravel versions support!

# Installation

If you do not have **php-intl** extension you can install it by following command (Ubuntu, Debian)
```bash
$ sudo apt-get install php-intl
```

If you have other OS, you can use it's respective package manager


---

### Laravel 5.3

```bash
composer require skysplit/laravel5-intl-translation=^2.0
```

### Laravel 5.2
```bash
composer require skysplit/laravel5-intl-translation=^1.0
```


## All versions

In your `config/app.php` providers  
Remove line
```php
Illuminate\Translation\TranslationServiceProvider::class,
Illuminate\Validation\ValidationServiceProvider::class,
```

And add line:
```php
Skysplit\Laravel\Translation\TranslationServiceProvider::class,
Skysplit\Laravel\Translation\ValidationServiceProvider::class,
```


## Publishing config and language files

> Be careful! This will override your existing `resources/lang/{lang}` files!
> Check **Currently adapted locales** table to see which files could be overriden.


```bash
php artisan vendor:publish --provider="Skysplit\Laravel\Translation\TranslationServiceProvider" --force
```

If you would like to publish only config

```bash
php artisan vendor:publish --provider="Skysplit\Laravel\Translation\TranslationServiceProvider" --tag=config
```

If you would like to publish only one language files set

```bash
php artisan vendor:publish --provider="Skysplit\Laravel\Translation\TranslationServiceProvider" --force --tag="lang.{locale}[,lang.{other_locale}]"
```

---
### Currently adapted locales

| Locale | Published files |
| --- | --- |
| **en** | `auth.php`, `validation.php` |
| **pl** | `auth.php`, `pagination.php`, `passwords.php`, `validation.php` |

# Usage examples

Both `trans()` and `trans_choice()` helper functions use this translator, so the only thing you have to change is your language files.

For detailed documentation please visit php's [MessageFormatter](http://php.net/manual/en/class.messageformatter.php) docs and links related there

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
{{ trans_choice('custom.plural', 0); }}
{{ trans_choice('custom.plural', 1); }}
{{ trans_choice('custom.plural', 2); }}
```

Returns

```
Jon has no apples
Jon has 1 apples
Jon has 2 apples
```

Instead of `trans_choice()` you can you use `trans()` helper as well.

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

Returns

```
Jon has 3 apples, 1 grape and no oranges
```

---

As you can see, the only thing `trans_choice()` do is passing first argument as `n` parameter to `trans()` helper.



### Plural offset

You can set offset for your plural rules. Consider this example:

```
You {n, plural, offset:1 =0{do not like this yet} =1{liked this} one{and one other person liked this} other{and # others liked this}}
```

Result:

```
You do not like this yet // n = 0
You liked this // n = 1
You and one other person liked this // n = 2
You and 2 others liked this // n = 3
You and 3 others liked this // n = 4

```

---

Plural rule are often very complex for languages. Intl does handle it for you.  
For example in Polish `few` rule is applied when `n % 10 = 2..4 and n % 100 != 12..14`, while `many` rule is applied  when `n != 1 and n % 10 = 0..1` or `n % 10 = 5..9` or `n % 100 = 12..14`.  
In Serbian `=1` will match when `n = 1`, but `one` will apply when `n = 1, 21, 31, 41` etc.

> Remember! You **always** have to provide `other` rule for plural translations.

For more details about pluralization please visit [CLDR Plural Rules](http://cldr.unicode.org/index/cldr-spec/plural-rules) specificaton and [CLDR Language plural rules](http://www.unicode.org/cldr/charts/latest/supplemental/language_plural_rules.html).

# Formatting in details
PHP's MessageFormatter also supports **ordinal**, **spellout**, **number**, **date**, **time** and **duration** formatting.  
For detailed information please visit this great [Yii2 Framework i18n Guide](http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html) which covers every **intl** topic wonderfully.
