![EasyHomes Banner](https://github.com/AntiCores/EasyHomes/blob/master/meta/EasyHomes.png)
# EasyHomes

| HitCount | License | Poggit | Release |
|:--:|:--:|:--:|:--:|
|[![HitCount](http://hits.dwyl.io/AntiCores/EasyHomes.svg)](http://hits.dwyl.io/AntiCores/EasyHomes)|[![GitHub license](https://img.shields.io/github/license/AntiCores/EasyHomes.svg)](https://github.com/AntiCores/EasyHomes/blob/master/LICENSE)|[![Poggit-CI](https://poggit.pmmp.io/ci.shield/AntiCores/EasyHomes/EasyHomes)](https://poggit.pmmp.io/ci/AntiCores/EasyHomes/EasyHomes)|[![](https://poggit.pmmp.io/shield.state/EasyHomes)](https://poggit.pmmp.io/p/EasyHomes)|

### A highly customizable and easy-to-use Homes plugin which supports both commands and forms for your Minecraft Bedrock Server running on PocketMine-MP. 

### Features

- Allows you to **create homes** on your server.
- Easy configuration and setup.
- You can change the language by editing the `lang.yml` file.
- Default commands and messages shown to the player can be changed via `lang.yml`.
- You can **set a limit to the maximum homes** that a player can set. Edit the default limit in `config.yml` to suit your needs.
- Two **storage** ways available: `Yaml` and `SQLite3` which can be set via `config.yml`.
- Neat **API** for developers looking for making addons or including this plugin into their own plugin.
- Support commands as well as **forms**. Both of which can work in conjunction.
- Support for **Admin Commands**.
- **Admins** can easily **monitor player homes** even when a player is **offline**.

### How to setup?

- Get the [.phar](https://poggit.pmmp.io/ci/AntiCores/EasyHomes/EasyHomes) of this plugin from [poggit](https://poggit.pmmp.io/ci/AntiCores/EasyHomes/EasyHomes)
- Put into your plugins folder.
- Restart the server.
- Enjoy...

### Commands and Permissions

- Normal Player Commands:

|Description|Command|Permission|Default|
|:--:|:--:|:--:|:--:|
|Home form|`/home`|`eh.command`|`true`|
|Home command|`/home [string:home]`|`eh.command.tp`|`true`|
|Teleport to home|`/home teleport [string:home]`|`eh.command.tp`|`true`|
|Delete a home|`/home delete [string:home]`|`eh.command.del`|`true`|
|Help command|`/home help`|`eh.command.help`|`true`|
|List homes|`/home list`|`eh.command.list`|`true`|
|Set a home|`/home set [string:home]`|`eh.command.set`|`true`|

- Admin Player Commands:

|Description|Command|Permission|Default|
|:--:|:--:|:--:|:--:|
|Admin form|`/homeadmin`|`eh.command.admin`|`op`|
|Delete a player home|`/homeadmin delete [string:player] [string:home]`|`eh.command.admin.del`|`op`|
|Get player's home limit|`/homeadmin getlimit [string:player]`|`eh.command.admin.limit.get`|`op`|
|Set player's home limit|`/homeadmin setlimit [string:player]`|`eh.command.admin.limit.set`|`op`|
|Help command|`/homeadmin help`|`eh.command.admin.help`|`op`|
|List player's homes|`/homeadmin list [string:player]`|`eh.command.admin.list`|`op`|
|Set player's home|`/homeadmin set [string:player] [string:home]`|`eh.command.admin.set`|`op`|
|Teleport to player's home|`/homeadmin teleport [string:player] [string:home]`|`eh.command.admin.tp`|`op`|

**_Note: Alias for every command exists. You can find detail in `lang.yml`._** 

### API

EasyHomes provides a simple API for developers wishing to use this plugin in theirs or make addons for it.<br />
- First you need to get hold of the plugin. You can do so by:<br />
```php
$easyHomes = Server::getInstance()->getPluginManager()->getPlugin("EasyHomes");
if($easyHomes instanceof \JackMD\EasyHomes\Main){
    //do whatever
}
```
- Then you need to get hold of the provider via:<br />
```php
$provider = $easyhomes->getProvider();
```
- Now you take a look at [ProviderInterface](https://github.com/AntiCores/EasyHomes/blob/master/src/JackMD/EasyHomes/provider/ProviderInterface.php) for the things you can access.
- Everything in it is easy and straight forward.
