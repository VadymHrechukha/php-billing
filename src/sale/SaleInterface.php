<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\sale;

use DateTimeImmutable;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\EntityInterface;
use hiqdev\php\billing\Exception\ConstraintException;
use hiqdev\php\billing\Exception\InvariantException;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\target\TargetInterface;

/**
 * Sale interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface SaleInterface extends EntityInterface
{
    /**
     * @return int|string|null
     */
    public function getId();

    /**
     * @return int|string|null
     */
    public function setId($id);

    /**
     * @return TargetInterface
     */
    public function getTarget();

    /**
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * @return PlanInterface
     */
    public function getPlan();

    /**
     * @return DateTimeImmutable
     */
    public function getTime();

    public function getCloseTime(): ?DateTimeImmutable;

    /**
     * @param DateTimeImmutable $time
     * @throws InvariantException
     * @throws ConstraintException
     */
    public function close(DateTimeImmutable $time): void;

    public function cancelClosing(): void;
}
