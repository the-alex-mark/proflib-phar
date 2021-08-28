<?php

namespace ProgLib\Phar;

use Composer\Script\Event;
use Exception;

/**
 * Содержит команды для выполнения скриптами <b>Composer</b>.
 */
class PharComposer {

    #region Properties

    /**
     * Имя ключа дополнительных параметров.
     * @var string
     */
    private static $extra = 'proglib-phar-compiler';

    #endregion

    #region Helpers

    /**
     * Возвращает значение дополнительного параметра.
     *
     * @return mixed
     * @throws Exception
     */
    public static function extra($event, $key, $default = null) {
        $extra = $event->getComposer()->getPackage()->getExtra();

        if (!isset($extra[static::$extra]))
            throw new Exception('Отсутствуют необходимы параметры для работы компилятора');

        if (!is_string($key))
            throw new Exception('Входные параметры имеют неверный формат');

        try {
            $path  = explode('.', $key);
            $param = $extra[static::$extra];

            foreach($path as $item)
                $param = $param[$item];
        }
        catch (Exception $e) {
            if (is_null($default))
                throw new Exception('Отсутствует обязательный параметр "' . $key . '"');
        }

        return (empty($param))
            ? $default
            : $param;
    }

    #endregion

    /**
     * Выполняет сжатие файлов в архив <b>Phar</b>.
     *
     * @param  Event $event
     * @return void
     */
    public static function compile(Event $event) {
        ini_set('phar.readonly', 'off');

        try {

            # Параметры "PharCompiler"
            $alias = self::extra($event, 'alias');
            $stub  = self::extra($event, 'default-stub', 'index.php');
            $src   = self::extra($event, 'path.src');
            $dist  = self::extra($event, 'path.dist');

            # Установка необходимых параметров
            $pharCompiler = (new PharCompiler())
                ->setAlias($alias)
                ->setDefaultStub($stub)
                ->setSourcePath($src)
                ->setDistPath($dist);

            # Сборка файла
            $pharCompiler->compile();

            # Выдача необходимых прав на исполняемый файл
            $pharFile = $pharCompiler->getFileName();
            if (file_exists($pharFile)) chmod($pharFile, 0750);

            # Уведомление об успешной компиляции
            echo "> " . "\033[0;32m" . "Архив \"$alias.phar\" успешно собран." . "\033[0m" . PHP_EOL;
        } catch (Exception $e) {

            # Уведомление о наличии ошибок при компиляции
            echo "> При сжатии файлов произошла непредвиденная ошибка: \033[0;31m" . $e->getMessage() . "\033[0m." . PHP_EOL;
        }
    }
}
