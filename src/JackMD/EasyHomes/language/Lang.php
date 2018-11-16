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

namespace JackMD\EasyHomes\language;

use JackMD\EasyHomes\Main;
use pocketmine\utils\Config;

class Lang{
	
	/** @var Config */
	private static $lang;
	/** @var string */
	private const LANG_VERSION = "SelenaGomez";
	
	/**
	 * @param Main $plugin
	 */
	public static function init(Main $plugin): void{
		$plugin->saveResource("lang.yml");
		self::checkLang($plugin);
		self::$lang = new Config($plugin->getDataFolder() . "lang.yml", Config::YAML);
	}
	
	/**
	 * @param Main $plugin
	 */
	private static function checkLang(Main $plugin): void{
		$lang = new Config($plugin->getDataFolder() . "lang.yml", Config::YAML);
		if((!$lang->exists("lang-version")) || ($lang->get("lang-version") !== self::LANG_VERSION)){
			rename($plugin->getDataFolder() . "lang.yml", $plugin->getDataFolder() . "lang_old.yml");
			$plugin->saveResource("lang.yml");
			$plugin->getLogger()->critical("Your language file is outdated.");
			$plugin->getLogger()->notice("Your old language has been saved as lang_old.yml and a new language file has been generated.");
			return;
		}
	}
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public static function get(string $key){
		return self::$lang->getNested($key);
	}
}