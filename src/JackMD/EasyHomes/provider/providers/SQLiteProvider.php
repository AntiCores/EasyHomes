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

namespace JackMD\EasyHomes\provider\providers;

use JackMD\EasyHomes\Main;
use JackMD\EasyHomes\provider\ProviderInterface;
use JackMD\EasyHomes\utils\Utils;
use pocketmine\level\Location;

class SQLiteProvider implements ProviderInterface{
	
	/** @var Main */
	private $plugin;
	
	/** @var \SQLite3 */
	private $homesDB;
	
	/**
	 * SQLiteProvider constructor.
	 *
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}
	
	public function prepare(): void{
		$this->homesDB = new \SQLite3($this->plugin->getDataFolder() . "easyHomes.db");
		$this->homesDB->exec("CREATE TABLE IF NOT EXISTS master (ID INT PRIMARY KEY, player TEXT COLLATE NOCASE, home TEXT, x INT, y INT, z INT, yaw FLOAT, pitch FLOAT, world TEXT)");
		$this->homesDB->exec("CREATE TABLE IF NOT EXISTS homecountlimit (player TEXT PRIMARY KEY COLLATE NOCASE, count INT)");
	}
	
	/**
	 * @param string $player
	 */
	public function registerPlayer(string $player): void{
		if(!$this->playerExists($player)){
			$this->setMaxHomes($player, (int) $this->plugin->getConfig()->get("default-home-limit"));
		}
	}
	
	/**
	 * @param string $player
	 * @return bool
	 */
	public function playerExists(string $player): bool{
		$playerName = strtolower($player);
		$result = $this->homesDB->query("SELECT player FROM master WHERE player = '$playerName';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}
	
	/**
	 * @param string   $player
	 * @param string   $home
	 * @param Location $location
	 * @param float    $yaw
	 * @param float    $pitch
	 */
	public function setHome(string $player, string $home, Location $location, float $yaw, float $pitch): void{
		if($this->homeExists($player, $home)){
			$this->deleteHome($player, $home);
		}
		$data = Utils::__toArray($location, $yaw, $pitch);
		$stmt = $this->homesDB->prepare("INSERT OR REPLACE INTO master (player, home, x, y, z, yaw, pitch, world) VALUES (:player, :home, :x, :y, :z, :yaw, :pitch, :world)");
		$stmt->bindValue(":player", strtolower($player));
		$stmt->bindValue(":home", $home);
		$stmt->bindValue(":x", $data[0]);
		$stmt->bindValue(":y", $data[1]);
		$stmt->bindValue(":z", $data[2]);
		$stmt->bindValue(":yaw", $data[3]);
		$stmt->bindValue(":pitch", $data[4]);
		$stmt->bindValue(":world", $data[5]);
		$stmt->execute();
	}
	
	/**
	 * @param string $player
	 * @param string $home
	 * @return Location
	 */
	public function getHome(string $player, string $home): Location{
		$playerName = strtolower($player);
		$result = $this->homesDB->query("SELECT * FROM master WHERE player = '$playerName' AND home = '$home'");
		$resultArray = $result->fetchArray(SQLITE3_ASSOC);
		$location = null;
		if($this->plugin->getServer()->isLevelGenerated($resultArray["world"])){
			if(!$this->plugin->getServer()->isLevelLoaded($resultArray["world"])){
				$this->plugin->getServer()->loadLevel($resultArray["world"]);
			}
			$location = new Location($resultArray["x"], $resultArray["y"], $resultArray["z"], $resultArray["yaw"], $resultArray["pitch"], $this->plugin->getServer()->getLevelByName($resultArray["world"]));
		}
		return $location;
	}
	
	/**
	 * @param string $player
	 * @return array|null
	 */
	public function getHomes(string $player): ?array{
		$playerName = strtolower($player);
		$homes = [];
		$result = $this->homesDB->query("SELECT home FROM master WHERE player = '$playerName'");
		$i = 0;
		while($resultArr = $result->fetchArray(SQLITE3_ASSOC)){
			$homes[] = $resultArr['home'];
			$i = $i + 1;
		}
		return $homes;
	}
	
	/**
	 * @param string $player
	 * @param string $home
	 * @return bool
	 */
	public function homeExists(string $player, string $home): bool{
		$playerName = strtolower($player);
		$result = $this->homesDB->query("SELECT home FROM master WHERE player = '$playerName' AND home = '$home';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}
	
	/**
	 * @param string $player
	 * @return int
	 */
	public function getMaxHomes(string $player): int{
		$playerName = strtolower($player);
		$result = $this->homesDB->query("SELECT count FROM homecountlimit WHERE player = '$playerName'");
		$resultArray = $result->fetchArray(SQLITE3_ASSOC);
		return (int) $resultArray["count"];
	}
	
	/**
	 * @param string $player
	 * @param int    $count
	 */
	public function setMaxHomes(string $player, int $count): void{
		$stmt = $this->homesDB->prepare("INSERT OR REPLACE INTO homecountlimit (player, count) VALUES (:player, :count)");
		$stmt->bindValue(":player", strtolower($player));
		$stmt->bindValue(":count", $count);
		$stmt->execute();
	}
	
	/**
	 * @param string $player
	 * @param string $home
	 */
	public function deleteHome(string $player, string $home): void{
		$playerName = strtolower($player);
		$stmt = $this->homesDB->prepare("DELETE FROM master WHERE player = '$playerName' AND home = '$home'");
		$stmt->execute();
	}
	
	public function close(): void{
		$this->homesDB->close();
	}
}