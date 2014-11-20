<?php
class tubepress_platform_impl_url_puzzle_PuzzleBasedQuery implements tubepress_platform_api_url_QueryInterface
{
    /**
     * @var puzzle_Query
     */
    private $_delegate;

    /**
     * @var bool
     */
    private $_isFrozen = false;

    public function __construct(puzzle_Query $delegate)
    {
        $this->_delegate = $delegate;
    }

    /**
     * Add a value to a key.  If a key of the same name has already been added,
     * the key value will be converted into an array and the new value will be
     * pushed to the end of the array.
     *
     * @param string $key   Key to add
     * @param mixed  $value Value to add to the key
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     */
    public function add($key, $value)
    {
        $this->_assertNotFrozen();

        $this->_delegate->add($key, $value);

        return $this;
    }

    /**
     * Removes all key value pairs
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     */
    public function clear()
    {
        $this->_assertNotFrozen();

        $this->_delegate->clear();

        return $this;
    }

    /**
     * Iterates over each key value pair in the collection passing them to the
     * callable. If the callable returns true, the current value from input is
     * returned into the result tubepress_platform_api_url_QueryInterface.
     *
     * The callable must accept two arguments:
     * - (string) $key
     * - (string) $value
     *
     * @param callable $closure Evaluation function
     *
     * @return tubepress_platform_api_url_QueryInterface
     */
    public function filter($closure)
    {
        return $this->_delegate->filter($closure);
    }

    /**
     * Get a specific key value.
     *
     * @param string $key Key to retrieve.
     *
     * @return mixed|null Value of the key or NULL
     */
    public function get($key)
    {
        return $this->_delegate->get($key);
    }

    /**
     * Get all keys in the collection
     *
     * @return array
     */
    public function getKeys()
    {
        return $this->_delegate->getKeys();
    }

    /**
     * Returns whether or not the specified key is present.
     *
     * @param string $key The key for which to check the existence.
     *
     * @return bool
     */
    public function hasKey($key)
    {
        return $this->_delegate->hasKey($key);
    }

    /**
     * Checks if any keys contains a certain value
     *
     * @param string $value Value to search for
     *
     * @return mixed Returns the key if the value was found FALSE if the value
     *     was not found.
     */
    public function hasValue($value)
    {
        return $this->_delegate->hasValue($value);
    }

    /**
     * Returns a tubepress_platform_api_url_QueryInterface containing all the elements of the collection after
     * applying the callback function to each one.
     *
     * The callable should accept three arguments:
     * - (string) $key
     * - (string) $value
     * - (array) $context
     *
     * The callable must return a the altered or unaltered value.
     *
     * @param callable $closure Map function to apply
     * @param array    $context Context to pass to the callable
     *
     * @return tubepress_platform_api_url_QueryInterface
     */
    public function map($closure, array $context = array())
    {
        return $this->_delegate->map($closure, $context);
    }

    /**
     * Add and merge in a tubepress_platform_api_url_QueryInterface or array of key value pair data.
     *
     * @param tubepress_platform_api_url_QueryInterface|array $data Associative array of key value pair data
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     */
    public function merge($data)
    {
        $this->_assertNotFrozen();

        if ($data instanceof tubepress_platform_api_url_QueryInterface) {

            $data = $data->toArray();
        }

        $this->_delegate->merge($data);

        return $this;
    }

    /**
     * Over write key value pairs in this collection with all of the data from
     * an array or collection.
     *
     * @param array|Traversable $data Values to override over this config
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     */
    public function overwriteWith($data)
    {
        $this->_assertNotFrozen();

        $this->_delegate->overwriteWith($data);

        return $this;
    }

    /**
     * Remove a specific key value pair
     *
     * @param string $key A key to remove
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     */
    public function remove($key)
    {
        $this->_assertNotFrozen();

        $this->_delegate->remove($key);

        return $this;
    }

    /**
     * Replace the data of the object with the value of an array
     *
     * @param array $data Associative array of data
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     */
    public function replace(array $data)
    {
        $this->_assertNotFrozen();

        $this->_delegate->replace($data);

        return $this;
    }

    /**
     * Set a key value pair
     *
     * @param string $key   Key to set
     * @param mixed  $value Value to set
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     */
    public function set($key, $value)
    {
        $this->_assertNotFrozen();

        $this->_delegate->set($key, $value);

        return $this;
    }

    /**
     * Specify how values are URL encoded
     *
     * @param string|bool $type One of 'RFC1738', 'RFC3986', or false to disable encoding
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     * @throws InvalidArgumentException
     */
    public function setEncodingType($type)
    {
        $this->_assertNotFrozen();

        $this->_delegate->setEncodingType($type);

        return $this;
    }

    /**
     * @return array This query as an associative array.
     */
    public function toArray()
    {
        return $this->_delegate->toArray();
    }

    /**
     * Convert the query string parameters to a query string string
     *
     * @return string
     */
    public function toString()
    {
        return $this->__toString();
    }

    /**
     * Alias of toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_delegate->__toString();
    }

    /**
     * Prevent any modifications to this query.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function freeze()
    {
        $this->_isFrozen = true;
    }

    /**
     * @return bool True if this query is frozen, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isFrozen()
    {
        return $this->_isFrozen;
    }

    private function _assertNotFrozen()
    {
        if ($this->_isFrozen) {

            throw new BadMethodCallException('Query is frozen');
        }
    }
}