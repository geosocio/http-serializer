<?php

namespace GeoSocio\HttpSerializer\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Constraint Violation
 */
interface ConstraintViolationExceptionInterface
{
    /**
     * Constraint Violations.
     *
     * @return ConstraintViolationListInterface
     */
    public function getConstraintViolations() :? ConstraintViolationListInterface;
}
