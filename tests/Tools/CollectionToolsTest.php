<?php

use PHPUnit\Framework\TestCase;
use SimpleConfig\Tools\CollectionTools;

final class CollectionToolsTest extends TestCase
{
    /** Test base configuration's non existence and array change */
    public function testBaseConfigurationsNonExistenceAndArrayChange()
    {
        $data = null;
        $patch = [];
        $merge = [];
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }

    /** Test base configuration's non existence and object change */
    public function testBaseConfigurationsNonExistenceAndObjectChange()
    {
        $data = null;
        $patch = (object) [];
        $merge = (object) [];
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }

    /** Test change's non existence */
    public function testChangesNonExistence()
    {
        $data = (object) [ 'a' => 1 ];
        $patch = null;
        $merge = null;
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }

    /** Test simple merge */
    public function testSimpleMerge()
    {
        $data = (object) [ 'a' => 1 ];
        $patch = (object) [ 'b' => 2 ];
        $merge = (object) [ 'a' => 1, 'b' => 2 ];
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }

    /** Test two levels merge */
    public function testTwoLevelsMerge()
    {
        $data = (object) [ 'a' => (object) [ 'b' => 1, 'c' => 2 ] ];
        $patch = (object) [ 'a' => (object) [ 'b' => 3, 'd' => 4 ], 'e' => 5 ];
        $merge = (object) [ 'a' => (object) [ 'b' => 3, 'c' => 2, 'd' => 4 ], 'e' => 5 ];
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }

    /** Test simple array merge */
    public function testSimpleArrayMerge()
    {
        $data = (object) [ 'a' => (object) [ 'b' => [ 1, 2, 3 ] ] ];
        $patch = (object) [ 'a' => (object) [ 'b' => (object) [ 1 => 4, 3 => 5 ] ] ];
        $merge = (object) [ 'a' => (object) [ 'b' => [ 1, 4, 3, 5 ] ] ];
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }

    /** Test multi-dimensional array merge */
    public function testMultiDimensionalArrayMerge()
    {
        $data =  (object) [ 'a' => (object) [ 'b' => [ [ 11 ], [ 21, 22 ], [ 31, 32 ] ] ] ];
        $patch = (object) [ 'a' => (object) [ 'b' => (object) [
            1 => (object) [ 0 => 41 ],
            2 => [ 51 ],
            3 => (object) [ 0 => 61 ],
        ] ] ];
        $merge = (object) [ 'a' => (object) [ 'b' => [ [ 11 ], [ 41, 22 ], [ 51, 32 ], (object) [ 0 => 61 ] ] ] ];
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }

    /** Test array of objects merge */
    public function testArrayOfObjectsMerge()
    {
        $data = (object) [ 'a' => (object) [ 'b' => [
            (object) [ 'c' => 1, 'd' => 2 ],
            (object) [ 'c' => 3, 'd' => 4 ],
        ] ] ];
        $patch = (object) [ 'a' => (object) [ 'b' => (object) [ 1 => (object) [ 'c' => 5 ] ] ] ];
        $merge = (object) [ 'a' => (object) [ 'b' => [
            (object) [ 'c' => 1, 'd' => 2 ],
            (object) [ 'c' => 5, 'd' => 4 ],
        ] ] ];
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }

    /** Test merge with elements to remove */
    public function testMergeWithElementsToRemove()
    {
        $data = (object) [
            'a' => (object) [ 'b' => 1, 'd' => 4, 'e' => 5 ],
            'f' => [ (object) [ 'g' => 1 ], (object) [ 'h' => 2 ], (object) [ 'i' => 3 ] ],
        ];
        $patch = (object) [ 'a' => (object) [ 'b' => 2, 'c' => 3, 'e' => null ], 'e' => null, 'f' => null ];
        $merge = (object) [ 'a' => (object) [ 'b' => 2, 'c' => 3, 'd' => 4 ] ];
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }

    /** Test merge with one-element array which has to become empty */
    public function testMergeWithOneElementArrayWhichHasToBecomeEmpty()
    {
        $data = (object) [ 'a' => [ 1 ] ];
        $patch = (object) [ 'a' => (object) [ 0 => null ] ];
        $merge = (object) [ 'a' => [] ];
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }

    /** Test merge with many-element array which has to become empty */
    public function testMergeWithManyElementArrayWhichHasToBecomeEmpty()
    {
        $data = (object) [ 'a' => [ 1, 3, 5 ] ];
        $patch = (object) [ 'a' => (object) [ 0 => null, 2 => null, 1 => null ] ];
        $merge = (object) [ 'a' => [] ];
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }

    /** Test adding to array element with index far greater than array length */
    public function testAddingToArrayElementWithIndexFarGreaterThanArrayLength()
    {
        $data = (object) [ 'a' => [ 1, 3, 5 ] ];
        $patch = (object) [ 'a' => (object) [ 6 => 13 ] ];
        $merge = (object) [ 'a' => [ 1, 3, 5, null, null, null, 13 ] ];
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }

    /** Test trimming array right */
    public function testTrimmingArrayRight()
    {
        $dataA = (object) [ 'a' => [ 1, 3, 5, null, null, null, 13 ] ];
        $dataB = (object) [ 'a' => [ 1, 3, 5, null, null, null, 13, null, null ] ];
        $patch = (object) [ 'a' => (object) [ 6 => null ] ];
        $merge = (object) [ 'a' => [ 1, 3, 5 ] ];
        $this->assertEquals($merge, CollectionTools::deepMerge($dataA, $patch));
        $this->assertEquals($merge, CollectionTools::deepMerge($dataB, $patch));
    }

    /** Test changing and removing elements from array */
    public function testChangingAndRemovingElementsFromArray()
    {
        $data = (object) [ 'a' => [ (object) [ 'b' => 1 ], (object) [ 'c' => 2 ], (object) [ 'd' => 3 ] ] ];
        $patch = (object) [ 'a' => (object) [ 0 => [ 'b' => 4 ], 2 => null ] ];
        $merge = (object) [ 'a' => [ (object) [ 'b' => 4 ], (object) [ 'c' => 2 ] ] ];
        $this->assertEquals($merge, CollectionTools::deepMerge($data, $patch));
    }
}
