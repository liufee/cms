<?php

use api\tests\AcceptanceTester;

/**
 * Here you can initialize variables via \Codeception\Util\Fixtures class
 * to store data in global array and use it in Cepts.
 *
 * ```php
 * // Here _bootstrap.php
 * \Codeception\Util\Fixtures::add('user1', ['name' => 'davert']);
 * ```
 *
 * In Cept
 *
 * ```php
 * \Codeception\Util\Fixtures::get('user1');
 * ```
 */

function getToken(AcceptanceTester $I){
    $I->sendPOST("/login", ["username"=>"feehi", "password"=>123456]);
    $I->canSeeResponseContains("accessToken");
    $dt = $I->grabResponse();
    return json_decode($dt, true)['accessToken'];
}