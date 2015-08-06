<?php
/**
 * SignShop Copyright (C) 2015 xionbig
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @author xionbig
 * @link http://xionbig.eu/plugins/SignShop 
 * @link http://forums.pocketmine.net/plugins/signshop.668/
 * @version 1.1.0
 */
namespace SignShop;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use Provider\MySQLProvider;
use Provider\SQLiteProvider;
use Provider\YAMLProvider;

class SignShop extends PluginBase implements Listener{ 
    public $temp = [];
    private $setup, $provider;
    private $manager = [];    
    
    public function onEnable(){
        $dataResources = $this->getDataFolder()."/resources/";
        if(!file_exists($this->getDataFolder())) 
            @mkdir($this->getDataFolder(), 0755, true);
        if(!file_exists($dataResources)) 
            @mkdir($dataResources, 0755, true);
        
        $this->setup = new Config($dataResources. "config.yml", Config::YAML, [
                "version" => "oneone",
                "signCreated" => "all",
                "lastChange" => time(),
                "server" => "http://xionbig.eu/plugins/SignShop/translate/download.php",
                "dataProvider" => "YAML",
                "dataProviderSettings" => ["host" => "127.0.0.1",         
                                            "port" => 3306,
                                            "user" => "usernameDatabase",
                                            "password" => "passwordDatabase",
                                            "database" => "databaseName"]
            ]);
        if($this->setup->get("signCreated") == "list")
            $this->setup->set("signCreated", "admin");
        
        $this->setup->save();
        
        switch(strtolower($this->setup->get("dataProvider"))){           
            case "yml":
            case "yaml":
                $this->provider = new Provider\YAMLProvider($this);
                break;
            case "sql":
            case "sqlite":
            case "sqlite3":
                $this->provider = new Provider\SQLiteProvider($this);
                break;
            case "mysqli":
            case "mysql":
                $this->provider = new Provider\MySQLProvider($this);
                break;
            default:
                $this->getLogger()->critical("The field 'dataProvider' in config.yml is incorrect! Use the provider YAML"); 
                $this->provider = new Provider\YAMLProvider($this);
        }
        
        $this->manager["message"] = new Manager\MessageManager($this, $dataResources);
        $this->manager["command"] = new Command\SignShopCommand($this); 
        $this->manager["items"] = new Manager\ListItems();
        $this->manager["money"] = new Manager\MoneyManager($this);
        $this->manager["sign"] = new Manager\SignManager($this);

        $this->getServer()->getCommandMap()->register("sign", $this->manager["command"]);
        
        $this->getServer()->getPluginManager()->registerEvents(new EventListener\LevelEvent($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener\PlayerSpawnEvent($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener\PlayerTouchEvent($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener\PlayerBlockBreakEvent($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener\PlayerSignCreateEvent($this), $this);
        $this->getServer()->getLogger()->info(TextFormat::GOLD."SignShop v".$this->getDescription()->getVersion()." Enabled!");
    }
    
    public function messageManager(){
        return $this->manager["message"];
    }
    public function getMoneyManager(){
        return $this->manager["money"];
    }
    
    public function getSignManager(){
        return $this->manager["sign"];
    }
    
    public function getItems(){
        return $this->manager["items"];
    }    
    
    public function getSetup(){
        return $this->setup;
    }
    
    public function getProvider(){
        return $this->provider;
    }
             
    public function onDisable(){
        unset($this->temp);
        if($this->setup instanceof Config)
            $this->setup->save();
        if($this->provider instanceof MySQLProvider || $this->provider instanceof SQLiteProvider || $this->provider instanceof YAMLProvider) 
            $this->provider->onDisable();
        $this->getSignManager()->onDisable();
    }
}