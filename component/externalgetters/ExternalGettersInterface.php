<?php
/**
 * Created by PhpStorm.
 * User: entymon
 * Date: 30/10/14
 * Time: 16:55
 */

namespace component\externalgetters;


use component\request\Request;

interface ExternalGettersInterface {

    public function execute(Request $request);

    public function getMethodsList();

    public function getLicenseeDetails();

} 