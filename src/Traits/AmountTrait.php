<?php

namespace HaoLi\LaravelAmount\Traits;

trait AmountTrait
{
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
        if (is_numeric($value) && in_array($key, $this->getAmountFields())) {
            $value = $value / $this->getAmountTimes($key);
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        if (is_numeric($value) && in_array($key, $this->getAmountFields())) {
            $value = (int) round($value * $this->getAmountTimes($key));
        }
        parent::setAttribute($key, $value);
    }

    public function getAmountFields()
    {
        return (property_exists($this, 'amountFields')) ? $this->amountFields : [];
    }

    public function getAmountTimes($key)
    {
        $ret = 100;

        if (property_exists($this, 'amountTimes')) {
            if (is_array($this->amountTimes) && array_key_exists($key, $this->amountTimes)) {
                $ret = $this->amountTimes[$key];
            } elseif (is_numeric($this->amountTimes)) {
                $ret = $this->amountTimes;
            }
        }

        return $ret;
    }
}
