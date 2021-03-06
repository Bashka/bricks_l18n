# Интернационализация

Класс _Translator_ позволяет использовать различные текстовые и числовые шаблоны 
в зависимости от используемой языковой локали для интернационализации 
приложения. По умолчанию текущая локаль определяется автоматически из системной 
константы `LC_CTYPE`, но может быть задана явно с помощью метода `locale`:

```php
use Bricks\L18n\Translator;

$t = new Translator;
$t->locale('ru_RU');
echo $t->locale(); // "ru_RU"
```

## Хранилище переводов

Для работы, класс _Translator_ использует хранилище переводов, которое может 
быть задано с помощью метода `register`. Этот метод принимает массив переводов и 
объеденяет его с хранилищем:

```php
use Bricks\L18n\Translator;

$t = new Translator;
$t->register([
  'ru_RU' => [
    'hello' => 'Привет'
  ]
]);
$t->register([
  'en_GB' => [
    'hello' => 'Hello'
  ]
]);
// Хранилище имеет вид:
// ['ru_RU' => ['hello' => 'Привет'], 'en_GB' => ['hello' => 'Hello']]
```

Конфликты, возникающие при добалении новых переводов в хранилище, разрешается 
путем переписывания старого значения новым:

```php
$t->register([
  'ru_RU' => [
    'hello' => 'Привет'
  ]
]);
$t->register([
  'ru_RU' => [
    'hello' => 'Приветстую'
  ]
]);
// Хранилище имеет вид:
// ['ru_RU' => ['hello' => 'Приветстую']]
```

## Преобразования

Для перевода текста используется метод `str`, который принимает ключ перевода и 
возвращает перевод для текущей локали:

```php
use Bricks\L18n\Translator;

$t = new Translator;
$t->register(...);
$t->locale('ru_RU');
echo $t->str('hello'); // "Привет"
$t->locale('en_GB');
echo $t->str('hello'); // "Hello"
```

В качестве второго параметра метода может быть передано значение по умолчанию, 
которое будет возвращено в случае отсутствия в хранилище искомого ключа:

```php
use Bricks\L18n\Translator;

$t = new Translator;
echo $t->str('hello', 'Default'); // "Default"
```

Допускается использование в качестве перевода шаблона, доступного функции 
`sprintf`. В этом случае ключ перевода должен начинаться с символа `%`:

```php
use Bricks\L18n\Translator;

$t = new Translator;
$t->register([
  'en_GB' => [
    '%user_not_found' => 'User [%s] not found'
  ]
]);
$t->locale('en_GB');
echo $t->str('%user_not_found', '', 'foo'); // "User [foo] not found"
```

Методы `num` и `money` позволяют форматировать числовые и денежные велечины для 
текущей локали. Они используют следующие переводы:

- `'0' => [разрядностьДробнойЧасти, разделительДробнойЧасти, разделительТысяч]` - правила форматирования целых чисел
- `'.0' => [разрядностьДробнойЧасти, разделительДробнойЧасти, разделительТысяч]` - правила форматирования дробных чисел
- `'$' => [разрядностьДробнойЧасти, разделительДробнойЧасти, разделительТысяч]` - правила форматирования денежных велечин

Методы `num` и `money` в качестве второго параметра принимают правила 
форматирования числовых и денежных велечин по умолчанию.

```php
use Bricks\L18n\Translator;

$t = new Translator;
$t->register([
  'en_GB' => [
    '0' => [0, '.', '']
    '.0' => [2, '.', '']
    '$' => [2, '.', ' ']
  ]
]);
$t->locale('en_GB');
echo $t->num(10); // "10"
echo $t->num(10.5); // "10.50"
echo $t->money(12000); // "12 000.00"
$t->locale('ru_RU');
echo $t->num(10, [0, ',', '']); // "10"
echo $t->num(10.5, [2, ',', '']); // "10,50"
echo $t->money(12000, [2, ',', '.']); // "12.000,00"
```
