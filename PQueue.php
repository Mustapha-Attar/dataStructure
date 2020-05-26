<?php

class PQueue{
    protected int $size;
    protected array $data = [];


    public function __construct(array $data){
        $this->size = sizeof($data);
        $this->data = $data;
        for(max(0, $i=$this->size/2-1);$i>=0;$i--):
            $this->sink($i);
        endfor;
    }
    public function isEmpty() : bool{
        return $this->size === 0;
    }
    public function clear():void{
        $this->data = [];
        $this->size = 0;
    }
    public function size():int{
        return $this->size;
    }
    public function peek(){
        if($this->isEmpty()) return null;
        return $this->get(0);
    }
    // Removes the root of the heap, O(log(n))
    public function poll() {
        return $this->removeAt(0);
    }
    // Test if an element is in heap, O(n)
    public function contains($elem):bool{
        for($i=0;$i<$this->size;$i++):
            if($this->get($i) === $elem) return true;
        endfor;
        return false;
    }
    public function add($elem):bool{
        if($elem !== null):
            $this->data[] = $elem;
            $this->swim($this->size);
            $this->size++;
            return true;
        endif;
        return false;
    }
    private function get(int $i){
        return $this->size > $i ? $this->data[$i]: null;
    }
    private function set(int $i, $data){
        return $this->data[$i] = $data;
    }
    // Swap two nodes. Assumes i & j are valid, O(1)
    private function swap(int $i, int $j){
        $elemI = $this->get($i);
        $elemJ = $this->get($j);

        $this->set($i, $elemJ);
        $this->set($j, $elemI);
    }
    private function less(int $i, int $j) {
        $node1 = $this->get($i);
        $node2 = $this->get($j);
        return $node1 <= $node2;
    }
    private function parent(int $i):int{
        return intval(($i - 1) / 2);
    }
    private function leftChild(int $i):int{
        return $i * 2 + 1;
    }
    private function rightChild(int $i):int{
        return $i * 2 + 2;
    }
    private function isIndexInRange(int $i):bool{
        return $this->size > $i;
    }
    private function sink(int $k){
        while(true):
            $left = $this->leftChild($k);
            $right = $this->rightChild($k);
            $smallest = $left; // Assume left is the smallest node of the two children

            // Find which is smaller left or right
            // If right is smaller set smallest to be right
            if ($right < $this->size && $this->less($right, $left)) $smallest = $right;

            // Stop if we're outside the bounds of the tree
            // or stop early if we cannot sink k anymore
            if ($left >= $this->size || $this->less($k, $smallest)) break;

            // Move down the tree following the smallest node
            $this->swap($smallest, $k);
            $k = $smallest;
        endwhile;
    }
    // Perform bottom up node swim, O(log(n))
    private function swim(int $i){
        $parent = $this->parent($i);

        // Keep swimming while we have not reached the
        // root and while we're less than our parent.
        while($i > 0 && $this->less($i, $parent)):
            // Exchange k with the parent
            $this->swap($parent, $i);
            $i = $parent;

            // Grab the index of the next parent node WRT to k
            $parent = $this->parent($i);
        endwhile;
    }
    public function remove ($elem):bool{
        if($elem === null) return false;
        for($i=0;$i<$this->size;$i++):
            if($this->get($i) === $elem):
                $this->removeAt($i);
                return true;
            endif;
        endfor;
        return false;
    }
    private function removeAt(int $i){
        if($this->isEmpty()) return null;
        $this->size--;
        $removed_data = $this->get($i);
        $this->swap($i, $this->size);
        unset($this->data[$this->size]);

        // Check if the last element was removed
        //then there is no need to swim
        if ($i !== $this->size && $i !== 0):
            //check if there is need for sink or swim
            $leftChild = $this->leftChild($i);
            $rightChild = $this->rightChild($i);
            $parent = $this->parent($i);
            if($this->isIndexInRange($leftChild) && $this->less($leftChild, $i)
                ||$this->isIndexInRange($rightChild) && $this->less($rightChild, $i)):
                $this->sink($i);
            elseif($this->less($i, $parent)):
                $this->swim($i);
            endif;
        endif;

        return $removed_data;
    }

    // Recursively checks if this heap is a min heap
    // This method is just for testing purposes to make
    // sure the heap invariant is still being maintained
    // Called this method with k=0 to start at the root
    public function isMinHeap(int $i):bool{
        if($i >= $this->size) return true;

        $leftChild = $this->leftChild($i);
        $rightChild = $this->rightChild($i);

        // Make sure that the current node k is less than
        // both of its children left, and right if they exist
        // return false otherwise to indicate an invalid heap
        if($this->isIndexInRange($leftChild) && $this->less($i, $leftChild)) return false;
        if($this->isIndexInRange($rightChild) && $this->less($i, $rightChild)) return false;

        return $this->isMinHeap($leftChild) && $this->isMinHeap($rightChild);
    }
}