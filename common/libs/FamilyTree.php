<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-02-29 21:13
 */

namespace common\libs;


use yii\helpers\ArrayHelper;

trait FamilyTree
{
    abstract public function getItems();

    public function getIdSign()
    {
        return "id";
    }

    public function getParentSign()
    {
        return "parent_id";
    }

    public function getLevelSign()
    {
        return "level";
    }

    /**
     * 获取某节点的所有子节点
     *
     * @param $id
     * @return array
     */
    public function getSons($id)
    {
        $items = $this->getItems();
        $sons = [];
        foreach ($items as $key => $value) {
            if ($value[$this->getParentSign()] == $id) {
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
        $items = $this->getItems();
        foreach ($items as $key => $value) {
            if ($value[$this->getParentSign()] == $id) {
                $value[$this->getLevelSign()] = $level;
                $nodes[] = $value;
                $nodes = array_merge($nodes, $this->getDescendants($value[$this->getIdSign()], $level + 1));
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
        $items = $this->getItems();
        $items = ArrayHelper::index($items, $this->getIdSign());
        foreach ($items as $key => $value) {
            if ($items[$id][$this->getParentSign()] == $value[$this->getIdSign()]) {
                $nodes[] = $value;
            }
        }
        return $nodes;
    }

    /**
     * 递归获取祖先节点
     *
     * @param $id
     * @return array
     */
    public function getAncestors($id)
    {
        $nodes = [];
        $items = $this->getItems();
        foreach ($items as $key => $value) {
            if ($value[$this->getIdSign()] == $id) {
                $nodes[] = $value;
                if ($value[$this->getParentSign()] != 0) {
                    $nodes = array_merge($nodes, $this->getAncestors($value[$this->getParentSign()]));
                }
            }
        }
        return $nodes;
    }

}