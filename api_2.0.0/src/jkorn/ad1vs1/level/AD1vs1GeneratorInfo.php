<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\level;


use jkorn\ad1vs1\AD1vs1Util;
use pocketmine\level\generator\Generator;

class AD1vs1GeneratorInfo
{
    /** @var string */
    private $generatorName;

    /** @var mixed */
    private $clazz;

    /** @var string */
    private $localizedName;

    /** @var int */
    private $width, $length;

    /** @var bool */
    private $lowCeiling;

    public function __construct(string $name, string $localizedName, $clazz, bool $lowCeiling)
    {
        $this->generatorName = $name;
        $this->localizedName = $localizedName;
        $this->clazz = $clazz;
        $variables = AD1vs1Util::getVariablesIn($clazz);
        $this->width = $variables["chunkXSize"];
        $this->length = $variables["chunkZSize"];
        $this->lowCeiling = $lowCeiling;
    }

    /**
     * @return mixed
     *
     * Gets the class of the generator.
     */
    public function getClazz() {
        return $this->clazz;
    }

    /**
     * @return string
     *
     * Gets the generator name.
     */
    public function getGeneratorName() {
        return $this->generatorName;
    }

    /**
     * @return string
     * Gets the localized name of the generator.
     */
    public function getLocalizedName() {
        return $this->localizedName;
    }

    /**
     * Registers the generator to the pocketmine generator manager.
     */
    public function register() {
        Generator::addGenerator($this->clazz, $this->localizedName);
    }

    /**
     * @return int
     *
     * Gets the width.
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     *
     * Gets the length.
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return bool
     * Determines whether or not the
     * ceiling is low.
     */
    public function isLowCeiling()
    {
        return $this->lowCeiling;
    }
}