<?php
class UnionFind{

    // The number of elements in this union find
    private int $size;

    // Used to track the size of each of the component
    private array $componentSizeOfRoot = [];

    // id[i] points to the parent of i, if id[i] = i then i is a root node
    private array $id = [];

    // Tracks the number of components in the union find
    private int $componentsNum;

    public function __construct(int $size){
        try{
            if($size <= 0) throw new Exception('size can\'t be equal or smaller than zero</br >');
        }catch(Exception $e){
            echo $e->getMessage();
            die;
        }
        $this->size = $this->componentsNum = $size;
        for($i=0;$i<$size;$i++):
            $this->id[$i] = $i;
            $this->componentSize[$i] = 1;
        endfor;
    }

    // Find which component/set 'p' belongs to, takes amortized constant time.
    public function find(int $p):int{

        //Find the root of the component/set
        $root = $p;
        while($root !== $this->id[$root])
            $root = $this->id[$root];

        // Compress the path leading back to the root.
        // Doing this operation is called "path compression"
        // and is what gives us amortized time complexity.
        while ($p !== $root):
            $next = $this->id[$p];
            $this->id[$p] = $root;
            $p = $next;
        endwhile;

        return $root;
    }

    //Return whether or not the elements 'p' and
    // 'q' are in the same components/set
    public function connected(int $p, int $q):bool{
        return $this->find($p) === $this->find($q);
    }

    //Return the size of the components/set 'p' belongs to
    public function componentSize(int $p):int{
        return $this->componentSizeOfRoot[$this->find($p)];
    }

    //Return the number of elements in this UnionFind/Disjoint set
    public function size():int{
        return $this->size;
    }

    //Returns the number of remaining components/sets
    public function components():int{
        return $this->componentsNum;
    }

    //Unify the components/sets containing elements 'p' and 'q'
    public function unify(int $p, int $q):void{
        $root1 = $this->find($p);
        $root2 = $this->find($q);

        //These elements are already in the same group!
        if($root1 === $root2) return;

        // Merge two components/sets together.
        // Merge smaller Component/set into the larger one
        if($this->componentSizeOfRoot[$root1] < $this->componentSizeOfRoot[$root2]):
            $this->componentSizeOfRoot[$root2] += $this->componentSizeOfRoot[$root1];
            $this->id[$root1] = $root2;
        else:
            $this->componentSizeOfRoot[$root1] += $this->componentSizeOfRoot[$root2];
            $this->id[$root2] = $root1;
        endif;

        // Since the roots found are different we know that the
        // number of the components.sets has decreased by one
        $this->componentsNum--;
    }
}