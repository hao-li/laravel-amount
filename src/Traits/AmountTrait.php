<?php

namespace HaoLi\LaravelAmount\Traits;

trait AmountTrait
{
    public static $amountTimes = 100;

    public function getMutatedAttributes()
    {
        $attributes = parent::getMutatedAttributes();
        return array_merge($attributes, $this->getAmountFields());
    }

    protected function mutateAttributeForArray($key, $value)
    {
        return (in_array($key, $this->getAmountFields()))
            ? $value / self::$amountTimes
            : parent::mutateAttributeForArray($key, $value);
    }

    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);
        if (in_array($key, $this->getAmountFields())) {
            $value = $value / self::$amountTimes;
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
        return (property_exists($this, 'amountFields')) ? $this->amountFields : [];
    }
}