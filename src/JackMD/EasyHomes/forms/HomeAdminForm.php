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
use JackMD\EasyHomes\utils\Utils;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\OfflinePlayer;
use pocketmine\Player;

class HomeAdminForm{
	
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
				case 3:
					self::getLimitForm($plugin, $player);
					break;
				case 4:
					self::setLimitForm($plugin, $player);
					break;
			}
		});
		$form->setTitle(Lang::get("form.admin.title"));
		$form->setContent(Lang::get("form.admin.main.content"));
		$form->addButton(Lang::get("form.admin.main.create"));
		$form->addButton(Lang::get("form.admin.main.teleport"));
		$form->addButton(Lang::get("form.admin.main.delete"));
		$form->addButton(Lang::get("form.admin.main.get_limit"));
		$form->addButton(Lang::get("form.admin.main.set_limit"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 */
	private static function createForm(Main $plugin, Player $player){
		$list = [];
		foreach($plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
			$list[] = $onlinePlayer->getName();
		}
		$form = new CustomForm(function(Player $player, $result) use ($plugin, $list){
			if($result === null){
				return;
			}
			if($result[1]){
				if(trim($result[2]) === ""){
					self::errorPlayerEmpty($player);
					return;
				}
				$userName = $result[2];
			}else{
				$userName = $list[$result[3]];
			}
			$user = Utils::checkPlayer($userName);
			if(is_null($user)){
				self::errorPlayerNotFound($player, $userName);
				return;
			}
			if(trim($result[4]) === ""){
				self::errorHomeEmpty($plugin, $player);
				return;
			}
			$homeName = $result[4];
			$plugin->getProvider()->registerPlayer($user->getName());
			if($plugin->getProvider()->getHomes($user->getName()) !== null){
				if(count($plugin->getProvider()->getHomes($user->getName())) >= $plugin->getProvider()->getMaxHomes($user->getName())){
					self::errorMaxHomes($plugin, $player, $user, Lang::get("form.admin.create.error.max_homes.content"), Lang::get("form.admin.create.error.max_homes.button"));
					return;
				}
			}
			$condition = ($plugin->getProvider()->homeExists($user->getName(), $homeName) ? Lang::get("form.admin.create.condition.updated") : Lang::get("form.admin.create.condition.created"));
			$plugin->getProvider()->setHome($user->getName(), $homeName, $player->getLocation(), $player->getYaw(), $player->getPitch());
			self::homeCreateSuccessful($player, $homeName, $condition);
			return;
		});
		$form->setTitle(Lang::get("form.admin.title"));
		$form->addLabel(Lang::get("form.admin.create.label"));
		$form->addToggle(Lang::get("form.admin.user.toggle"));
		$form->addInput(Lang::get("form.admin.user.input.player.text"), Lang::get("form.admin.user.input.player.placeholder"));
		$form->addDropdown(Lang::get("form.admin.user.dropdown"), $list);
		$form->addInput(Lang::get("form.admin.create.input.home.text"), Lang::get("form.admin.create.input.home.placeholder"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Player $player
	 */
	private static function errorPlayerEmpty(Player $player){
		$form = new SimpleForm(function(Player $player, $result){
			if($result === null){
				return;
			}
		});
		$form->setTitle(Lang::get("form.error_title"));
		$form->setContent(Lang::get("form.error.empty_player.content"));
		$form->addButton(Lang::get("form.error.empty_player.button"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Player $player
	 * @param string $name
	 */
	private static function errorPlayerNotFound(Player $player, string $name){
		$form = new SimpleForm(function(Player $player, $result){
			if($result === null){
				return;
			}
		});
		$form->setTitle(Lang::get("form.error_title"));
		$form->setContent(str_replace("{player}", $name, Lang::get("form.error.player_not_found.content")));
		$form->addButton(Lang::get("form.error.player_not_found.button"));
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
		$form->setContent(Lang::get("form.admin.create.error.empty_home.content"));
		$form->setButton1(Lang::get("form.admin.create.error.empty_home.yes_button"));
		$form->setButton2(Lang::get("form.admin.create.error.empty_home.no_button"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 * @param Player $user
	 * @param string $content
	 * @param string $button
	 */
	private static function errorMaxHomes(Main $plugin, Player $player, Player $user, string $content, string $button){
		$form = new SimpleForm(function(Player $player, $result){
			if($result === null){
				return;
			}
		});
		$form->setTitle(Lang::get("form.error_title"));
		$form->setContent(str_replace(["{homes}", "{max_homes}"], [count($plugin->getProvider()->getHomes($user->getName())), $plugin->getProvider()->getMaxHomes($user->getName())], $content));
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
		$form->setTitle(Lang::get("form.admin.title"));
		$form->setContent(str_replace(["{home_name}", "{condition}"], [$homeName, $condition], Lang::get("form.admin.create.success.content")));
		$form->addButton(Lang::get("form.admin.create.success.button"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 */
	private static function teleportForm(Main $plugin, Player $player){
		$playerList = [];
		foreach($plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
			$playerList[] = $onlinePlayer->getName();
		}
		$form = new CustomForm(function(Player $player, $result) use ($plugin, $playerList){
			if($result === null){
				return;
			}
			if($result[1]){
				if(trim($result[2]) === ""){
					self::errorPlayerEmpty($player);
					return;
				}
				$userName = $result[2];
			}else{
				$userName = $playerList[$result[3]];
			}
			$user = Utils::checkPlayer($userName);
			if(is_null($user)){
				self::errorPlayerNotFound($player, $userName);
				return;
			}
			$homeList = $plugin->getProvider()->getHomes($user->getName());
			if(is_null($homeList) || empty($homeList)){
				self::noHomes($player);
				return;
			}
			self::userHomeTeleportSelect($plugin, $player, $user);
			return;
		});
		$form->setTitle(Lang::get("form.admin.title"));
		$form->addLabel(Lang::get("form.admin.teleport.label"));
		$form->addToggle(Lang::get("form.admin.user.toggle"));
		$form->addInput(Lang::get("form.admin.user.input.player.text"), Lang::get("form.admin.user.input.player.placeholder"));
		$form->addDropdown(Lang::get("form.admin.user.dropdown"), $playerList);
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
		$form->setContent(Lang::get("form.admin.user.error.no_homes.content"));
		$form->addButton(Lang::get("form.admin.user.error.no_homes.button"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main                 $plugin
	 * @param Player               $player
	 * @param OfflinePlayer|Player $user
	 */
	private static function userHomeTeleportSelect(Main $plugin, Player $player, $user){
		$homeList = $plugin->getProvider()->getHomes($user->getName());
		$form = new CustomForm(function(Player $player, $result) use ($plugin, $user, $homeList){
			if($result === null){
				return;
			}
			$home = $homeList[$result[1]];
			$homeLocation = $plugin->getProvider()->getHome($user->getName(), $home);
			$player->teleport($homeLocation);
			self::homeTeleportSuccessful($player, $home);
			return;
		});
		$form->setTitle(Lang::get("form.admin.title"));
		$form->addLabel(Lang::get("form.admin.teleport.select.label"));
		$form->addDropdown(Lang::get("form.admin.teleport.select.dropdown"), $homeList);
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
		$form->setTitle(Lang::get("form.admin.title"));
		$form->setContent(str_replace("{home_name}", $home, Lang::get("form.admin.teleport.success.content")));
		$form->addButton(Lang::get("form.admin.teleport.success.button"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 */
	private static function deleteForm(Main $plugin, Player $player){
		$playerList = [];
		foreach($plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
			$playerList[] = $onlinePlayer->getName();
		}
		$form = new CustomForm(function(Player $player, $result) use ($plugin, $playerList){
			if($result === null){
				return;
			}
			if($result[1]){
				if(trim($result[2]) === ""){
					self::errorPlayerEmpty($player);
					return;
				}
				$userName = $result[2];
			}else{
				$userName = $playerList[$result[3]];
			}
			$user = Utils::checkPlayer($userName);
			if(is_null($user)){
				self::errorPlayerNotFound($player, $userName);
				return;
			}
			$homeList = $plugin->getProvider()->getHomes($user->getName());
			if(is_null($homeList) || empty($homeList)){
				self::noHomes($player);
				return;
			}
			self::userHomeDeleteSelect($plugin, $player, $user);
			return;
		});
		$form->setTitle(Lang::get("form.admin.title"));
		$form->addLabel(Lang::get("form.admin.delete.label"));
		$form->addToggle(Lang::get("form.admin.user.toggle"));
		$form->addInput(Lang::get("form.admin.user.input.player.text"), Lang::get("form.admin.user.input.player.placeholder"));
		$form->addDropdown(Lang::get("form.admin.user.dropdown"), $playerList);
		$player->sendForm($form);
	}
	
	/**
	 * @param Main                 $plugin
	 * @param Player               $player
	 * @param OfflinePlayer|Player $user
	 */
	private static function userHomeDeleteSelect(Main $plugin, Player $player, $user){
		$homeList = $plugin->getProvider()->getHomes($user->getName());
		$form = new CustomForm(function(Player $player, $result) use ($plugin, $user, $homeList){
			if($result === null){
				return;
			}
			$home = $homeList[$result[1]];
			$plugin->getProvider()->deleteHome($user->getName(), $home);
			self::homeDeleteSuccessful($player, $home);
			return;
		});
		$form->setTitle(Lang::get("form.admin.title"));
		$form->addLabel(Lang::get("form.admin.delete.select.label"));
		$form->addDropdown(Lang::get("form.admin.delete.select.dropdown"), $homeList);
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
		$form->setTitle(Lang::get("form.admin.title"));
		$form->setContent(str_replace("{home_name}", $home, Lang::get("form.admin.delete.success.content")));
		$form->addButton(Lang::get("form.admin.delete.success.button"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 */
	private static function getLimitForm(Main $plugin, Player $player){
		$playerList = [];
		foreach($plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
			$playerList[] = $onlinePlayer->getName();
		}
		$form = new CustomForm(function(Player $player, $result) use ($plugin, $playerList){
			if($result === null){
				return;
			}
			if($result[1]){
				if(trim($result[2]) === ""){
					self::errorPlayerEmpty($player);
					return;
				}
				$userName = $result[2];
			}else{
				$userName = $playerList[$result[3]];
			}
			$user = Utils::checkPlayer($userName);
			if(is_null($user)){
				self::errorPlayerNotFound($player, $userName);
				return;
			}
			self::homeGetLimitSuccessful($player, $user->getName(), $plugin->getProvider()->getMaxHomes($user->getName()));
			return;
		});
		$form->setTitle(Lang::get("form.admin.title"));
		$form->addLabel(Lang::get("form.admin.get_limit.label"));
		$form->addToggle(Lang::get("form.admin.user.toggle"));
		$form->addInput(Lang::get("form.admin.user.input.player.text"), Lang::get("form.admin.user.input.player.placeholder"));
		$form->addDropdown(Lang::get("form.admin.user.dropdown"), $playerList);
		$player->sendForm($form);
	}
	
	/**
	 * @param Player $player
	 * @param string $userName
	 * @param int    $limit
	 */
	private static function homeGetLimitSuccessful(Player $player, string $userName, int $limit){
		$form = new SimpleForm(function(Player $player, $result){
			if($result === null){
				return;
			}
		});
		$form->setTitle(Lang::get("form.admin.title"));
		$form->setContent(str_replace(["{player}", "{home_limit}"], [$userName, $limit], Lang::get("form.admin.get_limit.success.content")));
		$form->addButton(Lang::get("form.admin.get_limit.success.button"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 */
	private static function setLimitForm(Main $plugin, Player $player){
		$playerList = [];
		foreach($plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
			$playerList[] = $onlinePlayer->getName();
		}
		$form = new CustomForm(function(Player $player, $result) use ($plugin, $playerList){
			if($result === null){
				return;
			}
			if($result[1]){
				if(trim($result[2]) === ""){
					self::errorPlayerEmpty($player);
					return;
				}
				$userName = $result[2];
			}else{
				$userName = $playerList[$result[3]];
			}
			$user = Utils::checkPlayer($userName);
			if(is_null($user)){
				self::errorPlayerNotFound($player, $userName);
				return;
			}
			if(trim($result[4]) === ""){
				self::errorLimitEmpty($plugin, $player);
				return;
			}
			$limit = (int) $result[4];
			$plugin->getProvider()->setMaxHomes($user->getName(), $limit);
			self::userHomeLimitSetSuccessful($player, $user->getName(), $limit);
			return;
		});
		$form->setTitle(Lang::get("form.admin.title"));
		$form->addLabel(Lang::get("form.admin.set_limit.label"));
		$form->addToggle(Lang::get("form.admin.user.toggle"));
		$form->addInput(Lang::get("form.admin.user.input.player.text"), Lang::get("form.admin.user.input.player.placeholder"));
		$form->addDropdown(Lang::get("form.admin.user.dropdown"), $playerList);
		$form->addInput(Lang::get("form.admin.set_limit.input.text"), Lang::get("form.admin.set_limit.input.placeholder"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Main   $plugin
	 * @param Player $player
	 */
	private static function errorLimitEmpty(Main $plugin, Player $player){
		$form = new ModalForm(function(Player $player, bool $result) use ($plugin){
			if($result){
				self::setLimitForm($plugin, $player);
				return;
			}
		});
		$form->setTitle(Lang::get("form.error_title"));
		$form->setContent(Lang::get("form.admin.set_limit.error.empty_limit.content"));
		$form->setButton1(Lang::get("form.admin.set_limit.error.empty_limit.yes_button"));
		$form->setButton2(Lang::get("form.admin.set_limit.error.empty_limit.no_button"));
		$player->sendForm($form);
	}
	
	/**
	 * @param Player $player
	 * @param string $username
	 * @param int    $limit
	 */
	private static function userHomeLimitSetSuccessful(Player $player, string $username, int $limit){
		$form = new SimpleForm(function(Player $player, $result){
			if($result === null){
				return;
			}
		});
		$form->setTitle(Lang::get("form.admin.title"));
		$form->setContent(str_replace(["{player}", "{home_limit}"], [$username, $limit], Lang::get("form.admin.set_limit.success.content")));
		$form->addButton(Lang::get("form.admin.set_limit.success.button"));
		$player->sendForm($form);
	}
}