<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-02-02 19:57
 */

namespace backend\models\search;


interface SearchInterface
{
    public function search(array $params = [], array $options = []);
}