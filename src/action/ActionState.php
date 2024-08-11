<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

/**
 * Action State.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ActionState
{
    private const STATE_NEW       = 'new';

    private const STATE_FINISHED  = 'finished';

    private const STATE_PREMATURE = 'premature';

    private const STATE_FUTURE    = 'future';

    private const STATE_CANCELED  = 'canceled';

    private const STATE_EXPIRED   = 'expired';

    private function __construct(protected string $state = self::STATE_NEW)
    {
    }

    public function getName(): string
    {
        return $this->state;
    }

    public function isNew(): bool
    {
        return $this->state === self::STATE_NEW;
    }

    public function isFinished(): bool
    {
        return !$this->isNew();
    }

    public static function new(): self
    {
        return new self(self::STATE_NEW);
    }

    /**
     * @deprecated use ActionState::expired()
     * @return self
     */
    public static function finished(): self
    {
        return new self(self::STATE_FINISHED);
    }

    public static function premature(): self
    {
        return new self(self::STATE_PREMATURE);
    }

    public static function future(): self
    {
        return new self(self::STATE_FUTURE);
    }

    public static function canceled(): self
    {
        return new self(self::STATE_CANCELED);
    }

    public static function expired(): self
    {
        return new self(self::STATE_EXPIRED);
    }

    public static function fromString(string $name): self
    {
        $allowedStates = [
            self::STATE_NEW,
            self::STATE_FINISHED,
            self::STATE_PREMATURE,
            self::STATE_FUTURE,
            self::STATE_CANCELED,
            self::STATE_EXPIRED,
        ];
        foreach ($allowedStates as $state) {
            if ($state === $name) {
                return new self($state);
            }
        }

        throw new \Exception("wrong action state '$name'");
    }
}
