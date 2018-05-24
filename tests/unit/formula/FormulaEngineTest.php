<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\formula;

use DateTimeImmutable;
use hiqdev\php\billing\charge\modifiers\addons\MonthPeriod;
use hiqdev\php\billing\charge\modifiers\addons\Reason;
use hiqdev\php\billing\charge\modifiers\addons\Since;
use hiqdev\php\billing\charge\modifiers\FixedDiscount;
use hiqdev\php\billing\charge\modifiers\Leasing;
use hiqdev\php\billing\formula\FormulaEngine;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class FormulaEngineTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->engine = new FormulaEngine();
    }

    public function testSimpleDiscount()
    {
        $date = '2018-08-01';
        $rate = '2';
        $reason = 'test reason';
        $formula = $this->engine->build("discount.fixed('$rate%').since('$date').reason('$reason')");

        $this->assertInstanceOf(FixedDiscount::class, $formula);
        $this->assertSame($rate, $formula->getValue()->getValue());
        $this->assertTrue($formula->isRelative());
        $this->assertInstanceOf(Since::class, $formula->getSince());
        $this->assertEquals(new DateTimeImmutable($date), $formula->getSince()->getValue());
        $this->assertInstanceOf(Reason::class, $formula->getReason());
        $this->assertSame($reason, $formula->getReason()->getValue());
        $this->assertNull($formula->getTill());
    }

    public function testSimpleLeasing()
    {
        $date = '2018-08-01';
        $num = 2;
        $reason = 'test reason';
        $formula = $this->engine->build("leasing.since('$date').lasts('$num months').reason('$reason')");

        $this->assertInstanceOf(Leasing::class, $formula);
        $this->assertInstanceOf(MonthPeriod::class, $formula->getTerm());
        $this->assertSame($num, $formula->getTerm()->getValue());
        $this->assertInstanceOf(Since::class, $formula->getSince());
        $this->assertEquals(new DateTimeImmutable($date), $formula->getSince()->getValue());
        $this->assertInstanceOf(Reason::class, $formula->getReason());
        $this->assertSame($reason, $formula->getReason()->getValue());
        $this->assertNull($formula->getTill());
    }
}
