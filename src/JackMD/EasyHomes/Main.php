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

use JackMD\EasyHomes\command\HomeAdminCommand;
use JackMD\EasyHomes\command\HomeCommand;
use JackMD\EasyHomes\language\Lang;
use JackMD\EasyHomes\provider\ProviderInterface;
use JackMD\EasyHomes\provider\providers\SQLiteProvider;
use JackMD\EasyHomes\provider\providers\YamlProvider;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase{
	
	/** @var string */
	private const CONFIG_VERSION = "TaylorSwift";
	/** @var string */
	public $prefix = "§a[§eEasy§6Homes§a]§r ";
	/** @var ProviderInterface */
	private $provider;
	
	public function onEnable(): void{
		$this->checkFormAPI();
		$this->saveDefaultConfig();
		$this->checkConfig();
		$this->initLang();
		$this->setProvider();
		$this->getProvider()->prepare();
		$this->registerCommands();
		$this->getLogger()->info("EasyHomes Plugin Enabled.");
	}
	
	private function checkFormAPI(): void{
		if(!class_exists(SimpleForm::class)){
			throw new \RuntimeException("EasyHomes plugin will only work if you use the plugin phar from Poggit.");
		}
	}
	
	private function checkConfig(): void{
		$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		if((!$config->exists("config-version")) || ($config->get("config-version") !== self::CONFIG_VERSION)){
			rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config_old.yml");
			$this->saveResource("config.yml");
			$this->getLogger()->error("Your configuration file is outdated.");
			$this->getLogger()->error("Your old configuration has been saved as config_old.yml and a new configuration file has been generated.");
		}
	}
	
	private function initLang(): void{
		Lang::init($this);
	}
	
	private function setProvider(): void{
		$providerName = $this->getConfig()->get("data-provider");
		$provider = null;
		switch(strtolower($providerName)){
			case "sqlite":
				$provider = new SQLiteProvider($this);
				$this->getLogger()->notice("SQLiteProvider successfully enabled.");
				break;
			case "yaml":
				$provider = new YamlProvider($this);
				$this->getLogger()->notice("YamlProvider successfully enabled.");
				break;
			default:
				$this->getLogger()->error("Please set a valid data-provider in config.yml. Disabling plugin...");
				$this->getServer()->getPluginManager()->disablePlugin($this);
				break;
		}
		if($provider instanceof ProviderInterface){
			$this->provider = $provider;
		}
	}
	
	/**
	 * @return ProviderInterface
	 */
	public function getProvider(): ProviderInterface{
		return $this->provider;
	}
	
	private function registerCommands(): void{
		$this->getServer()->getCommandMap()->register("easyhomes", new HomeCommand(Lang::get("command.main.default.name"), $this));
		$this->getServer()->getCommandMap()->register("easyhomes", new HomeAdminCommand(Lang::get("command.main.admin.name"), $this));
	}
	
	public function onDisable(): void{
		if($this->isValidProvider()){
			$this->getProvider()->close();
		}
	}
	
	/**
	 * @return bool
	 */
	private function isValidProvider(): bool{
		if(!isset($this->provider) || ($this->provider === null) || !($this->provider instanceof ProviderInterface)){
			return false;
		}
		return true;
	}
}