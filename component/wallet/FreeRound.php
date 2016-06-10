<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 05/11/14
 * Time: 15:34
 */

namespace component\wallet;


use application\BaseComponent;
use exceptions\FreeRoundException;
use model\GMAPIDataRepository;

class FreeRound {

    /**
     * The request object containing all the details of the error
     * @var \component\request\Request
     */
    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }


    protected function getOperatorId()
    {
        $id = null;
        if($this->request->getOperatorName() == '0')
        {
            $id = $this->request->getOperatorName();
        }
        else
        {
            $data = new GMAPIDataRepository('bo_operators_per_licensee');
            $result = $data->fetchOperatorIdByLicenseeAndOperatorName($this->request->getLicenseeId(),$this->request->getOperatorName());

            if ((is_array($result) && sizeof($result) == 0) || $result === false )
            {
                BaseComponent::getLogger()->addException(new FreeRoundException("Could not fetch operator for free round ", 1, 1));
            }
            else
            {
                $id = $result['operator_id'];
            }
        }


        return $id;


    }
} 