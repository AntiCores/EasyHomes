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

use JackMD\EasyHomes\command\HomeCommand;
use JackMD\EasyHomes\command\SubCommand;
use JackMD\EasyHomes\language\Lang;
use JackMD\EasyHomes\Main;
use pocketmine\command\CommandSender;

class HelpSubCommand extends SubCommand{
	
	/** @var HomeCommand $commands */
	private $commands;
	
	/**
	 * HelpSubCommand constructor.
	 *
	 * @param Main        $plugin
	 * @param HomeCommand $commands
	 */
	public function __construct(Main $plugin, HomeCommand $commands){
		parent::__construct($plugin);
		$this->commands = $commands;
	}
	
	/**
	 * @param CommandSender $sender
	 * @return bool
	 */
	public function canUse(CommandSender $sender): bool{
		return $sender->hasPermission("eh.command.help");
	}
	
	/**
	 * @return string
	 */
	public function getName(): string{
		return Lang::get("command.normal.help.name");
	}
	
	/**
	 * @return string
	 */
	public function getUsage(): string{
		return Lang::get("command.normal.help.usage");
	}
	
	/**
	 * @return string
	 */
	public function getDescription(): string{
		return Lang::get("command.normal.help.desc");
	}
	
	/**
	 * @return array
	 */
	public function getAliases(): array {
		return Lang::get("command.normal.help.aliases");
	}
	
	/**
	 * @param CommandSender $sender
	 * @param array         $args
	 * @return bool
	 */
	public function execute(CommandSender $sender, array $args): bool{
		if(empty($args)){
			$pageNumber = 1;
		}elseif(is_numeric($args[0])){
			$pageNumber = (int) array_shift($args);
			if($pageNumber <= 0){
				$pageNumber = 1;
			}
		}else{
			return false;
		}
		/** @var SubCommand[][] $commands */
		$commands = [];
		foreach($this->commands->getCommands() as $command){
			if($command->canUse($sender)){
				$commands[$command->getName()] = $command;
			}
		}
		ksort($commands, SORT_NATURAL | SORT_FLAG_CASE);
		$commands = array_chunk($commands, $sender->getScreenLineHeight());
		$pageNumber = (int) min(count($commands), $pageNumber);
		$sender->sendMessage("§6EasyHomes §aHelp Page §f[§c" . $pageNumber . "§f/§c" . count($commands) . "§f]");
		foreach($commands[$pageNumber - 1] as $command){
			$sender->sendMessage("§l§c»§r §2" . $command->getUsage() . " §l§b»§r §f" . $command->getDescription());
		}
		$sender->sendMessage(Lang::get("command.normal.help.note"));
		return true;
	}
}