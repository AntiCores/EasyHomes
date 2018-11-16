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

use JackMD\EasyHomes\command\subcommand\admin\DeleteSubCommand;
use JackMD\EasyHomes\command\subcommand\admin\GetLimitSubCommand;
use JackMD\EasyHomes\command\subcommand\admin\HelpSubCommand;
use JackMD\EasyHomes\command\subcommand\admin\ListSubCommand;
use JackMD\EasyHomes\command\subcommand\admin\SetLimitSubCommand;
use JackMD\EasyHomes\command\subcommand\admin\SetSubCommand;
use JackMD\EasyHomes\command\subcommand\admin\TeleportSubCommand;
use JackMD\EasyHomes\forms\HomeAdminForm;
use JackMD\EasyHomes\language\Lang;
use JackMD\EasyHomes\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;

class HomeAdminCommand extends PluginCommand{
	
	/** @var Main */
	private $plugin;
	/** @var SubCommand[] $subCommands */
	private $subCommands = [];
	/** @var SubCommand[] $aliasSubCommands */
	private $aliasSubCommands = [];
	
	/**
	 * HomeAdminCommand constructor.
	 *
	 * @param string $name
	 * @param Main   $plugin
	 */
	public function __construct(string $name, Main $plugin){
		parent::__construct($name, $plugin);
		$this->plugin = $plugin;
		
		$this->setPermission("eh.command.admin");
		$this->setDescription(Lang::get("command.main.admin.desc"));
		$this->setUsage(Lang::get("command.main.admin.usage"));
		$this->setAliases(Lang::get("command.main.admin.alias"));
		
		$this->loadSubCommand(new HelpSubCommand($plugin, $this));
		$this->loadSubCommand(new DeleteSubCommand($plugin));
		$this->loadSubCommand(new SetLimitSubCommand($plugin));
		$this->loadSubCommand(new GetLimitSubCommand($plugin));
		$this->loadSubCommand(new ListSubCommand($plugin));
		$this->loadSubCommand(new SetSubCommand($plugin));
		$this->loadSubCommand(new TeleportSubCommand($plugin));
		
		$plugin->getLogger()->debug("Admin home commands registered.");
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
		if((!$sender->hasPermission("eh.command.admin"))){
			$sender->sendMessage($this->plugin->prefix . Lang::get("command.main.admin.admin_only"));
			return false;
		}
		if(!isset($args[0])){
			if($sender instanceof Player){
				HomeAdminForm::mainForm($this->plugin, $sender);
				return true;
			}else{
				$sender->sendMessage($this->plugin->prefix . Lang::get("command.main.admin.usage"));
				return false;
			}
		}
		$subCommand = strtolower(array_shift($args));
		if(isset($this->subCommands[$subCommand])){
			$command = $this->subCommands[$subCommand];
		}elseif(isset($this->aliasSubCommands[$subCommand])){
			$command = $this->aliasSubCommands[$subCommand];
		}else{
			$sender->sendMessage($this->plugin->prefix . Lang::get("command.not_found"));
			return true;
		}
		if($command->canUse($sender)){
			$command->execute($sender, $args);
		}else{
			$sender->sendMessage($this->plugin->prefix . Lang::get("command.main.admin.admin_only"));
		}
		return true;
	}
}