<?php
class dNode{
    protected  ?string $value;
    protected ?dNode $next;
    protected ?dNode $previous;
    public function __construct(?string $val, ?dNode $next){
        $this->value = $val;
        $this->next = $next;
    }
    public function setPrevious(?dNode $previous){
        $this->previous = $previous;
    }
    public function getVal(){
        return $this->value;
    }
    public function getNext(){
        return $this->next;
    }
}