<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\level;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\level\generators\Abstract1vs1Generator;
use pocketmine\math\Vector3;
use pocketmine\Server;

class AD1vs1GeneratorManager
{

    const DEFAULT_RED = "1vs1.default.red";
    const DEFAULT_YELLOW = "1vs1.default.yellow";
    const DEFAULT_GREEN = "1vs1.default.green";

    const DEFAULT_RED_LOW_CEIL = "1vs1.default.red.ceiling_low";
    const DEFAULT_YELLOW_LOW_CEIL = "1vs1.default.yellow.ceiling_low";
    const DEFAULT_GREEN_LOW_CEIL = "1vs1.default.green.ceiling_low";

    const DEFAULT_BLUE = "1vs1.default.blue";
    const DEFAULT_PURPLE = "1vs1.default.purple";


    /** @var AD1vs1GeneratorInfo[] */
    private $generators;

    /** @var Server */
    private $server;
    /** @var AD1vs1Main */
    private $main;

    public function __construct(AD1vs1Main $main)
    {
        $this->main = $main;
        $this->server = $main->getServer();

        $this->generators = [];
    }

    /**
     * @param string $name
     * @param string $localized
     * @param $object
     * @param bool $lowCeiling
     *
     * Registers the generator to the list.
     */
    public function registerGenerator(string $name, string $localized, $object, bool $lowCeiling) {

        if(!is_subclass_of($object, Abstract1vs1Generator::class)) {
            return;
        }

        $this->generators[$localized] = $info = new AD1vs1GeneratorInfo($name, $localized, $object, $lowCeiling);
        $info->register();
    }

    /**
     * @param bool $lowCeiling
     * @return AD1vs1GeneratorInfo
     *
     * Calls a random generator from the list.
     */
    public function randomGenerator(bool $lowCeiling = false) {

        $generators = [];
        foreach($this->generators as $generatorLocalized => $generator)
        {
            if($generator->isLowCeiling() === $lowCeiling) {
                $generators[$generatorLocalized] = $generator;
            }
        }

        return $generators[
            array_keys($generators)[mt_rand(0, count($generators) - 1)]
        ];
    }
}