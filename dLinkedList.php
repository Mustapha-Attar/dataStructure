<?php
require_once 'dNode.php';
class dLinkedList{
    protected ?dNode $head;
    public function __construct(){
        $this->head = null;
    }
    public function add(?string $value){
        $node = new dNode($value, $this->head);
        if($this->head !== null):
            $this->head->setPrevious($node);
        endif;
        $this->head = $node;
    }
    public function pop(){
        $this->head = $this->head->getNext();
    }
    public function getHead(){
        return $this->head;
    }
}