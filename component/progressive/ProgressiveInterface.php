<?php
/**
 * Created by PhpStorm.
 * User: entymon
 * Date: 03/11/14
 * Time: 16:53
 */

namespace component\progressive;


use component\request\Request;

interface ProgressiveInterface {

    public function progressiveServerCallToReadLevels();

    public function progressiveWinUpdateByCurrency();

    public function getProgressiveContributionValue();

    public function updateProgressiveContribution(Request $request);

    public function execute(Request $request);
} 