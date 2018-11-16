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

namespace JackMD\EasyHomes\utils;

use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\OfflinePlayer;
use pocketmine\Player;
use pocketmine\Server;

class Utils{
	
	/**
	 * Converts the location, yaw and pitch to array.
	 *
	 * @param Location $location
	 * @param float    $yaw
	 * @param float    $pitch
	 * @return array
	 */
	public static function __toArray(Location $location, float $yaw = 0.0, float $pitch = 0.0): array{
		return [(int) $location->getX(), (int) $location->getY(), (int) $location->getZ(), $yaw, $pitch, $location->getLevel()->getFolderName()];
	}
	
	/**
	 * Converts the array to Location.
	 *
	 * @param array      $array
	 * @param Level|null $level
	 * @return Location
	 */
	public static function __toLocation(array $array, Level $level = null): Location{
		return new Location($array[0], $array[1], $array[2], $array[3], $array[4], $level);
	}
	
	/**
	 * Checks if a player is online/offline or null.
	 *
	 * @param string $playerName
	 * @return null|OfflinePlayer|Player
	 */
	public static function checkPlayer(string $playerName){
		$player = Server::getInstance()->getOfflinePlayer($playerName);
		if($player instanceof Player){
			return $player;
		}
		if($player instanceof OfflinePlayer){
			if($player->hasPlayedBefore()){
				return $player;
			}else{
				return null;
			}
		}
		return null;
	}
}