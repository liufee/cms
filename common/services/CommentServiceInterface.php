<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-23 11:40
 */

namespace common\services;


interface CommentServiceInterface extends ServiceInterface
{
    const ServiceName = "commentService";

    public function getRecentComments($limit = 10);

    public function getCommentCountByPeriod($startAt=null, $endAt=null);
}