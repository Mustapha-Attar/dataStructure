<?php

namespace hashTable;


class Key{
    protected int $key, $hash;

    public function __construct(int $key){
        $this->key = $key;
        $this->hashKey();
    }

    public function hashKey():void{
//        $x = $this->key;
//        $this->hash = 3 * (pow($x, 2)) - (6 * $x) + 9;
        $this->hash = $this->key * 5;
    }

    public function getKey(): int{
        return $this->key;
    }

    public function getHash(): int{
        return $this->hash;
    }

    public function equals(Key $other):bool{
        if($this->hash !== $other->getHash()) return false;
        return $this->key === $other->getKey();
    }

    public static function create(int $key):Key{
        return new Key($key);
    }
}