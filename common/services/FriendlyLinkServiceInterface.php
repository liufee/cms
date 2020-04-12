<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-23 09:38
 */

namespace common\services;


interface FriendlyLinkServiceInterface extends ServiceInterface {
    const ServiceName = "friendlyLinkService";

    public function getFriendlyLinks();

    public function getFriendlyLinkCountByPeriod($startAt=null, $endAt=null);
}