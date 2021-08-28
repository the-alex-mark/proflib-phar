<?php

namespace ProgLib\Phar;

use Phar;

/**
 * Содержит методы для компиляции файлов <b>Phar</b>.
 */
class PharCompiler {

    #region Properties

    /**
     *
     * @var string
     */
    private $alias;

    /**
     *
     * @var string
     */
    private $stub_default;

    /**
     *
     * @var string
     */
    private $path_source;

    /**
     *
     * @var string
     */
    private $path_dist;

    #endregion

    #region Setters

    /**
     * Задаёт псевдоним, с помощью которого следует ссылаться на этот архив <b>Phar</b>.
     *
     * @param  string $alias Псевдоним.
     * @return $this
     */
    public function setAlias($alias) {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Задаёт заглушку для определенного формата файла <b>Phar</b>.
     *
     * @param  string $index
     * @return $this
     */
    public function setDefaultStub($index) {
        $this->stub_default = $index;

        return $this;
    }

    /**
     * Задаёт расположение файлов, которые требуется сжать.
     *
     * @param  string $path Расположение исходных файлов.
     * @return $this
     */
    public function setSourcePath($path) {
        $this->path_source = $path;

        return $this;
    }

    /**
     * Задаёт расположение скомпилированного архива.
     *
     * @param  string $path Расположение скомпилированного архива.
     * @return $this
     */
    public function setDistPath($path) {
        $this->path_dist = $path;

        return $this;
    }

    #endregion

    #region Helpers

    /**
     * Возвращает имя сжатого файла.
     *
     * @return string
     */
    public function getFileName() {
        return $this->path_dist . DIRECTORY_SEPARATOR . $this->alias . '.phar';
    }

    #endregion

    /**
     * Подготавливает расположение для создания архива.
     *
     * @param void
     */
    private function clear() {
        $dist = $this->path_dist;
        $file = $this->getFileName();

        # Подготовка расположения
        if (file_exists($file)) unlink($file);
        if (file_exists($file . '.gz')) unlink($file . '.gz');
        if (!file_exists($dist)) mkdir($dist, 0750);
    }

    /**
     * Выполняет сжатие файлов в архив <b>Phar</b>.
     *
     * @return void
     */
    public function compile() {
        $alias = $this->alias;
        $stub  = $this->stub_default;
        $src   = $this->path_source;
        $file  = $this->getFileName();

        # Необходимые проверки
        # FIXME: Реализовать проверку существования исходного расположения
        # FIXME: Реализовать валидацию входных параметров

        # Подготовка расположения
        $this->clear();

        # Сборка исполняемого файла
        $phar = new Phar($file, 0, "$alias.phar");
        $phar->startBuffering();
        $phar->buildFromDirectory($src);
        $phar->setStub("#!/usr/bin/env php \n" . $phar->createDefaultStub($stub));
        $phar->stopBuffering();
        $phar->compressFiles(Phar::GZ);
    }
}
