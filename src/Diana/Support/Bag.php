<?php

namespace Diana\Support;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class Bag extends Obj implements ArrayAccess, Iterator, Countable, JsonSerializable
{

    /**
     * The bag content.
     * @var mixed
     */
    public array $attributes;

    public function __construct(self|array $array = [])
    {
        if (is_a($array, self::class))
            $this->attributes = $array->attributes;
        else
            $this->attributes = (array) $array;

        foreach ($this->attributes as &$attribute) {
            if (is_array($attribute) || is_a($attribute, \stdClass::class))
                $attribute = new static($attribute);
        }
    }

    public function wrap(string $pre, string $post): string
    {
        $result = '';
        foreach ($this->attributes as $attribute)
            $result .= $pre . $attribute . $post;
        return $result;
    }

    public function join(string $separator): string
    {
        return join($separator, $this->attributes);
    }

    public function first(): mixed
    {
        $first = array_key_first($this->attributes);
        return $first ? $this->attributes[$first] : null;
    }

    public function flat(): static
    {
        return new static(iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($this->attributes)), true));
    }

    public function indexOf(string $value = null): bool|int|string
    {
        return array_search($value, $this->attributes);
    }

    public function map(callable $callback): static
    {
        $result = new static();
        foreach ($this->attributes as $key => $value)
            $result[$key] = $callback($value, $key, $this->attributes);

        return $result;
    }

    public function toString(): string
    {
        return json_encode($this);
    }

    private function bagIfBaggage(mixed $value): mixed
    {
        return is_array($value) ? new Bag($value) : $value;
    }

    public function offsetSet(mixed $name, mixed $value): void
    {
        $value = $this->bagIfBaggage($value);

        if ($name)
            $this->attributes[$name] = $value;
        else
            $this->attributes[] = $value;
    }

    public function offsetExists(mixed $name): bool
    {
        return isset ($this->attributes[$name]);
    }

    public function offsetUnset(mixed $name): void
    {
        unset($this->attributes[$name]);
    }

    public function offsetGet(mixed $name): mixed
    {
        return @$this->attributes[$name];
    }

    public function __set(mixed $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __get(mixed $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current(): mixed
    {
        return current($this->attributes);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        next($this->attributes);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key(): mixed
    {
        return key($this->attributes);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return key($this->attributes) !== null;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        reset($this->attributes);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count(): int
    {
        return count($this->attributes);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): mixed
    {
        return $this->attributes;
    }
}