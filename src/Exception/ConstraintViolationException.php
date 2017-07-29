<?php

namespace GeoSocio\HttpSerializer\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Constraint Violation Http Exception
 */
class ConstraintViolationException extends BadRequestHttpException implements ConstraintViolationExceptionInterface
{
    /**
     * @var ConstraintViolationListInterface
     */
    protected $constraintViolations;

    /**
     * {@inheritdoc}
     *
     * @param ConstraintViolationListInterface $constraintViolations
     */
    public function __construct(?ConstraintViolationListInterface $constraintViolations = null, $message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct($message, $previous, $code);
        $this->constraintViolations = $constraintViolations;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintViolations() :? ConstraintViolationListInterface
    {
        return $this->constraintViolations;
    }
}
