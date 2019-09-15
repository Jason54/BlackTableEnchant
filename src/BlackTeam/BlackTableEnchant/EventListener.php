<?php

declare(strict_types = 1);

namespace BlackTeam\BlackTableEnchant;

use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\Tool;
use pocketmine\Player;
use jojoe77777\FormAPI\SimpleForm;

class EventListener implements Listener {

    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function generateEnchants(Item $toEnchant, Block $ectable) : array {
        $bookshelfs = $this->plugin->getBookshelfs($ectable);
        if($bookshelfs >= 0) {
            $levelSub = 0.20;
        }
        if($bookshelfs > 5) {
            $levelSub = 0.40;
        }
        if($bookshelfs > 10) {
            $levelSub = 0.70;
        }
        if($bookshelfs > 15) {
            $levelSub = 1;
        }
        if($toEnchant instanceof Sword) {
            $firstEnch = explode(":", $this->plugin->swordEnchantments[array_rand($this->plugin->swordEnchantments)]);
            $secondEnch = explode(":", $this->plugin->swordEnchantments[array_rand($this->plugin->swordEnchantments)]);
            $thirdEnch = explode(":", $this->plugin->swordEnchantments[array_rand($this->plugin->swordEnchantments)]);
            $enchants = array(
                0 => $firstEnch[0].":".rand(1, intval($firstEnch[1] * ($levelSub - 0.15))).":".rand(intval(2 * ($levelSub + 1)), intval(6 * ($levelSub + 1))),
                1 => $secondEnch[0].":".rand(1, intval($secondEnch[1] * ($levelSub - 0.10))).":".rand(intval(6 * ($levelSub + 1)), intval(10 * ($levelSub + 1))),
                2 => $thirdEnch[0].":".rand(2, intval($thirdEnch[1] * ($levelSub))).":".rand(intval(10 * ($levelSub + 1)), intval(15 * ($levelSub + 1)))
            );
        }
        else if($toEnchant instanceof Bow) {
            $firstEnch = explode(":", $this->plugin->bowEnchantments[array_rand($this->plugin->bowEnchantments)]);
            $secondEnch = explode(":", $this->plugin->bowEnchantments[array_rand($this->plugin->bowEnchantments)]);
            $thirdEnch = explode(":", $this->plugin->bowEnchantments[array_rand($this->plugin->bowEnchantments)]);
            $enchants = array(
                0 => $firstEnch[0] . ":" . rand(1, intval($firstEnch[1] * ($levelSub - 0.15))) . ":" . rand(intval(2 * ($levelSub + 1)), intval(6 * ($levelSub + 1))),
                1 => $secondEnch[0] . ":" . rand(1, intval($secondEnch[1] * ($levelSub - 0.10))) . ":" . rand(intval(6 * ($levelSub + 1)), intval(10 * ($levelSub + 1))),
                2 => $thirdEnch[0] . ":" . rand(2, intval($thirdEnch[1] * ($levelSub))) . ":" . rand(intval(10 * ($levelSub + 1)), intval(15 * ($levelSub + 1)))
            );
                }
        else if($toEnchant instanceof Tool) {
            $firstEnch = explode(":", $this->plugin->toolEnchantments[array_rand($this->plugin->toolEnchantments)]);
            $secondEnch = explode(":", $this->plugin->toolEnchantments[array_rand($this->plugin->toolEnchantments)]);
            $thirdEnch = explode(":", $this->plugin->toolEnchantments[array_rand($this->plugin->toolEnchantments)]);
            $enchants = array(
                0 => $firstEnch[0].":".rand(1, intval($firstEnch[1] * ($levelSub - 0.15))).":".rand(intval(2 * ($levelSub + 1)), intval(6 * ($levelSub + 1))),
                1 => $secondEnch[0].":".rand(1, intval($secondEnch[1] * ($levelSub - 0.10))).":".rand(intval(6 * ($levelSub + 1)), intval(10 * ($levelSub + 1))),
                2 => $thirdEnch[0].":".rand(2, intval($thirdEnch[1] * ($levelSub))).":".rand(intval(10 * ($levelSub + 1)), intval(15 * ($levelSub + 1)))
            );
        }
        else if($toEnchant instanceof Armor) {
            $firstEnch = explode(":", $this->plugin->armorEnchantments[array_rand($this->plugin->armorEnchantments)]);
            $secondEnch = explode(":", $this->plugin->armorEnchantments[array_rand($this->plugin->armorEnchantments)]);
            $thirdEnch = explode(":", $this->plugin->armorEnchantments[array_rand($this->plugin->armorEnchantments)]);
            $enchants = array(
                0 => $firstEnch[0].":".rand(1, intval($firstEnch[1] * ($levelSub - 0.15))).":".rand(intval(2 * ($levelSub + 1)), intval(6 * ($levelSub + 1))),
                1 => $secondEnch[0].":".rand(1, intval($secondEnch[1] * ($levelSub - 0.10))).":".rand(intval(6 * ($levelSub + 1)), intval(10 * ($levelSub + 1))),
                2 => $thirdEnch[0].":".rand(2, intval($thirdEnch[1] * ($levelSub))).":".rand(intval(10 * ($levelSub + 1)), intval(15 * ($levelSub + 1)))
            );
        }
        else {
            $enchants = [];
        }
        return $enchants;
    }

