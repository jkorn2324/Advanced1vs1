<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\level;


use pocketmine\level\generator\Generator;

class AD1vs1GeneratorInfo
{
    /** @var string */
    private $generatorName;

    /** @var mixed */
    private $clazz;

    /** @var string */
    private $localizedName;

    public function __construct(string $name, string $localizedName, $clazz)
    {
        $this->generatorName = $name;
        $this->localizedName = $localizedName;
        $this->clazz = $clazz;
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
}