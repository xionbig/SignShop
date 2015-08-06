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
namespace SignShop\EventListener;

use SignShop\SignShop;
use pocketmine\event\Listener;
use pocketmine\event\level\LevelLoadEvent;

class LevelEvent implements Listener{
    private $SignShop;
    
    public function __construct(SignShop $SignShop) {
        $this->SignShop = $SignShop;        
    }
    
    public function levelLoad(LevelLoadEvent $event){
        $this->SignShop->getSignManager()->reload($event->getLevel()->getName());
    }    
}