    public function openECTUI(Player $player, Item $toEnchant, Block $ectable) {

        $enchants = $this->generateEnchants($toEnchant, $ectable);
        if(empty($enchants)) {
            $player->sendMessage("§cIl n'y a pas d'enchantements disponible pour cet objet!");
            return;
        }
        $form = new SimpleForm(function (Player $player, int $data = null) use ($toEnchant, $enchants) {
            if($data != null) {
                switch ($data) {
                    case 1:
                        $arr = explode(":",$enchants[0]);
                        if($player->getXpLevel() < $arr[2]) {
                            $player->sendMessage("§cTu n'as pas assez de niveaux!");
                            return;
                        } else {
                            $ench = Enchantment::getEnchantmentByName($arr[0]);
                            if($toEnchant->getEnchantment($ench->getId())) {
                                $player->sendMessage("§cVous ne pouvez plus enchanter le même enchantement!");
                                return;
                            }
                            $player->setXpLevel($player->getXpLevel() - $arr[2]);
                            $level = $arr[1];
                            if($level <= 0) {
                                $level = 1;
                            }
                            $toEnchant->addEnchantment(new EnchantmentInstance($ench, (int) $level));
                            $player->getInventory()->setItemInHand($toEnchant);
                        }

                        break;
                    case 2:
                        $arr = explode(":",$enchants[1]);
                        if($player->getXpLevel() < $arr[2]) {
                            $player->sendMessage("§cTu n'as pas assez de niveaux!");
                            return;
                        } else {
                            $ench = Enchantment::getEnchantmentByName($arr[0]);
                            if($toEnchant->getEnchantment($ench->getId())) {
                                $player->sendMessage("§cVous ne pouvez plus enchanter le même enchantement!");
                                return;
                            }
                            $player->setXpLevel($player->getXpLevel() - $arr[2]);
                            $level = $arr[1];
                            if($level <= 0) {
                                $level = 1;
                            }
                            $toEnchant->addEnchantment(new EnchantmentInstance($ench, (int) $level));
                            $player->getInventory()->setItemInHand($toEnchant);
                        }
                        break;
                    case 3:
                        $arr = explode(":",$enchants[2]);
                        if($player->getXpLevel() < $arr[2]) {
                            $player->sendMessage("§cTu n'as pas assez de niveaux!");
                            return;
                        } else {
                            $ench = Enchantment::getEnchantmentByName($arr[0]);
                            if($toEnchant->getEnchantment($ench->getId())) {
                                $player->sendMessage("§cVous ne pouvez plus enchanter le même enchantement!");
                                return;
                            }
                            $player->setXpLevel($player->getXpLevel() - $arr[2]);
                            $level = $arr[1];
                            if($level <= 0) {
                                $level = 1;
                            }
                            $toEnchant->addEnchantment(new EnchantmentInstance($ench, (int) $level));
                            $player->getInventory()->setItemInHand($toEnchant);
                        }
                        break;
                    default:

                        break;
                }
            }
        });

        $form->setTitle("Enchantez: ".$toEnchant->getName());
        $form->addButton("§l§cQuittez");
        foreach ($enchants as $ec) {
            $arr = explode(":", $ec);
            $lvl = $arr[1];
            if($lvl <= 0) {
                $lvl = 1;
            }
            $form->addButton($arr[0]." (".$lvl.") pour ".$arr[2]." niveaux");
        }
        $form->setContent("Enchantez votre tenue");
        $form->sendToPlayer($player);

    }

    public function onTouch(PlayerInteractEvent $event) {

        $block = $event->getBlock();
        if($block->getId() === Block::ENCHANTING_TABLE || $block->getId() === Block::ENCHANTMENT_TABLE) {
            $event->setCancelled(true);
            if($event->getPlayer()->isSneaking() === false) {
                $toEnchant = $event->getItem();
                $this->openECTUI($event->getPlayer(), $toEnchant, $block);
               
            }
        }

    }

}