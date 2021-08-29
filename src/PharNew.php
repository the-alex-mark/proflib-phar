<?php

namespace ProgLib\Phar;

use DateTime;
use DateTimeZone;
use Phar;
use Seld\PharUtils\Linter;
use Seld\PharUtils\Timestamps;

/**
 *
 */
class PharNew {

    /**
     * @param  string $file
     */
    public function __construct($file) {
        $this->file = $file;
        $this->phar = new Phar($file, 0);
    }

    #region Properties

    private $phar;

    private $file;

    #endregion

    /**
     *
     */
    public function compile() {
        $this->phar->setAlias('test.phar');
        $this->phar->setSignatureAlgorithm(Phar::SHA512);
        $this->phar->startBuffering();
        $this->phar->buildFromDirectory($src);
        $this->phar->setStub("#!/usr/bin/env php \n" . $this->phar->createDefaultStub($stub));
        $this->phar->stopBuffering();

        // Отключение для взаимодействия с системами без поддержки "GZIP" и "EXT"
        // $this->compress();

        $this->dispose();
        $this->sign();
        $this->lint();
    }

    /**
     * Сжимает все файлы в текущем архиве.
     */
    private function compress() {
        $this->phar->compressFiles(Phar::GZ);
    }

    /**
     * Уничтожает текущий экземпляр класса <b>Phar</b>.
     */
    private function dispose() {
        unset($this->phar);
    }

    /**
     * Перезаписывает архив с воспроизводимой меткой времени / подписью.
     */
    private function sign() {
        $date = (new DateTime('now'))->setTimezone(new DateTimeZone('UTC'));

        $util = new Timestamps($this->file);
        $util->updateTimestamps($date);
        $util->save($this->file, Phar::SHA512);
    }

    /**
     * Сопоставляет все файлы внутри данного архива с текущей версией <b>PHP</b>.
     */
    private function lint() {
        Linter::lint($this->file);
    }
}
