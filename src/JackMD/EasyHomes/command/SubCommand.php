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

use JackMD\EasyHomes\Main;
use pocketmine\command\CommandSender;

abstract class SubCommand{
	
	/** @var Main $plugin */
	public $plugin;
	/** @var string */
	public $prefix;
	
	/**
	 * @param Main   $plugin
	 */
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
		$this->prefix = $plugin->prefix;
	}
	
	/**
	 * @param CommandSender $sender
	 * @return bool
	 */
	public abstract function canUse(CommandSender $sender): bool;
	
	/**
	 * @return string
	 */
	public abstract function getUsage(): string;
	
	/**
	 * @return string
	 */
	public abstract function getName(): string;
	
	/**
	 * @return string
	 */
	public abstract function getDescription(): string;
	
	/**
	 * @return array
	 */
	public abstract function getAliases(): array;
	
	/**
	 * @param CommandSender $sender
	 * @param string[]      $args
	 * @return bool
	 */
	public abstract function execute(CommandSender $sender, array $args): bool;
}