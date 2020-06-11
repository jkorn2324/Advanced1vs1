<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\level\chunk;


use pocketmine\block\Block;
use pocketmine\level\ChunkLoader;
use pocketmine\level\format\FullChunk;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class AD1vs1ChunkLoader implements ChunkLoader
{

    /** @var Position */
    private $position;

    /** @var int */
    private $chunkX;

    /** @var int */
    private $chunkZ;

    /** @var Level */
    private $level;

    /** @var int */
    private $id = 0;

    /** @var callable */
    private $callable;

    public function __construct(Level $level, int $chunkX, int $chunkZ, callable $callable) {

        $this->position = Position::fromObject(new Vector3($chunkX << 4, 0, $chunkZ << 4), $level);
        $this->chunkX = $chunkX;
        $this->chunkZ = $chunkZ;
        $this->level = $level;
        $this->id = Level::generateChunkLoaderId($this);
        $this->callable = $callable;
    }

    /**
     * Returns the ChunkLoader id.
     * Call Level::generateChunkLoaderId($this) to generate and save it
     *
     * @return int
     */
    public function getLoaderId() {
        return $this->id;
    }

    /**
     * Returns if the chunk loader is currently active
     *
     * @return bool
     */
    public function isLoaderActive()
    {
        return true;
    }

    /**
     * @return Position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return float
     */
    public function getX()
    {
        return $this->chunkX;
    }

    /**
     * @return float
     */
    public function getZ()
    {
        return $this->chunkZ;
    }

    /**
     * @return Level
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * This method will be called when a Chunk is replaced by a new one
     *
     * @param FullChunk $chunk
     */
    public function onChunkChanged(FullChunk $chunk) {}

    /**
     * This method will be called when a registered chunk is loaded
     *
     * @param FullChunk $chunk
     */
    public function onChunkLoaded(FullChunk $chunk)
    {
        if(!$chunk->isPopulated()) {
            $this->level->populateChunk(intval($this->getX()), intval($this->getZ()));
            return;
        }

        $this->onChunkComplete();
    }

    /**
     * Called when the chunk has completed loading.
     */
    private function onChunkComplete() {
        $this->level->unregisterChunkLoader($this, intval($this->getX()), intval($this->getZ()));
        ($this->callable)();
    }

    /**
     * This method will be called when a registered chunk is unloaded
     *
     * @param FullChunk $chunk
     */
    public function onChunkUnloaded(FullChunk $chunk) {}

    /**
     * This method will be called when a registered chunk is populated
     * Usually it'll be sent with another call to onChunkChanged()
     *
     * @param FullChunk $chunk
     */
    public function onChunkPopulated(FullChunk $chunk)
    {
        $this->onChunkComplete();
    }

    /**
     * This method will be called when a block changes in a registered chunk
     *
     * @param Block|Vector3 $block
     */
    public function onBlockChanged(Vector3 $block) {}
}