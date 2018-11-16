<?php
declare(strict_types = 1);

/**
 *  ___              _  _
 * | __|__ _ ____  _| || |___ _ __  ___ ___
 * | _|/ _` (_-< || | __ / _ \ '  \/ -_|_-<
 * |___\__,_/__/\_, |_||_\___/_|_|_\___/__/
 *              |__/
 *
 * EasyHomes, a Homes plugin for PocketMine-MP.
 * Copyright (c) 2018 JackMD  < https://github.com/JackMD >
 *
 * Discord: JackMD#3717
 * Twitter: JackMTaylor_
 *
 * This software is distributed under "GNU General Public License v3.0".
 * This license allows you to use it and/or modify it but you are not at
 * all allowed to sell this plugin at any cost. If found doing so the
 * necessary action required would be taken.
 *
 * EasyHomes is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License v3.0 for more details.
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace JackMD\EasyHomes\forms;

use JackMD\EasyHomes\language\Lang;
use JackMD\EasyHomes\Main;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;

class HomeForm{
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 */
	public static function mainForm(Main $plugin, Player $player){
		$form = new SimpleForm(function(Player $player, $result) use ($plugin){
			if($result === null){
				return;
			}
			switch($result){
				case 0:
					self::createForm($plugin, $player);
					break;
				case 1:
					self::teleportForm($plugin, $player);
					break;
				case 2:
					self::deleteForm($plugin, $player);
					break;
			}
		});
		$form->setTitle(Lang::get("form.normal.title"));
		$form->setContent(Lang::get("form.normal.main.content"));
		$form->addButton(Lang::get("form.normal.main.create"));
		$form->addButton(Lang::get("form.normal.main.teleport"));
		$form->addButton(Lang::get("form.normal.main.delete"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 */
	private static function createForm(Main $plugin, Player $player){
		$form = new CustomForm(function(Player $player, $result) use ($plugin){
			if($result === null){
				return;
			}
			if(trim($result[0]) === ""){
				self::errorHomeEmpty($plugin, $player);
				return;
			}
			$homeName = $result[0];
			$plugin->getProvider()->registerPlayer($player->getName());
			if($plugin->getProvider()->getHomes($player->getName()) !== null){
				if(count($plugin->getProvider()->getHomes($player->getName())) >= $plugin->getProvider()->getMaxHomes($player->getName())){
					self::errorMaxHomes($plugin, $player, Lang::get("form.normal.create.error.max_homes.content"), Lang::get("form.normal.create.error.max_homes.button"));
					return;
				}
			}
			$condition = ($plugin->getProvider()->homeExists($player->getName(), $homeName) ? Lang::get("form.normal.create.condition.updated") : Lang::get("form.normal.create.condition.created"));
			$plugin->getProvider()->setHome($player->getName(), $homeName, $player->getLocation(), $player->getYaw(), $player->getPitch());
			self::homeCreateSuccessful($player, $homeName, $condition);
			return;
		});
		$form->setTitle(Lang::get("form.normal.title"));
		$form->addInput(Lang::get("form.normal.create.text"), Lang::get("form.normal.create.placeholder"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 */
	private static function errorHomeEmpty(Main $plugin, Player $player){
		$form = new ModalForm(function(Player $player, bool $result) use ($plugin){
			if($result){
				self::createForm($plugin, $player);
				return;
			}
		});
		$form->setTitle(Lang::get("form.error_title"));
		$form->setContent(Lang::get("form.normal.create.error.empty_home.content"));
		$form->setButton1(Lang::get("form.normal.create.error.empty_home.yes_button"));
		$form->setButton2(Lang::get("form.normal.create.error.empty_home.no_button"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 * @param string $content
	 * @param string $button
	 */
	private static function errorMaxHomes(Main $plugin, Player $player, string $content, string $button){
		$form = new SimpleForm(function(Player $player, $result){
			if($result === null){
				return;
			}
		});
		$form->setTitle(Lang::get("form.error_title"));
		$form->setContent(str_replace(["{homes}", "{max_homes}"], [count($plugin->getProvider()->getHomes($player->getName())), $plugin->getProvider()->getMaxHomes($player->getName())], $content));
		$form->addButton($button);
		$player->sendForm($form);
	}
	
	/**
	 * @param Player $player
	 * @param string $homeName
	 * @param string $condition
	 */
	private static function homeCreateSuccessful(Player $player, string $homeName, string $condition){
		$form = new SimpleForm(function(Player $player, $result){
			if($result === null){
				return;
			}
		});
		$form->setTitle(Lang::get("form.normal.title"));
		$form->setContent(str_replace(["{home_name}", "{condition}"], [$homeName, $condition], Lang::get("form.normal.create.success.content")));
		$form->addButton(Lang::get("form.normal.create.success.button"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 */
	private static function teleportForm(Main $plugin, Player $player){
		$list = $plugin->getProvider()->getHomes($player->getName());
		$form = new CustomForm(function(Player $player, $result) use ($plugin, $list){
			if($result === null){
				return;
			}
			if($plugin->getProvider()->getHomes($player->getName()) !== null){
				if(count($plugin->getProvider()->getHomes($player->getName())) > $plugin->getProvider()->getMaxHomes($player->getName())){
					self::errorMaxHomes($plugin, $player, Lang::get("form.normal.teleport.error.max_homes.content"), Lang::get("form.normal.teleport.error.max_homes.button"));
					return;
				}
			}
			$home = $list[$result[1]];
			$homeLocation = $plugin->getProvider()->getHome($player->getName(), $home);
			$player->teleport($homeLocation);
			self::homeTeleportSuccessful($player, $home);
			return;
		});
		if($list == null){
			self::noHomes($player);
			return;
		}
		$form->setTitle(Lang::get("form.normal.title"));
		$form->addLabel(Lang::get("form.normal.teleport.label"));
		$form->addDropdown(Lang::get("form.normal.teleport.dropdown"), $list);
		$player->sendForm($form);
	}
	
	/**
	 * @param Player $player
	 * @param string $home
	 */
	private static function homeTeleportSuccessful(Player $player, string $home){
		$form = new SimpleForm(function(Player $player, $result){
			if($result === null){
				return;
			}
		});
		$form->setTitle(Lang::get("form.normal.title"));
		$form->setContent(str_replace("{home_name}", $home, Lang::get("form.normal.teleport.success.content")));
		$form->addButton(Lang::get("form.normal.teleport.success.button"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Player $player
	 */
	private static function noHomes(Player $player){
		$form = new SimpleForm(function(Player $player, $result){
			if($result === null){
				return;
			}
		});
		$form->setTitle(Lang::get("form.error_title"));
		$form->setContent(Lang::get("form.normal.teleport.error.no_homes.content"));
		$form->addButton(Lang::get("form.normal.teleport.error.no_homes.button"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 */
	private static function deleteForm(Main $plugin, Player $player){
		$list = $plugin->getProvider()->getHomes($player->getName());
		$form = new CustomForm(function(Player $player, $result) use ($plugin, $list){
			if($result === null){
				return;
			}
			$home = $list[$result[1]];
			$plugin->getProvider()->deleteHome($player->getName(), $home);
			self::homeDeleteSuccessful($player, $home);
			return;
		});
		if($list == null){
			self::noHomes($player);
			return;
		}
		$form->setTitle(Lang::get("form.normal.title"));
		$form->addLabel(Lang::get("form.normal.delete.label"));
		$form->addDropdown(Lang::get("form.normal.delete.dropdown"), $list);
		$player->sendForm($form);
	}
	
	/**
	 * @param Player $player
	 * @param string $home
	 */
	private static function homeDeleteSuccessful(Player $player, string $home){
		$form = new SimpleForm(function(Player $player, $result){
			if($result === null){
				return;
			}
		});
		$form->setTitle(Lang::get("form.normal.title"));
		$form->setContent(str_replace("{home_name}", $home, Lang::get("form.normal.delete.success.content")));
		$form->addButton(Lang::get("form.normal.delete.success.button"));
		$player->sendForm($form);
	}
}