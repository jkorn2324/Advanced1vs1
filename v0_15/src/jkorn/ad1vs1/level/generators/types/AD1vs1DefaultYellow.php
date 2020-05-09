<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\level\generators\types;


use jkorn\ad1vs1\level\AD1vs1GeneratorManager;
use jkorn\ad1vs1\level\generators\Abstract1vs1Generator;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\FullChunk;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class AD1vs1DefaultYellow extends Abstract1vs1Generator
{

    /** @var array|Block[] */
    private $blocks = [];

    public function init(ChunkManager $level, Random $random)
    {
        parent::init($level, $random);
        $this->initBlocks();
    }

    /**
     * Initializes the blocks.
     */
    private function initBlocks()
    {
        $this->blocks = [
            Block::get(Block::STAINED_CLAY, 4),
            Block::get(Block::STAINED_CLAY, 4),
            Block::get(Block::WOOL, 4),
            Block::get(Block::WOOL, 4),
            Block::get(Block::GOLD_BLOCK)
        ];
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
    protected function setFloor(FullChunk $chunk, int $chunkXCoord, int $chunkZCoord, int $x, int $z)
    {
        $rand = mt_rand(0, count($this->blocks) - 1);

        /** @var Block $block */
        $block = $this->blocks[$rand];
        $chunk->setBlock($x, 99, $z, $block->getId(), $block->getDamage());

        $underneath = Block::get(BlockIds::BEDROCK);

        $chunk->setBlock($x, 98, $z, $underneath->getId(), $underneath->getDamage());
        $chunk->setBlock($x, 97, $z, BlockIds::BEDROCK);
        $chunk->setBlock($x, intval($this->getCeilingY()), $z, BlockIds::INVISIBLE_BEDROCK);
    }

    /**
     * Gets the name of the generator.
     */
    public function getName()
    {
        return AD1vs1GeneratorManager::DEFAULT_YELLOW;
    }

    /**
     * Gets the spawn of the arena.
     * @return Vector3
     */
    public function getSpawn()
    {
        return new Vector3(0, 100, 0);
    }
}