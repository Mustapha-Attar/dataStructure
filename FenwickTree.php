<?php


class FenwickTree{
    protected int $N;
    private array $tree;

    public function __construct(){
        $arg = func_get_arg(0);
        $argType = gettype($arg);
        try{
            switch ($argType):
                case 'integer':
                    $this->N = $arg + 1;
                    break;
                case 'array':
                    $this->init($arg);
                    break;
                default:
                    throw new Exception('Neither an Integer nur an Array is given!');
                    break;
            endswitch;
        }catch(Exception $e){
            echo "{$e->getMessage()} at line: {$e->getLine()} in {$e->getFile()}";
            die;
        }
    }

    //Construct a Fenwick tree with an initial set of values.
    // The 'values' array MUST BE ONE BASED meaning values[0]
    // does not get used, O(n) construction.
    protected function init(array $arr):void{
        $this->N = sizeof($arr);
        $arr[0] = 0;
        $this->tree = $arr;
        for($i=1;$i<$this->N;$i++):
            $parent = $i + self::lsb($i);
            if($parent < $this->N) $this->tree[$parent] += $this->tree[$i];
        endfor;
    }

    // Returns the value of the least significant bit (LSB)
    // lsb(108) = lsb(0b1101100) =     0b100 = 4
    // lsb(104) = lsb(0b1101000) =    0b1000 = 8
    // lsb(96)  = lsb(0b1100000) =  0b100000 = 32
    // lsb(64)  = lsb(0b1000000) = 0b1000000 = 64
    protected static function lsb(int $n){
        return $n & - $n;
    }

    public function prefixSum(int $i):int{
        $sum = 0;
        while($i !== 0):
            $sum+=$this->tree[$i];
            $i &= ~self::lsb($i); // Equivalently, i -= lsb(i);
        endwhile;
        return $sum;
    }

    public function sum(int $left, int $right):int{
        try{
            if($right < $left)
                throw new Exception("Make sure right >= left");
        }catch(Exception $e){
            echo "{$e->getMessage()} at line: {$e->getLine()} in {$e->getFile()}";
            die;
        }
        return $this->prefixSum($right) - $this->prefixSum($left - 1);
    }

    public function get(int $i):int{
        return $this->sum($i, $i);
    }

    public function add(int $i, int $v):void{
        while($i < $this->N):
            $this->tree[$i] += $v;
            $i += self::lsb($i);
        endwhile;
    }

    public function set(int $i, int $v):void{
        $this->add($i, $v - $this->sum($i, $i));
    }

    public function __toString(){
        return (string) $this->tree;
    }
}