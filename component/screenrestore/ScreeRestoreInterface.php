<?php
/**
 * Created by PhpStorm.
 * User: entymon
 * Date: 30/10/14
 * Time: 15:44
 */

namespace component\screenrestore;


use component\request\Request;

interface ScreeRestoreInterface {

    public function loadScreenRestore();

    public function saveScreenRestore();

    public function execute(Request $request);

} 