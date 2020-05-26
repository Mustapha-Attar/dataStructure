<?php


namespace hashTable;

require_once 'HashTableOpenAddressing.php';

class HashTableQuadraticProbing extends HashTableOpenAddressing{
    public function __construct(int $capacity = 16, float $loadFactor = .5){
        parent::__construct($capacity, $loadFactor);
    }

    protected function setupProbing($key): void{
    }

    protected function probe(int $x): int{
        return ($x * $x + $x) / 2;
    }

    protected function increaseCapacity(): void{
        $this->capacity = self::nextPowerOfTwo($this->capacity);
    }

    //by power of 2, I mean that the base is 2
    public static function nearestPowerOfTwo(int $n):int{
        return pow(2, ceil(log($n, 2)));
    }

    //by power of 2, I mean that the base is 2
    public static function nextPowerOfTwo(int $n):int{
        return self::nearestPowerOfTwo($n) * 2;
    }

    //make sure that the capacity is a power of 2
    //by power of 2, I mean that the base is 2
    protected function adjustCapacity(): void{
        $n = self::nearestPowerOfTwo($this->capacity);
        $this->capacity = $n === $this->capacity? $this->capacity: $n;
    }
}