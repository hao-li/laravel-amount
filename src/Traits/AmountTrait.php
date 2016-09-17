<?php

namespace HaoLi\LaravelAmount\Traits;

trait AmountTrait
{
    public static $amountTimes = 100;

    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);
        if (in_array($key, $this->getAmountFields())) {
            $value = (int)($value / self::$amountTimes);
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getAmountFields())) {
            $value = (int)($value * self::$amountTimes);
        }
        parent::setAttribute($key, $value);
    }

    public function getAmountFields()
    {
        return (property_exists($this, 'amountFields')) ? $this->amountFields : null;
    }
}