<?php
/**
 * Created by PhpStorm.
 * User: yousan
 * Date: 12/11/15
 * Time: 4:56 PM
 *
 * Original: http://qiita.com/Hiraku/items/71e385b56dcaa37629fe
 */

abstract class AP_Enum
{
    private $scalar;

    function __construct($value)
    {
        $ref = new ReflectionObject($this);
        $consts = $ref->getConstants();
        if (! in_array($value, $consts, true)) {
            throw new InvalidArgumentException;
        }

        $this->scalar = $value;
    }

    final static function __callStatic($label, $args)
    {
        $class = get_called_class();
        $const = constant("$class::$label");
        return new $class($const);
    }

    //元の値を取り出すメソッド。
    //メソッド名は好みのものに変更どうぞ
    final function valueOf()
    {
        return $this->scalar;
    }

    final function __toString()
    {
        return (string)$this->scalar;
    }
}