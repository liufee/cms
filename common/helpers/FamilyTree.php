<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-07-24 17:21
 */

namespace common\helpers;


use yii\helpers\ArrayHelper;

class FamilyTree
{

    /**
     * @var array
     */
    private $_tree;

    private $_parentSign = "parent_id";


    /**
     * FamilyTree constructor.
     *
     * @param array $tree
     */
    public function __construct(array $tree)
    {
        $this->_tree = $tree;
    }

    /**
     * @return array
     */
    public function getTree()
    {
        return $this->_tree;
    }

    /**
     * @param array $tree
     * @return FamilyTree
     */
    public function setTree($tree)
    {
        $this->_tree = $tree;
        return $this;
    }

    /**
     * @return string
     */
    public function getParentSign()
    {
        return $this->_parentSign;
    }

    /**
     * @param string $parentSign
     * @return FamilyTree
     */
    public function setParentSign($parentSign)
    {
        $this->_parentSign = $parentSign;
        return $this;
    }

    /**
     * 获取某节点的所有子节点
     *
     * @param $id
     * @return array
     */
    public function getSons($id)
    {
        $sons = [];
        foreach ($this->_tree as $key => $value) {
            if ($value[$this->_parentSign] == $id) {
                $sons[] = $value;
            }
        }
        return $sons;
    }

    /**
     * 获取某节点的所有子孙节点
     *
     * @param $id
     * @param int $level
     * @return array
     */
    public function getDescendants($id, $level = 1)
    {
        $nodes = [];
        foreach ($this->_tree as $key => $value) {
            if ($value[$this->_parentSign] == $id) {
                $value['level'] = $level;
                $nodes[] = $value;
                $nodes = array_merge($nodes, $this->getDescendants($value['id'], $level + 1));
            }
        }
        return $nodes;
    }

    /**
     * 获取某节点的所有父节点
     *
     * @param $id
     * @return array
     */
    public function getParents($id)
    {
        $nodes = [];
        $tree = ArrayHelper::index($this->_tree, 'id');
        foreach ($tree as $key => $value) {
            if ($tree[$id][$this->_parentSign] == $value['id']) {
                $nodes[] = $value;
            }
        }
        return $nodes;
    }

    /**
     * 获取某节点的所有祖先节点
     *
     * @param $id
     * @return array
     */
    public function getAncectors($id)
    {
        $array = $this->_getAncectors($id);
        if( isset($array[0]) ) unset($array[0]);
        return $array;
    }

    /**
     * 递归获取祖先节点
     *
     * @param $id
     * @return array
     */
    private function _getAncectors($id)
    {
        $nodes = [];
        foreach ($this->_tree as $key => $value) {
            if ($value['id'] == $id) {
                $nodes[] = $value;
                if ($value[$this->_parentSign] != 0) {
                    $nodes = array_merge($nodes, $this->_getAncectors($value[$this->_parentSign]));
                }
            }
        }
        return $nodes;
    }

}