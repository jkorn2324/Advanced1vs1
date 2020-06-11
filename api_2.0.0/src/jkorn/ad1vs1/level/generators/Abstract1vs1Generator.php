<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\level\generators;


use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\FullChunk;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

abstract class Abstract1vs1Generator extends Generator
{

    /** @var int */
    protected $chunkXSize = 3;
    /** @var int */
    protected $chunkZSize = 3;

    /** @var int */
    protected $height = 10;

    /** @var ChunkManager */
    protected $level;
    /** @var Random */
    protected $random;

    /** @var int */
    protected $count = 0;

    /**
     * Abstract1vs1Generator constructor.
     * @param array $settings
     */
    public function __construct(array $settings = []) {}

    /**
     * @param ChunkManager $level
     * @param Random $random
     *
     * Initializes the duel generator.
     */
    public function init(ChunkManager $level, Random $random)
    {
        $this->level = $level;
        $this->random = $random;
    }

    /**
     * @param $chunkX
     * @param $chunkZ
     *
     * Generates the chunk.
     */
    public function generateChunk($chunkX, $chunkZ)
    {
        if($this->level instanceof ChunkManager) {
            $chunk = $this->level->getChunk($chunkX, $chunkZ);
            $chunkXCoord = $chunkX; $chunkZCoord = $chunkZ;
            if($chunkXCoord >= 0 && $chunkXCoord < $this->chunkXSize && $chunkZCoord >= 0 && $chunkZCoord < $this->chunkZSize) {
                for($x = 0; $x < 16; $x++) {
                    for($z = 0; $z < 16; $z++) {
                        if ($this->isBarrier($chunkXCoord, $chunkZCoord, $x, $z)) {
                            $this->setBarrier($chunk, $x, $z);
                        } else {
                            $this->setFloor($chunk, $chunkXCoord, $chunkZCoord, $x, $z);
                        }
                    }
                }
            }
            $chunk->setX($chunkX);
            $chunk->setZ($chunkZ);
            $chunk->setGenerated();
            $this->level->setChunk($chunkX, $chunkZ, $chunk);
        }
    }

    /**
     * @param FullChunk $chunk
     * @param int $x
     * @param int $z
     *
     * Sets the barrier based on the given coords.
     */
    private function setBarrier(FullChunk $chunk, int $x, int $z) {

        $spawnHeight = $this->getSpawn()->y; $yCeiling = $this->getCeilingY();
        for($y = $spawnHeight - 1; $y < $yCeiling; $y++) {
            $chunk->setBlock($x, $y, $z, BlockIds::INVISIBLE_BEDROCK);
        }
    }

    /**
     * @param FullChunk $chunk
     * @param int $chunkXCoord
     * @param int $chunkZCoord
     * @param int $x
     * @param int $z
     *
     * Sets the barrier.
     */
    protected abstract function setFloor(FullChunk $chunk, int $chunkXCoord, int $chunkZCoord, int $x, int $z);

    /**
     * @param int $chunkXCoord
     * @param int $chunkZCoord
     * @param int $x
     * @param int $z
     * @return bool
     *
     * Determines if the given coords is a barrier.
     */
    private function isBarrier(int $chunkXCoord, int $chunkZCoord, int $x, int $z) {

        $xCheck = false;
        if($chunkXCoord == 0) {
            $xCheck = $x == 0;
        } elseif ($chunkXCoord == $this->chunkXSize - 1) {
            $xCheck = $x == 15;
        }

        $zCheck = false;
        if($chunkZCoord == 0) {
            $zCheck = $z == 0;
        } elseif ($chunkZCoord == $this->chunkZSize - 1) {
            $zCheck = $z == 15;
        }

        return $xCheck || $zCheck;
    }

    /**
     * @param $chunkX
     * @param $chunkZ
     *
     * Populates a chunk.
     */
    public function populateChunk($chunkX, $chunkZ) {}

    /**
     * @return mixed[]
     */
    public function getSettings()
    {
        return [];
    }

    /**
     * @return int
     *
     * Gets the y of the ceiling.
     */
    protected function getCeilingY() {
        /** @var Vector3 $spawn */
        $spawn = $this->getSpawn();
        return $spawn->y + $this->height;
    }
}