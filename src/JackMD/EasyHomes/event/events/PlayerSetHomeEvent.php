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
use pocketmine\level\Location;

class PlayerSetHomeEvent extends BaseEvent implements Cancellable{

	/** @var string */
	private $playerName;
	/** @var string */
	private $home;
	/** @var Location */
	private $location;
	/** @var float */
	private $yaw;
	/** @var float */
	private $pitch;
	/** @var bool */
	private $isAdmin;

	public function __construct(EasyHomesAPI $api, string $playerName, string $home, Location $location, float $yaw, float $pitch, bool $isAdmin = false){
		parent::__construct($api);

		$this->playerName = $playerName;
		$this->home = $home;
		$this->location = $location;
		$this->yaw = $yaw;
		$this->pitch = $pitch;
		$this->isAdmin = $isAdmin;
	}

	public function getPlayerName(): string{
		return $this->playerName;
	}

	public function getHomeName(): string{
		return $this->home;
	}

	public function setHomeName(string $home): void{
		$this->home = $home;
	}

	public function getLocation(): Location{
		return $this->location;
	}

	public function setLocation(Location $location): void{
		$this->location = $location;
	}

	public function getYaw(): float{
		return $this->yaw;
	}

	public function setYaw(float $yaw): void{
		$this->yaw = $yaw;
	}

	public function getPitch(): float{
		return $this->pitch;
	}

	public function setPitch(float $pitch): void{
		$this->pitch = $pitch;
	}

	public function isExecutedByAdmin(): bool{
		return $this->isAdmin;
	}
}
