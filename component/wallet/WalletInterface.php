<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 13/10/14
 * Time: 15:09
 */

namespace component\wallet;


use component\communication\Communication;
use component\request\Request;

/**
 * Interface WalletInterface
 * @author Samuel I. Amaziro
 * @package component\wallet
 */
interface WalletInterface
{

    /**
     * Before Login Process
     * @return mixed
     */
    public function beforeLogin();

    /**
     * Place Bet Process
     * @return mixed
     */
    public function placeBet();

    /**
     * Settle Bet Process
     * @return mixed
     */
    public function settleBet();

    /**
     * Place and Settle Bet Process
     * @return mixed
     */
    public function placeAndSettleBet();

    /**
     * End Process
     * @return mixed
     */
    public function end();

    /**
     * Execute Process
     * @param Request $request
     * @return mixed
     */
    public function executeProcess(Request $request, Communication $communication);
} 