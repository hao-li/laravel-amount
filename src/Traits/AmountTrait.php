<?php

namespace HaoLi\LaravelAmount\Traits;

trait AmountTrait
{
    public static $amountTimes = 100;
    private $amountSetFields = [];

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

    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);
        if (in_array($key, $this->getAmountFields()) && array_key_exists($key, $this->amountSetFields)) {
            $value = $value / self::$amountTimes;
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getAmountFields())) {
            $this->amountSetFields[$key] = $value;
            $value = (int)($value * self::$amountTimes);
        }
        parent::setAttribute($key, $value);
    }

    public function getAmountFields()
    {
        return (property_exists($this, 'amountFields')) ? $this->amountFields : [];
    }
}