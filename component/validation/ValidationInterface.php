<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/09/14
 * Time: 14:41
 */

namespace component\validation;

interface ValidationInterface {

    /**
     * Validates a request
     * @param Request $request
     * @return bool
     */
    public function validateRequest(\component\request\Request $request);

    /**
     * Get flag of validation result
     * @return bool
     */
    public function isRequestValid();

    /**
     * Return request object
     * @return mixed
     */
    public function getValidatedRequest();

} 