<?php
declare(strict_types = 1);

namespace JackMD\EasyHomes\database;

use JackMD\EasyHomes\EasyHomes;
use JackMD\EasyHomes\utils\EasyHomesQuery;
use Paroxity\ParoxityEcon\ParoxityEcon;
use Paroxity\ParoxityEcon\Utils\ParoxityEconQuery;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class EasyHomesDatabase{

	/** @var EasyHomes */
	private $plugin;
	/** @var DataConnector */
	private $connector;

	public function __construct(ParoxityEcon $plugin){
		$this->plugin = $plugin;

		$this->initDatabase();
	}

	private function initDatabase(): void{
		$this->connector = libasynql::create(
			$this->plugin,
			$this->plugin->getConfig()->get("database"),
			[
				"sqlite" => "stmts/sqlite.sql",
				//"mysql"  => "stmts/mysql.sql"
			]
		);

		$this->connector->executeGeneric(EasyHomesQuery::INIT, [], function(): void{
			$this->plugin->getLogger()->debug("Database Initialized.");
		});
	}

	public function getConnector(): DataConnector{
		return $this->connector;
	}

	public function close(): void{
		$this->connector->close();
	}

}