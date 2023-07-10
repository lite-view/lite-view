<?php


/***
 * Class ArrayForm
 *
 * 实现 new ArrayObject($arr, ArrayObject::ARRAY_AS_PROPS) 功能
 */

namespace App\Common;


use ArrayAccess;

class ArrayForm implements ArrayAccess
{
    private $data;

    public function __construct($data = [])
    {
        $this->data = $data;
        foreach ($data as $field => $value) {
            $this->$field = $value;
        }
    }

    public function toArray()
    {
        return $this->data;
    }

    /* 下面两个方法用来存取对象属性 */

    public function __get($offset)
    {
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }

    public function __set($offset, $value)
    {
        $this->data[$offset] = $value;
        $this->$offset = $value;
    }

    /* 必须实现的ArrayAccess接口的以下4个方法 */

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }
}
