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

namespace JackMD\EasyHomes\command;

use JackMD\EasyHomes\command\subcommand\DefaultSubCommand;
use JackMD\EasyHomes\command\subcommand\DeleteSubCommand;
use JackMD\EasyHomes\command\subcommand\HelpSubCommand;
use JackMD\EasyHomes\command\subcommand\ListSubCommand;
use JackMD\EasyHomes\command\subcommand\SetSubCommand;
use JackMD\EasyHomes\command\subcommand\TeleportSubCommand;
use JackMD\EasyHomes\forms\HomeForm;
use JackMD\EasyHomes\language\Lang;
use JackMD\EasyHomes\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;

class HomeCommand extends PluginCommand{
	
	/** @var Main */
	private $plugin;
	
	/** @var SubCommand[] $subCommands */
	private $subCommands = [];
	/** @var SubCommand[] $aliasSubCommands */
	private $aliasSubCommands = [];
	
	/**
	 * HomeCommand constructor.
	 *
	 * @param string $name
	 * @param Main   $plugin
	 */
	public function __construct(string $name, Main $plugin){
		parent::__construct($name, $plugin);
		$this->plugin = $plugin;
		
		$this->setPermission("eh.command");
		$this->setDescription(Lang::get("command.main.default.desc"));
		$this->setUsage(Lang::get("command.main.default.usage"));
		$this->setAliases(Lang::get("command.main.default.alias"));
		
		$this->loadSubCommand(new HelpSubCommand($plugin, $this));
		$this->loadSubCommand(new DefaultSubCommand($plugin));
		$this->loadSubCommand(new ListSubCommand($plugin));
		$this->loadSubCommand(new SetSubCommand($plugin));
		$this->loadSubCommand(new DeleteSubCommand($plugin));
		$this->loadSubCommand(new TeleportSubCommand($plugin));
		
		$plugin->getLogger()->debug("Home commands registered.");
	}
	
	/**
	 * @param SubCommand $command
	 */
	private function loadSubCommand(SubCommand $command): void{
		$this->subCommands[$command->getName()] = $command;
		foreach($command->getAliases() as $alias){
			if($alias != ""){
				$this->aliasSubCommands[$alias] = $command;
			}
		}
	}
	
	/**
	 * @return SubCommand[]
	 */
	public function getCommands(): array{
		return $this->subCommands;
	}
	
	/**
	 * @param CommandSender $sender
	 * @param string        $alias
	 * @param string[]      $args
	 * @return bool
	 */
	public function execute(CommandSender $sender, string $alias, array $args): bool{
		if(!$sender instanceof Player){
			$sender->sendMessage($this->plugin->prefix . Lang::get("command.in_game"));
			return false;
		}
		if(!isset($args[0])){
			HomeForm::mainForm($this->plugin, $sender);
			return true;
		}
		$home = $args;
		$subCommand = strtolower(array_shift($args));
		if(isset($this->subCommands[$subCommand])){
			$command = $this->subCommands[$subCommand];
		}elseif(isset($this->aliasSubCommands[$subCommand])){
			$command = $this->aliasSubCommands[$subCommand];
		}else{
			if((count($home) > 1) || (!isset($home[0]))){
				$sender->sendMessage($this->plugin->prefix . Lang::get("command.normal.default.usage"));
				return false;
			}
			$playerName = $sender->getName();
			if($this->plugin->getProvider()->getHomes($playerName) !== null){
				if(count($this->plugin->getProvider()->getHomes($playerName)) > $this->plugin->getProvider()->getMaxHomes($playerName)){
					$sender->sendMessage($this->plugin->prefix . str_replace(["{homes}", "{max_homes}"], [count($this->plugin->getProvider()->getHomes($playerName)), $this->plugin->getProvider()->getMaxHomes($playerName)], Lang::get("command.normal.teleport.max_homes")));
					return false;
				}
			}
			if(!$this->plugin->getProvider()->homeExists($playerName, $home[0])){
				$sender->sendMessage($this->plugin->prefix . str_replace("{home_name}", $home[0], Lang::get("command.normal.teleport.home_not_exist")));
				return false;
			}
			$homeLocation = $this->plugin->getProvider()->getHome($playerName, $home[0]);
			$sender->teleport($homeLocation);
			$sender->sendMessage($this->plugin->prefix . str_replace("{home_name}", $home[0], Lang::get("command.normal.teleport.success")));
			return true;
		}
		
		if($command->canUse($sender)){
			$command->execute($sender, $args);
		}else{
			$sender->sendMessage($this->plugin->prefix . Lang::get("command.not_found"));
		}
		return true;
	}
}