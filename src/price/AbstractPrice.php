<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\price;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\ChargeModifier;
use hiqdev\php\billing\charge\SettableChargeModifierTrait;
use hiqdev\php\billing\Exception\CannotReassignException;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Price.
 * @see PriceInterface
 * By default Price is applicable when same target and same type as Action.
 * But it can be different e.g. same price for all targets when certain type.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
abstract class AbstractPrice implements PriceInterface, ChargeModifier
{
    use SettableChargeModifierTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var TypeInterface
     */
    protected $type;

    /**
     * @var TargetInterface
     */
    protected $target;

    /**
     * @var PlanInterface|null
     */
    protected $plan;

    public function __construct(
        $id,
        TypeInterface $type,
        TargetInterface $target,
        PlanInterface $plan = null
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->target = $target;
        $this->plan = $plan;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlan(): ?PlanInterface
    {
        return $this->plan;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPlan()
    {
        return $this->plan !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlan(PlanInterface $plan)
    {
        if ($this->hasPlan()) {
            throw new CannotReassignException('price plan');
        }
        $this->plan = $plan;
    }

    /**
     * {@inheritdoc}
     * Default sum calculation method: sum = price * usage.
     */
    public function calculateSum(QuantityInterface $quantity): ?Money
    {
        $usage = $this->calculateUsage($quantity);
        if ($usage === null) {
            return null;
        }

        $price = $this->calculatePrice($quantity);
        if ($price === null) {
            return null;
        }

        /// TODO add configurable rounding mode later
        return $price->multiply(sprintf('%.14F', $usage->getQuantity()), Money::ROUND_UP);
    }

    public function jsonSerialize(): array
    {
        $data = [
            'id' => $this->id,
            'class' => (new \ReflectionClass($this))->getShortName(),
            'type' => $this->getType()->getName(),
            'type_id' => $this->getType()->getId(),
            'object_id' => $this->getTarget()->getId(),
            'object' => [
                'id' => $this->getTarget()->getId(),
                'name' => $this->getTarget()->getName(),
                'type' => $this->getTarget()->getType(),
            ],
            'plan_id' => $this->getPlan()?->getId(),
        ];

        if ($this instanceof PriceWithSubpriceInterface) {
            $data['subprice'] = $this->getSubprices()->values();
        }

        if ($this instanceof PriceWithRateInterface) {
            $data['rate'] = $this->getRate();
        }

        if ($this instanceof PriceWithUnitInterface) {
            $data['unit'] = $this->getUnit()->getName();
        }

        if ($this instanceof PriceWithCurrencyInterface) {
            $data['currency'] = $this->getCurrency()->getCode();
        }

        if ($this instanceof PriceWithSumsInterface) {
            $data['sums'] = $this->getSums()->values();
        }

        if ($this instanceof PriceWithQuantityInterface) {
            $data['quantity'] = $this->getPrepaid()->getQuantity();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(ActionInterface $action): bool
    {
        /* sorry, debugging facility
        var_dump([
            'action.target'     => $action->getTarget(),
            'price.target'      => $this->getTarget(),
            'action.type'       => $action->getType(),
            'price.type'        => $this->getType(),
            'target matches'    => $action->getTarget()->matches($this->getTarget()),
            'type matches'      => $action->getType()->matches($this->getType()),
        ]); */
        return $action->getTarget()->matches($this->getTarget()) &&
               $action->getType()->matches($this->getType());
    }
}
