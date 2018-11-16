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
use pocketmine\utils\Config;

class YamlProvider implements ProviderInterface{
	
	/** @var Main */
	private $plugin;
	
	/**
	 * YamlProvider constructor.
	 *
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}
	
	public function prepare(): void{
		if(!is_dir($this->plugin->getDataFolder() . "data/")){
			mkdir($this->plugin->getDataFolder() . "data/");
		}
	}
	
	/**
	 * @param string $player
	 */
	public function registerPlayer(string $player): void{
		if(!$this->playerExists($player)){
			$config = new Config($this->plugin->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
			$config->save();
			$this->setMaxHomes($player, (int) $this->plugin->getConfig()->get("default-home-limit"));
		}
	}
	
	public function playerExists(string $player): bool{
		$configs = [];
		foreach(glob($this->plugin->getDataFolder() . "data/" . "*.yml") as $config){
			$configs[] = str_replace([$this->plugin->getDataFolder() . "data/", ".yml"], ["", ""], $config);
		}
		$config = new Config($this->plugin->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
		if((in_array(strtolower($player), $configs)) && ($config->exists("max-homes"))){
			return true;
		}
		return false;
	}
	
	public function setHome(string $player, string $home, Location $location, float $yaw, float $pitch): void{
		$config = new Config($this->plugin->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
		$config->setNested("homes." . $home, Utils::__toArray($location, $yaw, $pitch));
		$config->save();
	}
	
	public function getHome(string $player, string $home): Location{
		$config = new Config($this->plugin->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
		$homeArray = $config->getNested("homes." . $home);
		
		$location = null;
		if($this->plugin->getServer()->isLevelGenerated($homeArray[5])){
			if(!$this->plugin->getServer()->isLevelLoaded($homeArray[5])){
				$this->plugin->getServer()->loadLevel($homeArray[5]);
			}
			$location = Utils::__toLocation($homeArray, $this->plugin->getServer()->getLevelByName($homeArray[5]));
		}
		return $location;
	}
	
	public function getHomes(string $player): ?array{
		$config = new Config($this->plugin->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
		return $config->exists("homes") ? array_keys($config->getAll()["homes"]) : null;
	}
	
	public function homeExists(string $player, string $home): bool{
		$config = new Config($this->plugin->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
		return ($config->getNested("homes." . $home) !== null) ? true : false;
	}
	
	public function getMaxHomes(string $player): int{
		$config = new Config($this->plugin->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
		return (int) $config->get("max-homes");
	}
	
	public function setMaxHomes(string $player, int $count): void{
		$config = new Config($this->plugin->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
		$config->set("max-homes", $count);
		$config->save();
	}
	
	public function deleteHome(string $player, string $home): void{
		$config = new Config($this->plugin->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
		$config->removeNested("homes." . $home);
		$config->save();
	}
	
	public function close(): void{
		//useless in this case...
	}
}