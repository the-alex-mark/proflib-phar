# ProgLib Phar Compiler

Компиляция проекта в архив **PHAR**

<br>

## Установка

```json
{
  "require": {
    "the-alex-mark/proglib-phar": "dev-master"
  },
  "repositories": [
    {
      "url": "https://github.com/the-alex-mark/proglib-phar-compiler",
      "type": "vcs"
    }
  ]
}
```

```bash
composer update
```

<br>

При компиляции проекта в архив **PHAR** необходимо изменить пространство имён для автозагрузки и переместить директорию `vendor` в одно расположение.  
Для этого используйте данный пример надстроек файла `composer.json`:
```json
{
  "config": {
    "vendor-dir": "src/vendor/"
  },
  "autoload": {
    "psr-4": {
      "App\\": "app/"
    }
  }
}
```

```bash
composer dump-autoload
```
<br>

## Использование

```json
{
  "scripts": {
    "phar:compile": "ProgLib\\Phar\\PharComposer::compile"
  },
  "extra": {
    "proglib-phar-compiler": {
      "alias": "app",
      "default-stub": "index.php",
      "path": {
        "src": "src/",
        "dist": "dist/"
      }
    }
  }
}
```

```bash
composer phar:compile
```

<br>

## Дополнительная информация

- [Phar](https://www.php.net/manual/ru/class.phar.php)
- [Composer](https://getcomposer.org/doc)
