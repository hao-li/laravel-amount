<?php

namespace HaoLi\LaravelAmount\Traits;

trait AmountTrait
{
    public static $amountTimes = 100;

    public function setRawAttributes(array $attributes, $sync = false)
    {
        $amountFields = $this->getAmountFields();

        foreach ($attributes as $attribute => &$value) {
            if (in_array($attribute, $amountFields)) {
                $value = $value / self::$amountTimes;
            }
        }

        parent::setRawAttributes($attributes, $sync);
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
        return (property_exists($this, 'amountFields')) ? $this->amountFields : [];
    }
}