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
namespace SignShop\Manager;

use SignShop\SignShop;
use pocketmine\plugin\Plugin;

class MoneyManager{
    private $PocketMoney = false, $EconomyS = false, $MassiveEconomy = false;
        
    public function __construct(SignShop $SignShop){        
        if($SignShop->getServer()->getPluginManager()->getPlugin("PocketMoney") instanceof Plugin){
            $version = explode(".", $SignShop->getServer()->getPluginManager()->getPlugin("PocketMoney")->getDescription()->getVersion());
            if($version[0] < 4){
                $SignShop->getLogger()->critical("The version of PocketMoney is too old! Please update PocketMoney to version 4.0.1");
                $SignShop->getServer()->shutdown();                
            }
            $this->PocketMoney = $SignShop->getServer()->getPluginManager()->getPlugin("PocketMoney");
        }
        
        elseif($SignShop->getServer()->getPluginManager()->getPlugin("EconomyAPI") instanceof Plugin){
            $version = explode(".", $SignShop->getServer()->getPluginManager()->getPlugin("EconomyAPI")->getDescription()->getVersion());
            if($version[1] < 5){
                $SignShop->getLogger()->critical("The version of EconomyS is too old! Please update EconomyS to version 5.5");
                $SignShop->getServer()->shutdown();                
            }
            $this->EconomyS = $SignShop->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        }
        
        elseif($SignShop->getServer()->getPluginManager()->getPlugin("MassiveEconomy") instanceof Plugin)
            $this->MassiveEconomy = $SignShop->getServer()->getPluginManager()->getPlugin("MassiveEconomy");
        
        else{
            $SignShop->getLogger()->critical("This plugin to work needs the plugin PocketMoney or EconomyS or MassiveEconomy.");
            $SignShop->getServer()->shutdown();
        }  
    }
    
    public function getValue(){
        if($this->PocketMoney) return "pm";
        if($this->EconomyS) return "$";
        if($this->MassiveEconomy) return $this->MassiveEconomy->getMoneySymbol();
        return "?";
    }    
    
    public function getMoney($player){
        if($this->PocketMoney) return $this->PocketMoney->getMoney($player);
        if($this->EconomyS) return $this->EconomyS->myMoney($player);  
        if($this->MassiveEconomy) return $this->MassiveEconomy->getMoney($player);
        return 0;
    }
    
    public function addMoney($player, $value){
        if($this->PocketMoney) $this->PocketMoney->setMoney($player, $this->getMoney($player) + $value);
        elseif($this->EconomyS) $this->EconomyS->setMoney($player, $this->getMoney($player) + $value);
        elseif($this->MassiveEconomy) $this->MassiveEconomy->setMoney($player, $this->getMoney($player) + $value);
        return false;
    }
    
    public function isExists($player){
        if($this->PocketMoney) return $this->PocketMoney->accountExists($player);
        elseif($this->EconomyS) return $this->EconomyS->isRegistered($player);
        elseif($this->MassiveEconomy) return $this->MassiveEconomy->isPlayerRegistered($player);
        return false;
    }
}