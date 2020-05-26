<?php

namespace hashTable;

defined('__ROOT__') || define('__ROOT__', dirname(dirname(__FILE__)));

require_once 'Key.php';
require_once  __ROOT__.'/Node.php';

use \Node;
use \Exception;

class Entry extends Node{
    protected Key $key;

    public function __construct(Key $key, $value, ?Entry $next){
        parent::__construct($value, $next);
        $this->key = $key;
    }

    public function key():Key{
        return $this->key;
    }
    public function getKey(): int{
        return $this->key->getKey();
    }
    public function equals():bool{
        $entry = func_get_arg(0);
        try{
            if(is_object($entry) && get_class($entry) === 'Entry')
                throw new Exception('The that you wanna compare must be an Entry');
        }catch(Exception $e){
            echo $e->getMessage();
            die();
        }
        return $this->key->equals($entry->Key());
    }
}