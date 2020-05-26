<?php

namespace explosion\task;

use HiroTeam\explosion\C4;
use pocketmine\level\Explosion;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\block\Block;

class c4Task extends Task{

    private $plugin;

    public $player;

    public $block;

    public function __construct(C4 $plugin, Player $player, Block $block){
        $this->plugin = $plugin;
        $this->player = $player;
        $this->block = $block;

    }


    public function onRun(int $currentTick){

        $this->player->sendMessage($this->plugin->config->get("messageAfterExplosion"));
        $level = $this->player->getLevel();

        $x = $this->block->getX();
        $y = $this->block->getY();
        $z = $this->block->getZ();
        $block = $this->block;

        $position = new Position($x, $y, $z, $level);
        $explosion = new Explosion($position, $this->plugin->config->get("sizeExplosion"));
        $explosion->explodeA();
        $explosion->explodeB();
        $dessous = $block->getLevel()->getBlockAt($x, $y-1, $z);
        if($dessous->getId() != Block::BEDROCK) {
            $this -> block -> level -> setBlock(new Vector3($x, $y - 1, $z), Block ::get(0, 0));
        }
        $this->block->level->setBlock(new Vector3($x, $y, $z-1), Block::get(0, 0));
        $this->block->level->setBlock(new Vector3($x-1, $y, $z), Block::get(0, 0));
        $this->block->level->setBlock(new Vector3($x, $y+1, $z), Block::get(0, 0));
        $this->block->level->setBlock(new Vector3($x, $y, $z+1), Block::get(0, 0));
        $this->block->level->setBlock(new Vector3($x+1, $y, $z), Block::get(0, 0));
    }

}