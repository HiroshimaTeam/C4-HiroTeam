<?php

namespace HiroTeam\explosion;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use explosion\task\c4Task;

class C4 extends PluginBase implements Listener{

    public $config;

    private $WorldGuard;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->WorldGuard = $this->getServer()->getPluginManager()->getPlugin("WorldGuard");

        @mkdir($this->getDataFolder());

        if(!file_exists($this->getDataFolder(). "config.yml")) {
            $this->saveResource('config.yml');
        }
        $this->config = new Config($this->getDataFolder().'config.yml', Config::YAML);

    }

    public function onPlace(BlockPlaceEvent $event){
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $level = $player->getLevel();

        $x = $block->getX();
        $y = $block->getY();
        $z = $block->getZ();

        $position = new Position($x, $y, $z, $level);

        if (($region = $this->WorldGuard->getRegionFromPosition($position)) !== "") {
            return true;

        }

        $c4 = $this->config->get("c4ID");
        if($c4 === $block->getId() . ":". $block->getDamage()){
            $player->sendMessage($this->config->get("messagePlace"));
            $this->getScheduler()->scheduleDelayedTask(new task\c4Task($this, $player, $block), 80);

        }
    }

    public function onInteract(PlayerInteractEvent $event){
        $block = $event->getBlock();
        $player = $event->getPlayer();

        if ($player instanceof Player){
            $c4 = $this->config->get("c4ID");
            if ($c4 === $block->getId() . ":". $block->getDamage()){
                $event->setCancelled(true);
            }
        }
    }

    public function onBreak(BlockBreakEvent $event){
        $block = $event->getBlock();
        $player = $event->getPlayer();

        if($player instanceof Player) {
            $PandoraBox = $this->config->get("c4ID");
            if ($PandoraBox === $block->getId() . ":". $block->getDamage()) {
                $event->setCancelled(true);

            }
        }
    }

}