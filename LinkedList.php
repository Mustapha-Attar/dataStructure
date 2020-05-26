<?php

require_once 'Node.php';

class LinkedList{
    protected ?Node $head;
    public function __construct(){
        $this->head = null;
    }
    public function add(Node $node){
        //it's crucial to clone the original node and use the new var
        //because it's gonna remove its next element, but by cloning it
        //it'll keep the next element of the original node
        $newNode = clone $node;
        $newNode->setNext($this->head);
        $this->head = $newNode;
    }
    public function pop(){
        $val = $this->head->getVal();
        $this->head = $this->head->getNext();
        return $val;
    }
    public function getHead(): ?Node{
        return $this->head;
    }
    //takes O(n)
    public function remove(Node $node){
        //check if the node that is wanted to be removed
        //is the head itself, in that case, will set the head
        //to point to the next element
        if($this->head->equals($node)):
            $oldVal = $this->head->getVal();
            $this->head = $this->head->getNext();
            return $oldVal;
        endif;
        $e = $this->head->getNext();
        $pre = $this->head;
        while($e !== null):
            if($e->equals($node)):
                $eVal = $e->getVal();
                $pre->setNext($e->getNext());
                return $eVal;
            endif;
            $pre = $e;
            $e = $e->getNext();
        endwhile;
        return null;
    }

    public function clear(){
        $this->head = null;
    }
}