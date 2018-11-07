# laravel-amount
<a href="https://packagist.org/packages/hao-li/laravel-amount"><img src="https://poser.pugx.org/hao-li/laravel-amount/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/hao-li/laravel-amount"><img src="https://poser.pugx.org/hao-li/laravel-amount/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/hao-li/laravel-amount"><img src="https://poser.pugx.org/hao-li/laravel-amount/license.svg" alt="License"></a>

## 背景
系统中涉及到金额的字段，View 层表现的时候一般都是以**元**为单位使用小数形式展示，不过 Domain 层存储时从空间、性能、容错角度出发，经常以**分**为单位，用整型来存储。

在 Lavarel 中，可以在 Model 中添加属性方法进行转换

```php
public function getAmountAttribute($value)
{
    return $value / 100;
}

public function setAmountAttribute($value)
{
    $this->attributes['amount'] = (int)($value * 100);
}
```

不过涉及金额的字段比较多时就需要定义很多相同逻辑的函数，本项目即将该逻辑抽出为 Trait，简化金额字段相关的处理。
> 除了金额外，小数位数固定的**面积**、**长度**等场景使用起来也很方便。

## 原理

将转换逻辑封装在 AmountTrait 中，覆写 Model 类的 getMutatedAttributes, mutateAttributeForArray, getAttributeValue 及 setAttribute 方法，当访问相关字段时自动进行转换处理。

```php
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
```

## 依赖
Laravel >= 5.2

## 安装
```
composer require "hao-li/laravel-amount:dev-master"
```

## 使用

1. 在 Model 中引用 AmountTrait

  ```php
  use HaoLi\LaravelAmount\Traits\AmountTrait;
  ```

2. 使用 AmountTrait

  ```php
  use AmountTrait;
  ```

3. 定义金额字段（本例中为 amount）

  ```php
  protected $amountFields = ['amount'];
  ```
4. 通过 `$amountTimes` 指定金额字段的倍数（可选，默认 100）
  * 各金额字段使用相同的倍数
    ```php
    protected $amountTimes = 100;
    ```
  * 不同金额字段设置不同倍数
    ```php
    protected $amountTimes = [
        'amount' => 100,
    ]
    ```
5. 完成

  之后读取 amount 字段时，该字段的内容会自动从数据库的**分**转换为**元**，向其赋值时反之从**元**转换为**分**。

## FAQ

### 和别的 trait 中方法冲突

以 setRawAttributes 为例（此为之前方案，目前并未覆写此方法，仅为举例，其他方法原理相同）

1. 将冲突的方法分别重命名
  ```php
  use AmountTrait, BTrait {
      AmountTrait::setRawAttributes as amountTraitSetRawAttributes;
      BTrait::setRawAttributes as BTraitSetRawAttributes;
  }
  ```

2. 在 Model 中定义该冲突的方法，根据情况分别调用别名方法
  ```php
  public function setRawAttributes(array $attributes, $sync = false)
  {
      $this->BTraitSetRawAttributes($attributes, $sync);
      $attributes = $this->getAttributes();
      $this->amountTraitSetRawAttributes($attributes, $sync);
  }
  ```
  > 注意这里 $attributes 可能已被改变，所以再次使用时要重新取得最新值
