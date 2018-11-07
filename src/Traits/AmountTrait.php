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
            ? $value / $this->getAmountTimes($key)
            : parent::mutateAttributeForArray($key, $value);
    }

    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);
        if (in_array($key, $this->getAmountFields())) {
            $value = $value / $this->getAmountTimes($key);
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getAmountFields())) {
            $value = (int)round($value * $this->getAmountTimes($key));
        }
        parent::setAttribute($key, $value);
    }

    public function getAmountFields()
    {
        return (property_exists($this, 'amountFields')) ? $this->amountFields : [];
    }

    public function getAmountTimes($key)
    {
        if (is_array(self::$amountTimes) && array_key_exists($key, self::$amountTimes)) {
            $ret = self::$amountTimes[$key];
        } else if (is_numeric(self::$amountTimes)) {
            $ret = self::$amountTimes;
        } else {
            $ret = 100;
        }
        return $ret;
    }
}
