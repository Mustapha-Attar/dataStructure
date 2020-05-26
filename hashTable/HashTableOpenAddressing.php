<?php


namespace hashTable;

require_once 'Key.php';

use \Exception;

abstract class HashTableOpenAddressing{
    protected float $loadFactor;
    protected int $capacity, $threshold, $modificationCount = 0;

    // 'usedBuckets' counts the total number of used buckets inside the
    // hash-table (includes cells marked as deleted). While 'keyCount'
    // tracks the number of unique keys currently inside the hash-table.
    protected int $usedBuckets = 0, $keysCount = 0;

    // These arrays store the key-value pairs.
    protected array $keys = [], $values = [];

    protected const TOMBSTONE = 'TOMBSTONE';
    protected const DEFAULT_CAPACITY = 10;
    protected const DEFAULT_LOADFACTOR = .5;

    public function __construct(int $capacity = 10, float $loadFactor = .5){
        try{
            if($capacity <= 0)
                throw new Exception('Illegal capacity');
            if($loadFactor <= 0 || $loadFactor > 1)
                throw new Exception('Illegal Load factor');
        }catch(Exception $e){
            echo $e->getMessage();
            die;
        }

        $this->capacity = $capacity;
        $this->loadFactor = $loadFactor;
        $this->adjustCapacity();
        $this->threshold = $this->capacity * $this->loadFactor;
    }

    // These three methods are used to dictate how the probing is to actually
    // occur for whatever open addressing scheme you are implementing.
    abstract protected function setupProbing($key):void;

    abstract protected function probe(int $x):int;

    // Adjusts the capacity of the hash table after it's been made larger.
    // This is important to be able to override because the size of the hashtable
    // controls the functionality of the probing function.
    abstract protected function adjustCapacity():void;

    // Increases the capacity of the hash table.
    protected function increaseCapacity():void{
        $this->capacity = ($this->capacity * 2) + 1;
    }

    protected function clear():void{
        $this->keys = [];
        $this->values = [];
        $this->usedBuckets = $this->keysCount = 0;

        $this->modificationCount++;
    }

    public function size():int{
        return $this->keysCount;
    }

    public function capacity():int{
        return $this->capacity;
    }

    public function isEmpty():bool{
        return $this->keysCount === 0;
    }

    public function put(Key $key, $value){
        return $this->insert($key, $value);
    }

    public function add(Key $key, $value){
        return $this->insert($key, $value);
    }

    public function containsKey(Key $key){
        return $this->hasKey($key);
    }

    public function keys():array{
        $keys = [];
        foreach($this->keys as $key):
            if($key === self::TOMBSTONE) continue;
            $keys[] = $key;
        endforeach;
        return $keys;
    }

    public function values():array{
        return $this->values;
    }

    // Place a key-value pair into the hash-table. If the value already
    // exists inside the hash-table then the value is updated.
    public function insert(Key $key, $value){
        try{
            if($value === null)
                throw new Exception('Null value isn\'t allowed');
        }catch(Exception $e){
            echo "{$e->getMessage()} at line: {$e->getLine()} in {$e->getFile()}";
            die;
        }
        if($this->usedBuckets >= $this->threshold) $this->resizeTable();

        $this->setupProbing($key);

        //$offset: index without probing
        $offset = $this->normalizeIndex($key->getHash());
        for($i=$offset,$j=-1,$x=0; ; $x++, $i=$this->normalizeIndex($offset + $this->probe($x))):
            $loopKey = $this->keys[$i] ?? null;
            // The current slot was previously deleted
            if($loopKey === self::TOMBSTONE):
                $j = $j === -1 ? $i: $j;

            // The current cell already contains a key
            elseif($loopKey !== null):
                // The key we're trying to insert already exists in the hash-table,
                // so update its value with the most recent value
                if($key->equals($loopKey)):
                    $oldValue = $this->values[$i];
                    if($j===-1):
                        $this->values[$i] = $value;
                    else:
                        $this->keys[$i] = self::TOMBSTONE;
                        $this->values[$i] = null;
                        $this->keys[$j] = $key;
                        $this->values[$j] = $value;
                    endif;
                    $this->modificationCount++;
                    return $oldValue;
                endif;
            // Current cell is null so an insertion/update can occur
            else:
                // No previously encountered deleted buckets
                if($j === -1):
                    $this->usedBuckets++;
                    $this->keys[$i] = $key;
                    $this->values[$i] = $value;
                else:
                    $this->keys[$j] = $key;
                    $this->values[$j] = $value;
                endif;
                $this->keysCount++;
                $this->modificationCount++;
                break;
            endif;
        endfor;
        return null;
    }

