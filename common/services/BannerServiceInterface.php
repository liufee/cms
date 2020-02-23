<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-30 18:42
 */

namespace common\services;

interface BannerServiceInterface extends ServiceInterface
{
    const ServiceName = "bannerService";

    public function newBannerModel($id);
    public function getBannerList($id);
    public function getBannerDetail($id, $sign);
    public function updateBanner($id, $sign, array $postData = []);
    public function createBanner($id, array $postData = []);
    public function sortBanner($id, $sign, $sort);
    public function deleteBanner($id, $sign);

    public function getBannersByAdType($type);
}