<?php

namespace binarySearchTree;

require_once 'Node.php';

class BinarySearchTree{

    // Tracks the number of nodes in this BST
    private int $nodeCount = 0;

    // This BST is a rooted tree so we maintain a handle on the root node
    private ?Node $root = null;

    public function root():?Node{
        return $this->root;
    }


    // Check if this binary tree is empty
    public function isEmpty():bool{
        return $this->size() === 0;
    }

    // Get the number of nodes in this binary tree
    public function size():int{
        return $this->nodeCount;
    }

    // Add an element to this binary tree. Returns true
    // if we successfully perform an insertion
    public function add($element):bool{

        // Check if the value already exists in this
        // binary tree, if it does ignore adding it
        if($this->contains($element)):
            return false;

        // Otherwise add this element to the binary tree
        else:
            $this->root = $this->addElement($this->root, $element);
            $this->nodeCount++;
            return true;
        endif;
    }

    // Private method to recursively add a value in the binary tree
    private function addElement(?Node $node, $newElement):Node{
        // Base case: found a leaf node
        if($node === null):
            $node = new Node($newElement, null, null);
        else:
            // Pick a subtree to insert element
            if($node->compareTo($newElement) > 0):
                $node->leftChild = $this->addElement($node->leftChild, $newElement);
            else:
                $node->rightChild = $this->addElement($node->rightChild, $newElement);
            endif;
        endif;

        return $node;
    }

    // Remove a value from this binary tree if it exists, O(n)
    public function remove($element):int{
        // Make sure the node we want to remove
        // actually exists before we remove it
        if($this->contains($element)):
            $this->root = $this->removeElement($this->root, $element);
            $this->nodeCount--;
            return true;
        endif;
        return false;
    }

    // Private method to recursively remove a value in the binary tree
    private function removeElement(?Node $node, $element):?Node{
        if($node === null) return null;
        $compare = $node->compareTo($element);
        // Dig into left subtree, the value we're looking
        // for is smaller than the current value
        if($compare > 0):
            $node->leftChild = $this->removeElement($node->leftChild, $element);
        // Dig into right subtree, the value we're looking
        // for is greater than the current value
        elseif($compare < 0):
            $node->rightChild = $this->removeElement($node->rightChild, $element);
        // Found the node we wish to remove
        else:
            // This is the case with only a right subtree or
            // no subtree at all. In this situation just
            // swap the node we wish to remove with its right child.
            if($node->leftChild === null):
                $rightNode = $node->rightChild;
                $node = null;
                return $rightNode;
            // This is the case with only a left subtree or
            // no subtree at all. In this situation just
            // swap the node we wish to remove with its left child.
            elseif($node->rightChild === null):
                $leftNode = $node->leftChild;
                $node = null;
                return $leftNode;
            // When removing a node from a binary tree with two links the
            // successor of the node being removed can either be the largest
            // value in the left subtree or the smallest value in the right
            // subtree. In this implementation I have decided to find the
            // smallest value in the right subtree which can be found by
            // traversing as far left as possible in the right subtree.
            else:
                // Find the leftmost node in the right subtree
                $tmpNode = $this->findMin($node->rightChild);
                // Swap the data
                $node->data = $tmpNode->data;
                // Go into the right subtree and remove the leftmost node we
                // found and swapped data with. This prevents us from having
                // two nodes in our tree with the same value.
                $node->rightChild = $this->removeElement($node->rightChild, $tmpNode->data);
                // If instead we wanted to find the largest node in the left
                // subtree as opposed to smallest node in the right subtree
                // here is what we would do:
                // $tmpNode = $this->findMax($node->leftChild);
                // $node->data = $tmpNode->data;
                // $node->leftChild = $this->removeElement($node->leftChild, $tmpNode->data);
            endif;
        endif;
        return $node;
    }

    // Helper method to find the leftmost node (which has the smallest value)
    public function findMin(Node $node):Node{
        while($node->leftChild !== null)
            $node = $node->leftChild;
        return $node;
    }

    // Helper method to find the rightmost node (which has the largest value)
    public function findMax(Node $node):Node{
        while($node->rightChild !== null)
            $node = $node->rightChild;
        return $node;
    }

    // returns true is the element exists in the tree
    public function contains($element):bool{
        return $this->containsElement($this->root, $element);
    }

    // private recursive method to find an element in the tree
    private function containsElement(?Node $node, $element):bool{
        // Base case: reached bottom, value not found
        if($node === null) return false;

        $com = $node->compareTo($element);
        // Dig into the left subtree because the value we're
        // looking for is smaller than the current value
        echo "node->data: $node->data <br />";
        echo "element: $element <br />";
        if($com > 0):
            echo '$node->data > $element <br />';
            return $this->containsElement($node->leftChild, $element);
        elseif($com < 0):
            echo '$node->data < $element <br />';
            return $this->containsElement($node->rightChild, $element);
        else:
            echo 'found <br />';
            return true;
        endif;
    }

    // Computes the height of the tree, O(n)
    public function height():int{
        return $this->innerHeight($this->root);
    }

    // Recursive helper method to compute the height of the tree
    private function innerHeight(?Node $node):int{
        if($node === null) return 0;
        return max($this->innerHeight($node->leftChild), $this->innerHeight($node->rightChild));
    }
}