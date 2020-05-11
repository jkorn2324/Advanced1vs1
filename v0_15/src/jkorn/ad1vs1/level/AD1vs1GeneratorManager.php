<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\level;


use jkorn\ad1vs1\AD1vs1Main;
use pocketmine\math\Vector3;
use pocketmine\Server;

class AD1vs1GeneratorManager
{

    const DEFAULT_RED = "1vs1.default.red";
    const DEFAULT_GREEN = "1vs1.default.green";
    const DEFAULT_BLUE = "1vs1.default.blue";
    const DEFAULT_YELLOW = "1vs1.default.yellow";
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
     * @param int $width
     * @param int $length
     *
     * Registers the generator to the list.
     */
    public function registerGenerator(string $name, string $localized, $object, int $width = 3, int $length = 3) {
        $this->generators[$localized] = $info = new AD1vs1GeneratorInfo($name, $localized, $object, $width, $length);
        $info->register();
    }

    /**
     * @return AD1vs1GeneratorInfo
     *
     * Calls a random generator from the list.
     */
    public function randomGenerator() {
        return $this->generators[
            array_keys($this->generators)[mt_rand(0, count($this->generators) - 1)]
        ];
    }
}