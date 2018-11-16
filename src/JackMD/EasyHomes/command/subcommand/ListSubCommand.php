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

namespace JackMD\EasyHomes\command\subcommand;

use JackMD\EasyHomes\command\SubCommand;
use JackMD\EasyHomes\language\Lang;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ListSubCommand extends SubCommand{
	
	/**
	 * @param CommandSender $sender
	 * @return bool
	 */
	public function canUse(CommandSender $sender): bool{
		return $sender->hasPermission("eh.command.list");
	}
	
	/**
	 * @return string
	 */
	public function getName(): string{
		return Lang::get("command.normal.list.name");
	}
	
	/**
	 * @return string
	 */
	public function getDescription(): string{
		return Lang::get("command.normal.list.desc");
	}
	
	/**
	 * @return string
	 */
	public function getUsage(): string{
		return Lang::get("command.normal.list.usage");
	}
	
	/**
	 * @return array
	 */
	public function getAliases(): array {
		return Lang::get("command.normal.list.aliases");
	}
	
	/**
	 * @param CommandSender|Player $sender
	 * @param array         $args
	 * @return bool
	 */
	public function execute(CommandSender $sender, array $args): bool{
		if(count($args) > 1){
			$sender->sendMessage($this->prefix . $this->getUsage());
			return false;
		}
		if(($list = $this->plugin->getProvider()->getHomes($sender->getName())) == null){
			$sender->sendMessage($this->prefix  . Lang::get("command.normal.list.no_home"));
			return false;
		}
		$sender->sendMessage($this->prefix . str_replace("{count}", count($list), Lang::get("command.normal.list.success")) . "\n" . TextFormat::DARK_GREEN . implode(TextFormat::GOLD . ", " . TextFormat::DARK_GREEN, $list));
		return true;
	}
}