    public function resizeTable():void{
        $oldCapacity = $this->capacity;
        $oldKeys = $this->keys;
        $oldValues = $this->values;

        $this->increaseCapacity();
        $this->adjustCapacity();
        $this->threshold = $this->capacity * $this->loadFactor;

        $this->clear();

        for($i=0;$i<$oldCapacity;$i++):
            $oldKey = $oldKeys[$i] ?? null;
            if($oldKey !== null && $oldKey !== self::TOMBSTONE)
                $this->insert($oldKey, $oldValues[$i]);
        endfor;

        unset($oldKeys, $oldValues);
    }

    final protected function normalizeIndex(int $hash):int{
        return abs($hash) % $this->capacity;
    }

    final static protected function gcd(int $a, int $b):int{
        if($b === 0)
            return $a;
        return self::gcd($b, $a % $b);
    }

    // Returns true/false on whether a given key exists within the hash-table
    public function hasKey(Key $key):bool{
        $this->setupProbing($key);

        //$offset: index without probing
        $offset = $this->normalizeIndex($key->getHash());
        // Start at the original hash value and probe until we find a spot where our key
        // is or hit a null element in which case our element does not exist.
        for($i = $offset, $j = -1, $x = 0;;$x++, $i = $this->normalizeIndex($offset + $this->probe($x))):

            $loopKey = $this->keys[$i] ?? null;
            // Ignore deleted cells, but record where the first index
            // of a deleted cell is found to perform lazy relocation later.
            if($loopKey === self::TOMBSTONE):
                $j = $j === -1 ? $i: $j;
            elseif($loopKey !== null):
                // The key we want is in the hash-table!
                if($key->equals($loopKey)):
                    // If j != -1 this means we previously encountered a deleted cell.
                    // We can perform an optimization by swapping the entries in cells
                    // i and j so that the next time we search for this key it will be
                    // found faster. This is called lazy deletion/relocation.
                    if($j !== -1):
                        $this->move($i, $j);
                    endif;
                    return true;
                endif;
            else:
                break;
            endif;
        endfor;
        return false;
    }

    public function get(Key $key){
        $this->setupProbing($key);
        //$offset: index without probing
        $offset = $this->normalizeIndex($key->getHash());
        // Start at the original hash value and probe until we find a spot where our key
        // is or hit a null element in which case our element does not exist.
        for($i = $offset, $j = -1, $x = 0;;$x++, $i = $this->normalizeIndex($offset + $this->probe($x))):

            $loopKey = $this->keys[$i] ?? null;
            // Ignore deleted cells, but record where the first index
            // of a deleted cell is found to perform lazy relocation later.
            if($loopKey === self::TOMBSTONE):
                $j = $j === -1 ? $i: $j;
            elseif($loopKey !== null):
                // The key we want is in the hash-table!
                if($key->equals($loopKey)):
                    // If j !== -1 this means we previously encountered a deleted cell.
                    // We can perform an optimization by swapping the entries in cells
                    // i and j so that the next time we search for this key it will be
                    // found faster. This is called lazy deletion/relocation.
                    if($j !== -1):
                        $this->move($i, $j);
                        return $this->values[$j];
                    endif;
                    return $this->values[$i];
                endif;
            else:
                break;
            endif;
        endfor;
        return null;
    }

    protected function removeAtIndex(int $index):void{
        $this->keys[$index] = self::TOMBSTONE;
        $this->values[$index] = null;
    }

    protected function move(int $indexData, int $indexTo){
        $this->keys[$indexTo] = $this->keys[$indexData];
        $this->values[$indexTo] = $this->values[$indexData];
        $this->removeAtIndex($indexData);
    }

    protected function remove(Key $key){
        $this->setupProbing($key);
        //$offset: index without probing
        $offset = $this->normalizeIndex($key->getHash());
        // Start at the original hash value and probe until we find a spot where our key
        // is or hit a null element in which case our element does not exist.
        for($i = $offset, $x = 0;;$x++, $i = $this->normalizeIndex($offset + $this->probe($x))):

            $loopKey = $this->keys[$i] ?? null;
            // Ignore deleted cells, but record where the first index
            // of a deleted cell is found to perform lazy relocation later.
            if($loopKey === self::TOMBSTONE)
                continue;
            if($loopKey === null)
                return null;

            // The key we want is in the hash-table!
            if($key->equals($loopKey)):
                $this->keysCount--;
                $this->modificationCount++;
                $oldValue = $this->values[$i];
                $this->removeAtIndex($i);
                return $oldValue;
            endif;
        endfor;
        return null;
    }

    public function __toString():string{
        $str = "{";
        for($i=0;$i<$this->capacity;$i++):
            $key = $this->keys[$i] ?? null;
            if($key !== self::TOMBSTONE && $key !== null)
                $str .= "<br/>{$this->keys[$i]->getKey()} => {$this->values[$i]} at index: {$i},";
        endfor;
        $str = substr($str, 0, strlen($str)-1);
        return "$str<br/>}<br/>Capacity: {$this->capacity}, Count: {$this->keysCount}";
    }
}