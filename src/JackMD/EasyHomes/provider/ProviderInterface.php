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

namespace JackMD\EasyHomes\provider;

use JackMD\EasyHomes\Main;
use pocketmine\level\Location;

interface ProviderInterface{
	
	/**
	 * ProviderInterface constructor.
	 *
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin);
	
	/**
	 * Prepare the database. Developers wishing to make a provider should only use this and no one else.
	 */
	public function prepare(): void;
	
	/**
	 * Registers a player into the database.
	 *
	 * @param string $player
	 */
	public function registerPlayer(string $player): void;
	
	/**
	 * Check if a player already exists in the database.
	 *
	 * @param string $player
	 * @return bool
	 */
	public function playerExists(string $player): bool;
	
	/**
	 * Set home of the player.
	 *
	 * @param string   $player
	 * @param string   $home
	 * @param Location $location
	 * @param float    $yaw
	 * @param float    $pitch
	 */
	public function setHome(string $player, string $home, Location $location, float $yaw, float $pitch): void;
	
	/**
	 * Returns the home of a player.
	 *
	 * @param string $player
	 * @param string $home
	 * @return Location
	 */
	public function getHome(string $player, string $home): Location;
	
	/**
	 * @param string $player
	 * @return array|null
	 */
	public function getHomes(string $player): ?array;
	
	/**
	 * Check if the home of a player exists in the database.
	 *
	 * @param string $player
	 * @param string $home
	 * @return bool
	 */
	public function homeExists(string $player, string $home): bool;
	
	/**
	 * Returns the max homes limit of a player.
	 *
	 * @param string $player
	 * @return int
	 */
	public function getMaxHomes(string $player): int;
	
	/**
	 * Set the max homes limit of a player.
	 *
	 * @param string $player
	 * @param int    $count
	 */
	public function setMaxHomes(string $player, int $count): void;
	
	/**
	 * Delete a players home.
	 *
	 * @param string $player
	 * @param string $home
	 */
	public function deleteHome(string $player, string $home): void;
	
	/**
	 * Closes the database. Developers wishing to make a provider should only use this and no one else.
	 */
	public function close(): void;
}