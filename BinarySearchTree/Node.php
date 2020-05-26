<?php
namespace binarySearchTree;

use \Exception;

class Node{
    public $data;
    public ?Node $leftChild, $rightChild;
    public function __construct($data, ?Node $leftChild, ?Node $rightChild){
        $this->data = $data;
        $this->leftChild = $leftChild;
        $this->rightChild = $rightChild;
    }
    public function dataType():string{
        return gettype($this->data);
    }
    public function compareTo($data):float{
        $dataType = $this->dataType();
        try{
            if($dataType !== gettype($data))
                throw new Exception('Different data types cann\'t be compared');
        }catch(Exception $e){
            echo $e->getMessage();
            die;
        }
        try{
            if($dataType === 'integer' || $dataType === 'double'):
                return $this->data - $data;
            elseif($dataType === 'string'):
                return strcmp($this->data, $data);
            else:
                throw new Exception('Data type other than (Integer, Float(Double), String)');
            endif;
        }catch(Exception $e){
            echo $e->getMessage();
            die;
        }
    }
}