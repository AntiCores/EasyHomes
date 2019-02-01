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

namespace JackMD\EasyHomes\event\events;

use JackMD\EasyHomes\EasyHomesAPI;
use JackMD\EasyHomes\event\BaseEvent;
use pocketmine\event\Cancellable;

class PlayerDeleteHomeEvent extends BaseEvent implements Cancellable{

	/** @var string */
	private $playerName;
	/** @var string */
	private $home;

	/**
	 * PlayerDeleteHomeEvent constructor.
	 *
	 * @param EasyHomesAPI $api
	 * @param string       $playerName
	 * @param string       $home
	 */
	public function __construct(EasyHomesAPI $api, string $playerName, string $home){
		parent::__construct($api);

		$this->playerName = $playerName;
		$this->home = $home;
	}

	/**
	 * @return string
	 */
	public function getPlayerName(): string{
		return $this->playerName;
	}

	/**
	 * @param string $playerName
	 */
	public function setPlayerName(string $playerName): void{
		$this->playerName = $playerName;$this->getPlugin(
	}

	/**
	 * @return string
	 */
	public function getHomeName(): string{
		return $this->home;
	}

	/**
	 * @param string $home
	 */
	public function setHomeName(string $home): void{
		$this->home = $home;
	}
}
