<?php
class Node{
    protected $value;
    protected ?node $next;
    public function __construct($val, ?node $next){
        $this->value = $val;
        $this->next = $next;
    }
    public function getVal(){
        return $this->value;
    }

    public function getNext():?Node{
        return $this->next;
    }

    public function setNext(?Node $node): void{
        $this->next = $node;
    }
    public function setVal($value){
        $this->value = $value;
    }
    public function equals():bool{
        $node = func_get_arg(0);
        try{
            if(is_object($node) && get_class($node) === 'Node')
                throw new Exception('The that you wanna compare must be a Node');
        }catch(Exception $e){
            echo $e->getMessage();
            die();
        }
        return $this->getVal() === $node->getVal();
    }
}