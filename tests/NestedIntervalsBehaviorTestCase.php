<?php
/**
 * @link https://github.com/paulzi/yii2-nested-intervals
 * @copyright Copyright (c) 2015 PaulZi <pavel.zimakoff@gmail.com>
 * @license MIT (https://github.com/paulzi/yii2-nested-intervals/blob/master/LICENSE)
 */

namespace paulzi\nestedintervals\tests;

use paulzi\nestedintervals\tests\migrations\TestMigration;
use paulzi\nestedintervals\tests\models\MultipleTreeNode;
use paulzi\nestedintervals\tests\models\Node;
use Yii;

/**
 * @author PaulZi <pavel.zimakoff@gmail.com>
 */
class NestedIntervalsBehaviorTestCase extends BaseTestCase
{
    public function testGetParents()
    {
        $data = [1, 4, 9];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, Node::findOne(21)->parents));
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, MultipleTreeNode::findOne(21)->parents));

        $data = [];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, Node::findOne(1)->parents));
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, MultipleTreeNode::findOne(1)->parents));

        $data = [2, 7];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, Node::findOne(17)->getParents(2)->all()));
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, MultipleTreeNode::findOne(17)->getParents(2)->all()));

        $data = [26, 30];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, MultipleTreeNode::findOne(38)->parents));
    }

    public function testGetParent()
    {
        $this->assertEquals(5, Node::findOne(12)->parent->id);
        $this->assertEquals(5, MultipleTreeNode::findOne(12)->parent->id);

        $this->assertEquals(1, Node::findOne(4)->getParent()->one()->getAttribute('id'));
        $this->assertEquals(26, MultipleTreeNode::findOne(29)->getParent()->one()->getAttribute('id'));

        $this->assertEquals(null, Node::findOne(1)->parent);
        $this->assertEquals(null, MultipleTreeNode::findOne(1)->parent);
    }

    public function testGetRoot()
    {
        $this->assertEquals(1, Node::findOne(16)->root->id);
        $this->assertEquals(26, MultipleTreeNode::findOne(28)->root->id);

        $this->assertEquals(1, Node::findOne(1)->getRoot()->one()->getAttribute('id'));
        $this->assertEquals(26, MultipleTreeNode::findOne(26)->getRoot()->one()->getAttribute('id'));
    }

    public function testGetDescendants()
    {
        $data = [8, 9, 20, 21, 22, 10, 23, 24, 25];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, Node::findOne(4)->descendants));
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, MultipleTreeNode::findOne(4)->descendants));

        $data = [2, 5, 6, 7];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, Node::findOne(2)->getDescendants(1, true)->all()));
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, MultipleTreeNode::findOne(2)->getDescendants(1, true)->all()));

        $data = [10, 25, 24, 23, 9, 22, 21, 20, 8];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, Node::findOne(4)->getDescendants(3, false, true)->all()));
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, MultipleTreeNode::findOne(4)->getDescendants(3, false, true)->all()));

        $data = [];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, Node::findOne(8)->descendants));
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, MultipleTreeNode::findOne(8)->descendants));
    }

    public function testGetChildren()
    {
        $data = [8, 9, 10];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, Node::findOne(4)->children));
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, MultipleTreeNode::findOne(4)->children));

        $data = [];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, Node::findOne(3)->getChildren()->all()));
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, MultipleTreeNode::findOne(28)->getChildren()->all()));
    }

    public function testGetLeaves()
    {
        $data = [8, 20, 21, 22, 23, 24, 25];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, Node::findOne(4)->leaves));
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, MultipleTreeNode::findOne(4)->leaves));

        $data = [3, 8];
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, Node::findOne(1)->getLeaves(2)->all()));
        $this->assertEquals($data, array_map(function ($value) { return $value->id; }, MultipleTreeNode::findOne(1)->getLeaves(2)->all()));
    }

    public function testGetPrev()
    {
        $this->assertEquals(11, Node::findOne(12)->prev->id);
        $this->assertEquals(11, MultipleTreeNode::findOne(12)->prev->id);

        $this->assertEquals(null, Node::findOne(20)->getPrev()->one());
        $this->assertEquals(null, MultipleTreeNode::findOne(20)->getPrev()->one());
    }

    public function testGetNext()
    {
        $this->assertEquals(13, Node::findOne(12)->next->id);
        $this->assertEquals(13, MultipleTreeNode::findOne(12)->next->id);

        $this->assertEquals(null, Node::findOne(19)->getNext()->one());
        $this->assertEquals(null, MultipleTreeNode::findOne(19)->getNext()->one());
    }

    public function testIsRoot()
    {
        $this->assertTrue(Node::findOne(1)->isRoot());
        $this->assertTrue(MultipleTreeNode::findOne(1)->isRoot());
        $this->assertTrue(MultipleTreeNode::findOne(26)->isRoot());

        $this->assertFalse(Node::findOne(3)->isRoot());
        $this->assertFalse(MultipleTreeNode::findOne(3)->isRoot());
        $this->assertFalse(MultipleTreeNode::findOne(37)->isRoot());
    }

    public function testIsChildOf()
    {
        $this->assertTrue(Node::findOne(10)->isChildOf(Node::findOne(1)));
        $this->assertTrue(MultipleTreeNode::findOne(10)->isChildOf(MultipleTreeNode::findOne(1)));

        $this->assertTrue(Node::findOne(9)->isChildOf(Node::findOne(4)));
        $this->assertTrue(MultipleTreeNode::findOne(9)->isChildOf(MultipleTreeNode::findOne(4)));

        $this->assertFalse(Node::findOne(12)->isChildOf(Node::findOne(15)));
        $this->assertFalse(MultipleTreeNode::findOne(12)->isChildOf(MultipleTreeNode::findOne(15)));

        $this->assertFalse(Node::findOne(21)->isChildOf(Node::findOne(22)));
        $this->assertFalse(MultipleTreeNode::findOne(21)->isChildOf(MultipleTreeNode::findOne(22)));

        $this->assertFalse(Node::findOne(8)->isChildOf(Node::findOne(8)));
        $this->assertFalse(MultipleTreeNode::findOne(8)->isChildOf(MultipleTreeNode::findOne(8)));

        $this->assertFalse(MultipleTreeNode::findOne(6)->isChildOf(MultipleTreeNode::findOne(27)));
    }

    public function testIsLeaf()
    {
        $this->assertTrue(Node::findOne(3)->isLeaf());
        $this->assertTrue(MultipleTreeNode::findOne(3)->isLeaf());

        $this->assertFalse(Node::findOne(4)->isLeaf());
        $this->assertFalse(MultipleTreeNode::findOne(4)->isLeaf());
    }

    public function testMakeRootInsert()
    {
        (new TestMigration())->up();
        $dataSet = new ArrayDataSet(require(__DIR__ . '/data/empty.php'));
        $this->getDatabaseTester()->setDataSet($dataSet);
        $this->getDatabaseTester()->onSetUp();

        $node = new Node(['slug' => 'r']);
        $this->assertTrue($node->makeRoot()->save());

        $node = new MultipleTreeNode(['slug' => 'r1']);
        $this->assertTrue($node->makeRoot()->save());

        $node = new MultipleTreeNode([
            'slug' => 'r2',
            'tree' => 100,
        ]);
        $this->assertTrue($node->makeRoot()->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-make-root-insert.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testMakeRootUpdate()
    {
        $node = MultipleTreeNode::findOne(9);
        $this->assertTrue($node->makeRoot()->save());

        $node = MultipleTreeNode::findOne(27);
        $node->setAttribute('tree', 100);
        $this->assertTrue($node->makeRoot()->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-make-root-update.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testMakeRootNewExceptionIsRaisedWhenTreeAttributeIsFalseAndRootIsExists()
    {
        $node = new Node(['slug' => 'r']);
        $node->makeRoot()->save();
    }

    public function testPrependToInsertInNoEmpty()
    {
        $node = new Node(['slug' => 'new1']);
        $this->assertTrue($node->prependTo(Node::findOne(1))->save());

        $node = new Node(['slug' => 'new2']);
        $this->assertTrue($node->prependTo(Node::findOne(6))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(1))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(6))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-prepend-to-insert-in-no-empty.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testPrependToInsertInEmpty()
    {
        $node = new Node(['slug' => 'new1']);
        $this->assertTrue($node->prependTo(Node::findOne(14))->save());

        $node = new Node(['slug' => 'new2']);
        $this->assertTrue($node->prependTo(Node::findOne(15))->save());

        $node = new Node(['slug' => 'new3']);
        $this->assertTrue($node->prependTo(Node::findOne(8))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(16))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(18))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-prepend-to-insert-in-empty.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testPrependToInsertInEmptyAmount77NoPrepend()
    {
        $config = [
            'amountOptimize' => 77,
            'noPrepend'      => true,
        ];

        $node = new Node(['slug' => 'new1']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(Node::findOne(14))->save());

        $node = new Node(['slug' => 'new2']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(Node::findOne(15))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(16))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(18))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-prepend-to-insert-in-empty-amount-77-no-prepend.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testPrependToInsertInEmptyAmount4NoAppend()
    {
        $config = [
            'amountOptimize' => 4,
            'noAppend'       => true,
        ];

        $node = new Node(['slug' => 'new1']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(Node::findOne(14))->save());

        $node = new Node(['slug' => 'new2']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(Node::findOne(15))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(16))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(18))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-prepend-to-insert-in-empty-amount-4-no-append.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testPrependToInsertInEmptyAmount7NoInsert()
    {
        $config = [
            'amountOptimize' => 7,
            'noInsert'       => true,
        ];

        $node = new Node(['slug' => 'new1']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(Node::findOne(14))->save());

        $node = new Node(['slug' => 'new2']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(Node::findOne(15))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(16))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(18))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-prepend-to-insert-in-empty-amount-7-no-insert.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testPrependToInsertInEmptyAmount13Reserve3()
    {
        $config = [
            'amountOptimize' => 13,
            'reserveFactor'  => 3,
        ];

        $node = new Node(['slug' => 'new']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(Node::findOne(6))->save());

        $node = new MultipleTreeNode(['slug' => 'new']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(6))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-prepend-to-insert-in-empty-amount-13-reserve-3.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testPrependToUpdateSameNode()
    {
        $node = Node::findOne(4);
        $this->assertTrue($node->prependTo(Node::findOne(1))->save());

        $node = MultipleTreeNode::findOne(4);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(1))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-prepend-to-update-same-node.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testPrependToUpdateDeep()
    {
        $node = Node::findOne(10);
        $this->assertTrue($node->prependTo(Node::findOne(18))->save());

        $node = MultipleTreeNode::findOne(10);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(18))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-prepend-to-update-deep.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testPrependToUpdateOut()
    {
        $node = Node::findOne(6);
        $this->assertTrue($node->prependTo(Node::findOne(1))->save());

        $node = MultipleTreeNode::findOne(6);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(1))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-prepend-to-update-out.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testPrependToUpdateAnotherTree()
    {
        $node = MultipleTreeNode::findOne(30);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(4))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-prepend-to-update-another-tree.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testPrependToUpdateSelf()
    {
        $node = Node::findOne(2);
        $this->assertTrue($node->prependTo(Node::findOne(1))->save());

        $node = MultipleTreeNode::findOne(2);
        $this->assertTrue($node->prependTo(MultipleTreeNode::findOne(1))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/data.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testPrependToInsertExceptionIsRaisedWhenTargetIsNewRecord()
    {
        $node = new Node(['slug' => 'new']);
        $node->prependTo(new Node())->save();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testPrependToUpdateExceptionIsRaisedWhenTargetIsNewRecord()
    {
        $node = Node::findOne(2);
        $node->prependTo(new Node())->save();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testPrependToUpdateExceptionIsRaisedWhenTargetIsSame()
    {
        $node = Node::findOne(3);
        $node->prependTo(Node::findOne(3))->save();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testPrependToUpdateExceptionIsRaisedWhenTargetIsChild()
    {
        $node = Node::findOne(5);
        $node->prependTo(Node::findOne(11))->save();
    }

    public function testAppendToInsertInNoEmpty()
    {
        $node = new Node(['slug' => 'new1']);
        $this->assertTrue($node->appendTo(Node::findOne(2))->save());

        $node = new Node(['slug' => 'new2']);
        $this->assertTrue($node->appendTo(Node::findOne(6))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(2))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(6))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-append-to-insert-in-no-empty.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testAppendToInsertInEmpty()
    {
        $node = new Node(['slug' => 'new1']);
        $this->assertTrue($node->appendTo(Node::findOne(14))->save());

        $node = new Node(['slug' => 'new2']);
        $this->assertTrue($node->appendTo(Node::findOne(15))->save());

        $node = new Node(['slug' => 'new3']);
        $this->assertTrue($node->appendTo(Node::findOne(8))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(16))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(18))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-append-to-insert-in-empty.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testAppendToInsertInEmptyAmount77NoPrepend()
    {
        $config = [
            'amountOptimize' => 77,
            'noPrepend'      => true,
        ];

        $node = new Node(['slug' => 'new1']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(Node::findOne(14))->save());

        $node = new Node(['slug' => 'new2']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(Node::findOne(15))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(16))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(18))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-append-to-insert-in-empty-amount-77-no-prepend.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testAppendToInsertInEmptyAmount4NoAppend()
    {
        $config = [
            'amountOptimize' => 4,
            'noAppend'       => true,
        ];

        $node = new Node(['slug' => 'new1']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(Node::findOne(14))->save());

        $node = new Node(['slug' => 'new2']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(Node::findOne(15))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(16))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(18))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-append-to-insert-in-empty-amount-4-no-append.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testAppendToInsertInEmptyAmount7NoInsert()
    {
        $config = [
            'amountOptimize' => 7,
            'noInsert'       => true,
        ];

        $node = new Node(['slug' => 'new1']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(Node::findOne(14))->save());

        $node = new Node(['slug' => 'new2']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(Node::findOne(15))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(16))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(18))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-append-to-insert-in-empty-amount-7-no-insert.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testAppendToInsertInEmptyAmount13Reserve3()
    {
        $config = [
            'amountOptimize' => 13,
            'reserveFactor'  => 3,
        ];

        $node = new Node(['slug' => 'new']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(Node::findOne(6))->save());

        $node = new MultipleTreeNode(['slug' => 'new']);
        Yii::configure($node->getBehavior('tree'), $config);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(6))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-append-to-insert-in-empty-amount-13-reserve-3.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testAppendToUpdateSameNode()
    {
        $node = Node::findOne(2);
        $this->assertTrue($node->appendTo(Node::findOne(1))->save());

        $node = MultipleTreeNode::findOne(2);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(1))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-append-to-update-same-node.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testAppendToUpdateDeep()
    {
        $node = Node::findOne(10);
        $this->assertTrue($node->appendTo(Node::findOne(18))->save());

        $node = MultipleTreeNode::findOne(10);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(18))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-append-to-update-deep.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testAppendToUpdateOut()
    {
        $node = Node::findOne(6);
        $this->assertTrue($node->appendTo(Node::findOne(1))->save());

        $node = MultipleTreeNode::findOne(6);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(1))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-append-to-update-out.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testAppendToUpdateAnotherTree()
    {
        $node = MultipleTreeNode::findOne(30);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(4))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-append-to-update-another-tree.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testAppendToUpdateSelf()
    {
        $node = Node::findOne(4);
        $this->assertTrue($node->appendTo(Node::findOne(1))->save());

        $node = MultipleTreeNode::findOne(4);
        $this->assertTrue($node->appendTo(MultipleTreeNode::findOne(1))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/data.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testAppendToInsertExceptionIsRaisedWhenTargetIsNewRecord()
    {
        $node = new Node(['slug' => 'new']);
        $node->appendTo(new Node())->save();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testAppendToUpdateExceptionIsRaisedWhenTargetIsNewRecord()
    {
        $node = Node::findOne(2);
        $node->appendTo(new Node())->save();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testAppendToUpdateExceptionIsRaisedWhenTargetIsSame()
    {
        $node = Node::findOne(3);
        $node->appendTo(Node::findOne(3))->save();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testAppendToUpdateExceptionIsRaisedWhenTargetIsChild()
    {
        $node = Node::findOne(5);
        $node->appendTo(Node::findOne(11))->save();
    }

    public function testInsertBeforeMiddle()
    {
        $node = new Node(['slug' => 'new1']);
        $this->assertTrue($node->insertBefore(Node::findOne(16))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        $this->assertTrue($node->insertBefore(MultipleTreeNode::findOne(22))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        $this->assertTrue($node->insertBefore(MultipleTreeNode::findOne(33))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-insert-before-insert-middle.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testInsertBeforeInsertBegin()
    {
        $node = new Node(['slug' => 'new1']);
        $this->assertTrue($node->insertBefore(Node::findOne(20))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        $this->assertTrue($node->insertBefore(MultipleTreeNode::findOne(31))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        $this->assertTrue($node->insertBefore(MultipleTreeNode::findOne(36))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-insert-before-insert-begin.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testInsertBeforeUpdateSameNode()
    {
        $node = Node::findOne(4);
        $this->assertTrue($node->insertBefore(Node::findOne(2))->save());

        $node = MultipleTreeNode::findOne(38);
        $this->assertTrue($node->insertBefore(MultipleTreeNode::findOne(37))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-insert-before-update-same-node.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testInsertBeforeUpdateOtherNode()
    {
        $node = Node::findOne(9);
        $this->assertTrue($node->insertBefore(Node::findOne(16))->save());

        $node = MultipleTreeNode::findOne(35);
        $this->assertTrue($node->insertBefore(MultipleTreeNode::findOne(28))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-insert-before-update-other-node.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testInsertBeforeUpdateNext()
    {
        $node = Node::findOne(12);
        $this->assertTrue($node->insertBefore(Node::findOne(13))->save());

        $node = MultipleTreeNode::findOne(33);
        $this->assertTrue($node->insertBefore(MultipleTreeNode::findOne(34))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/data.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testInsertBeforeUpdateAnotherTree()
    {
        $node = MultipleTreeNode::findOne(26);
        $this->assertTrue($node->insertBefore(MultipleTreeNode::findOne(15))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-insert-before-update-another-tree.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testInsertBeforeInsertExceptionIsRaisedWhenTargetIsNewRecord()
    {
        $node = new Node(['slug' => 'new']);
        $node->insertBefore(new Node())->save();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testInsertBeforeInsertExceptionIsRaisedWhenTargetIsRoot()
    {
        $node = new Node(['name' => 'new']);
        $node->insertBefore(Node::findOne(1))->save();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testInsertBeforeUpdateExceptionIsRaisedWhenTargetIsSame()
    {
        $node = Node::findOne(3);
        $node->insertBefore(Node::findOne(3))->save();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testInsertBeforeUpdateExceptionIsRaisedWhenTargetIsChild()
    {
        $node = Node::findOne(10);
        $node->insertBefore(Node::findOne(23))->save();
    }

    public function testInsertAfterInsertMiddle()
    {
        $node = new Node(['slug' => 'new1']);
        $this->assertTrue($node->insertAfter(Node::findOne(21))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        $this->assertTrue($node->insertAfter(MultipleTreeNode::findOne(14))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        $this->assertTrue($node->insertAfter(MultipleTreeNode::findOne(37))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-insert-after-insert-middle.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testInsertAfterInsertEnd()
    {
        $node = new Node(['slug' => 'new1']);
        $this->assertTrue($node->insertAfter(Node::findOne(19))->save());

        $node = new MultipleTreeNode(['slug' => 'new1']);
        $this->assertTrue($node->insertAfter(MultipleTreeNode::findOne(10))->save());

        $node = new MultipleTreeNode(['slug' => 'new2']);
        $this->assertTrue($node->insertAfter(MultipleTreeNode::findOne(34))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-insert-after-insert-end.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testInsertAfterUpdateSameNode()
    {
        $node = Node::findOne(2);
        $this->assertTrue($node->insertAfter(Node::findOne(4))->save());

        $node = MultipleTreeNode::findOne(36);
        $this->assertTrue($node->insertAfter(MultipleTreeNode::findOne(37))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-insert-after-update-same-node.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testInsertAfterUpdateOtherNode()
    {
        $node = Node::findOne(6);
        $this->assertTrue($node->insertAfter(Node::findOne(21))->save());

        $node = MultipleTreeNode::findOne(32);
        $this->assertTrue($node->insertAfter(MultipleTreeNode::findOne(30))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-insert-after-update-other-node.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testInsertAfterUpdatePrev()
    {
        $node = Node::findOne(16);
        $this->assertTrue($node->insertAfter(Node::findOne(15))->save());


        $node = MultipleTreeNode::findOne(38);
        $this->assertTrue($node->insertAfter(MultipleTreeNode::findOne(37))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/data.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testInsertAfterUpdateAnotherTree()
    {
        $node = MultipleTreeNode::findOne(26);
        $this->assertTrue($node->insertAfter(MultipleTreeNode::findOne(21))->save());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-insert-after-update-another-tree.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testInsertAfterInsertExceptionIsRaisedWhenTargetIsNewRecord()
    {
        $node = new Node(['slug' => 'new']);
        $node->insertAfter(new Node())->save();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testInsertAfterInsertExceptionIsRaisedWhenTargetIsRoot()
    {
        $node = new Node(['slug' => 'new']);
        $node->insertAfter(Node::findOne(1))->save();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testInsertAfterUpdateExceptionIsRaisedWhenTargetIsSame()
    {
        $node = Node::findOne(3);
        $node->insertAfter(Node::findOne(3))->save();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testInsertAfterUpdateExceptionIsRaisedWhenTargetIsChild()
    {
        $node = Node::findOne(10);
        $node->insertAfter(Node::findOne(23))->save();
    }

    public function testDelete()
    {
        $this->assertEquals(1, Node::findOne(2)->delete());

        $this->assertEquals(1, MultipleTreeNode::findOne(30)->delete());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-delete.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testDeleteRoot()
    {
        Node::findOne(1)->delete();
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testDeleteExceptionIsRaisedWhenNodeIsNewRecord()
    {
        $node = new Node(['slug' => 'new']);
        $node->delete();
    }

    public function testDeleteWithChildren()
    {
        $this->assertEquals(4, Node::findOne(6)->deleteWithChildren());

        $this->assertEquals(1, MultipleTreeNode::findOne(28)->deleteWithChildren());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-delete-with-children.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testDeleteWithChildrenRoot()
    {
        $this->assertEquals(25, Node::findOne(1)->deleteWithChildren());

        $this->assertEquals(14, MultipleTreeNode::findOne(26)->deleteWithChildren());

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-delete-with-children-root.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    /**
     * @expectedException \yii\base\Exception
     */
    public function testDeleteWithChildrenExceptionIsRaisedWhenNodeIsNewRecord()
    {
        $node = new Node(['slug' => 'new']);
        $node->deleteWithChildren();
    }

    public function testOptimize()
    {
        Node::findOne(6)->optimize();
        MultipleTreeNode::findOne(9)->optimize();

        $dataSet = $this->getConnection()->createDataSet(['tree', 'multiple_tree']);
        $expectedDataSet = new ArrayDataSet(require(__DIR__ . '/data/test-optimize.php'));
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    /**
     * @expectedException \yii\base\NotSupportedException
     */
    public function testExceptionIsRaisedWhenInsertIsCalled()
    {
        $node = new Node(['slug' => 'new']);
        $node->insert();
    }

    public function testUpdate()
    {
        $node = Node::findOne(3);
        $node->slug = 'update';
        $this->assertEquals(1, $node->update());
    }
}