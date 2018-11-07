<?php

namespace HaoLi\LaravelAmount\Tests;

use PHPUnit\Framework\TestCase;
use HaoLi\LaravelAmount\Traits\AmountTrait;

final class AmountTraitTest extends TestCase
{
    use AmountTrait;

    protected $amountFields = ['test1', 'test2', 'test3'];

    public function testArrayGetAmountTimes()
    {
        self::$amountTimes = [
            'test1' => 1000,
            'test2' => 10000,
        ];
        $this->assertEquals(1000, $this->getAmountTimes('test1'));
        $this->assertEquals(10000, $this->getAmountTimes('test2'));
        $this->assertEquals(100, $this->getAmountTimes('test3'));
    }

    public function testNumericGetAmountTimes()
    {
        self::$amountTimes = 1000;
        $this->assertEquals(1000, $this->getAmountTimes('test1'));
        $this->assertEquals(1000, $this->getAmountTimes('test2'));
        $this->assertEquals(1000, $this->getAmountTimes('test3'));

        self::$amountTimes = '1000';
        $this->assertEquals(1000, $this->getAmountTimes('test1'));
        $this->assertEquals(1000, $this->getAmountTimes('test2'));
        $this->assertEquals(1000, $this->getAmountTimes('test3'));
    }

    public function testOtherGetAmountTimes()
    {
        self::$amountTimes = 'a';
        $this->assertEquals(100, $this->getAmountTimes('test1'));
        $this->assertEquals(100, $this->getAmountTimes('test2'));
        $this->assertEquals(100, $this->getAmountTimes('test3'));

        self::$amountTimes = null;
        $this->assertEquals(100, $this->getAmountTimes('test1'));
        $this->assertEquals(100, $this->getAmountTimes('test2'));
        $this->assertEquals(100, $this->getAmountTimes('test3'));
    }
}
