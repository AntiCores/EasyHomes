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

namespace JackMD\EasyHomes;

use JackMD\EasyHomes\event\events\PlayerDeleteHomeEvent;
use JackMD\EasyHomes\event\events\PlayerRegisterEvent;
use JackMD\EasyHomes\event\events\PlayerSetHomeEvent;
use JackMD\EasyHomes\event\events\PlayerTeleportHomeEvent;
use pocketmine\level\Location;
use pocketmine\Player;

class EasyHomesAPI{

	/** @var Main */
	private $plugin;

	/**
	 * EasyHomesAPI constructor.
	 *
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}

	/**
	 * @return Main
	 */
	public final function getPlugin(): Main{
		return $this->plugin;
	}

	/**
	 * Registers a player into the database.
	 */
	public function registerPlayer(string $playerName): bool{
		$event = new PlayerRegisterEvent($this, $playerName);
		$event->call();
		$this->plugin->getProvider()->registerPlayer($playerName);
		return true;
	}

	public function setHome(string $player, string $home, Location $location, float $yaw, float $pitch): bool{
		$event = new PlayerSetHomeEvent($this, $player, $home, $location, $yaw, $pitch);
		$event->call();

		if($event->isCancelled()){
			return false;
		}

		$this->plugin->getProvider()->setHome($player, $home, $location, $yaw, $pitch);
		return true;
	}

	public function deleteHome(string $player, string $home): bool{
		$event = new PlayerDeleteHomeEvent($this, $player, $home);
		$event->call();

		if($event->isCancelled()){
			return false;
		}

		$this->plugin->getProvider()->deleteHome($player, $home);
		return true;
	}

	public function teleportToHome(Player $player, Location $homeLocation, bool $isAdmin = false): bool{
		$event = new PlayerTeleportHomeEvent($this, $player, $homeLocation, $isAdmin);
		$event->call();

		if($event->isCancelled()){
			return false;
		}

		$player->teleport($homeLocation);
		return true;
	}
}