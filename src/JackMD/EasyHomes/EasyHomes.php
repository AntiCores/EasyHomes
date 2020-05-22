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
use function intval;
use function rename;
use function time;

class EasyHomes extends PluginBase{

	/** @var int */
	private const CONFIG_VERSION = 1;

	/** @var string */
	public $prefix = "§a[§eEasy§6Homes§a]§r ";

	public function onEnable(): void{
		$this->checkVirions();
		$this->checkConfig();


	}

	private function checkVirions(): void{
		if(!class_exists(SimpleForm::class)){
			throw new \RuntimeException("EasyHomes plugin will only work if you use the plugin phar from Poggit.");
		}
	}

	private function checkConfig(): void{
		$config = $this->getConfig();
		$dataFolder = $this->getDataFolder();

		if((!$config->exists("config-version")) || (intval($config->get("config-version")) !== self::CONFIG_VERSION)){
			$old = "config_old.yml";

			if(!rename($dataFolder . "config.yml", $dataFolder . $old)){
				$old = "config_old_" . time() . "yml";

				rename($dataFolder . "config.yml", $dataFolder . $old);
			}

			$this->saveResource("config.yml");
			$this->getLogger()->error("Your configuration file is outdated.");
			$this->getLogger()->error("Your old configuration has been saved as $old and a new configuration file has been generated.");
		}
	}
}
