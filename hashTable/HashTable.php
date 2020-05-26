<?php

namespace hashTable;

defined('__ROOT__') || define('__ROOT__', dirname(dirname(__FILE__)));

require_once 'Entry.php';
require_once __ROOT__.'/LinkedList.php';

use \LinkedList;

class HashTable{
    protected array $table = [];
    protected int $capacity, $size = 0, $threshold = 0;
    protected float $maxLoadFactor;

    public function __construct(int $capacity = 10, $maxLoadFactor = .75){
        $this->capacity = $capacity;
        $this->maxLoadFactor = $maxLoadFactor;
        $this->threshold = $this->capacity * $this->maxLoadFactor;
    }

    public function size():int{
        return $this->size;
    }

    public function isEmpty():bool{
        return $this->size === 0;
    }

    public function clear():void{
        $this->table = [];
        $this->size = 0;
    }


    public function normalizeIndex(int $hash) : int{
        return abs($hash) % $this->capacity;
    }

    public function index(int $key):int{
        $keyOb = new Key($key);
        return $this->normalizeIndex($keyOb->getHash());
    }

    public function hasKey(int $key):int{
        $bucketIndex = $this->index($key);
        return $this->seekEntry($bucketIndex, $key) !== null;
    }

    public function add(int $key, $value){
        $keyObj = new Key($key);
        $newEntry = new Entry($keyObj, $value, null);
        $bucketIndex = $this->normalizeIndex($keyObj->getHash());
        return $this->insertEntryInBucket($bucketIndex, $newEntry);
    }

    public function get(int $key){
        $bucketIndex = $this->index($key);
        $entry = $this->seekEntry($bucketIndex, $key);
        if($entry !== null) return $entry->getVal();
        return null;
    }

    public function remove(int $key){
        $bucketIndex = $this->index($key);
        return $this->bucketRemoveEntry($bucketIndex, $key);
    }

    protected function bucketRemoveEntry(int $bucketIndex, int $key){
        $entry = $this->seekEntry($bucketIndex, $key);
        if($entry !== null):
            $linkedList = $this->table[$bucketIndex];
            $linkedList->remove($entry);
            $this->size--;
            $value = $entry->getVal();
            unset($entry);
            return $value;
        endif;
        return null;
    }
    public function insertEntryInBucket(int $bucketIndex, Entry $entry){
        $linkedList = $this->table[$bucketIndex] ?? new LinkedList();
        $existentEntry = $this->seekEntry($bucketIndex, $entry->getKey());
        if($existentEntry === null):
            $linkedList->add($entry);
            $this->table[$bucketIndex] = $linkedList;
            $this->size++;
            if($this->size > $this->threshold) $this->resizeTable();
            return null;
        else:
            $oldVal = $existentEntry->getVal();
            $existentEntry->setVal($entry->getVal());
            return $oldVal;
        endif;
    }
    public function seekEntry(int $bucketIndex, int $key):?Entry{
        $linkedList = $this->table[$bucketIndex] ?? null;
        if($linkedList === null) return null;
        $entry = $linkedList->getHead();
        while($entry !== null):
            if($entry->getKey() === $key)
                return $entry;
            $entry = $entry->getNext();
        endwhile;
        return null;
    }

    protected function resizeTable(){
        echo 'resize<br />';
        $this->capacity *= 2;
        $this->threshold = $this->capacity * $this->maxLoadFactor;
        $newTable = [];
        foreach($this->table as $oldBucket):
            $entry = $oldBucket->getHead();
            while($entry !== null):
                $bucketIndex = $this->normalizeIndex($entry->key()->getHash());
                $linkedList = $newTable[$bucketIndex] ?? new LinkedList();
                $linkedList->add($entry);
                $newTable[$bucketIndex]=$linkedList;
                $entry = $entry->getNext();
            endwhile;
        endforeach;
        $this->table = $newTable;
    }
    public function keys():array{
        $keys = [];
        foreach($this->table as $bucket):
            $node = $bucket->getHead();
            while($node !== null):
                $keys[] = $node->getKey();
                $node = $node->getNext();
            endwhile;
        endforeach;
        return $keys;
    }
    public function values():array{
        $values = [];
        foreach($this->table as $bucket):
            $node = $bucket->getHead();
            while($node !== null):
                $values[] = $node->getVal();
                $node = $node->getNext();
            endwhile;
        endforeach;
        return $values;
    }
    public function table():array{
        return $this->table;
    }
}