<?php

use api\tests\FunctionalTester;

/**
 * Here you can initialize variables via \Codeception\Util\Fixtures class
 * to store data in global array and use it in Cests.
 *
 * ```php
 * // Here _bootstrap.php
 * \Codeception\Util\Fixtures::add('user1', ['name' => 'davert']);
 * ```
 *
 * In Cests
 *
 * ```php
 * \Codeception\Util\Fixtures::get('user1');
 * ```
 */

function getTokenFunctional(FunctionalTester $I){
    $I->sendPOST("/login", ["username"=>"feehi", "password"=>123456]);
    $I->canSeeResponseContains("accessToken");
    $dt = $I->grabResponse();
    $token = json_decode($dt, true)['accessToken'];
    return $token;
}