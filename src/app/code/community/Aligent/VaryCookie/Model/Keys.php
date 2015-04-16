<?php
/**
 * Keys.php
 *
 * @category  Aligent
 * @package   Aligent_VaryCookie
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @license   All Rights Reserved.
 * @link      http://www.aligent.com.au/
 */

/**
 * Aligent_VaryCookie_Model_Keys
 *
 * This model should be used as a singleton, however keys are stored statically and will work like a singleton
 * regardless of the method of instantiation.
 *
 * Use the addKey() method to add a key for varying the cache.
 *
 * getVaryString() will return a string with sorted keys for sane use.
 *
 * Other methods are self explanatory.
 *
 * @category  Aligent
 * @package   Aligent_VaryCookie
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @license   All Rights Reserved.
 * @link      http://www.aligent.com.au/
 */
class Aligent_VaryCookie_Model_Keys
{

    const GLUE_CHAR = '|';
    const ASSIGN_CHAR = '=';

    protected static $varyKeys = array();

    /**
     * Add a key to the vary cookies. Duplicate keys will be replaced.
     *
     * @param string $key
     * @param scalar $value Note, true and false values will be converted to string 'true' and 'false' respectively.
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function addKey($key, $value = null)
    {

        switch (true) {
            case is_null($value) :
                $value = true;
                break;
            case true === $value :
                $value = 'true';
                break;
            case false === $value :
                $value = 'false';
                break;
        }

        if (!is_string($key)) {
            throw new InvalidArgumentException('Keys must be strings');
        }
        if (!is_scalar($value)) {
            throw new InvalidArgumentException('Keys can only hold scalar values.');
        }
        self::$varyKeys[$key] = $value;

        return $this;
    }

    /**
     * Remove a key from the vary cookies. A nonexistent key will be silently ignored.
     *
     * @param $key
     *
     * @return $this
     */
    public function removeKey($key)
    {
        if (array_key_exists($key, self::$varyKeys)) {
            unset (self::$varyKeys[$key]);
        }

        return $this;
    }

    /**
     * Replaces all existing keys with those supplied here. The values of the array will be used for vary keys, the keys
     * of the array will be ignored. Any duplicates will be ignored. Boolean values will be converted to string
     * 'true' and 'false'.
     *
     * @param array $keys
     *
     * @return $this
     */
    public function setKeys(array $keys)
    {
        self::$varyKeys = array();
        foreach ($keys as $index => $key) {
            if (!is_int($index)) {
                throw new InvalidArgumentException(
                    '$keys array in setKeys() must have int indexes. Indexes will be discarded'
                );
            }
            $value = null;
            if (is_array($key)) {
                if (count($key) <> 1) {
                    throw new \JsonSchema\Exception\InvalidArgumentException(
                        '$keys array must contain string keys or single element key value arrays'
                    );
                }
                $value = current($key);
                $key   = key($key);
            }
            $this->addKey($key, $value);
        }

        return $this;
    }

    /**
     * Gets the value that was set for the key.
     * If no value was set, then will return true.
     * If the key was not set, will return false.
     *
     * @param $key
     *
     * @return true|string
     */
    public function getKey($key)
    {
        if (!array_key_exists($key, self::$varyKeys)) {
            return false;
        }

        return self::$varyKeys[$key];
    }

    /**
     * Checks whether or not a particular key is set.
     *
     * @param $key
     *
     * @return bool
     */
    public function isSetKey($key)
    {
        return array_key_exists($key, self::$varyKeys);
    }

    /**
     * Returns a sorted array of vary keys.
     * Note this method only returns the key indexes, not the values.
     * To get the key values, @see getKeyValues()
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->getKeyValues());
    }

    /**
     * Returns a sorted array of key value pairs.
     *
     * Note that if no value was set for a key, then the value returned here will be true. This is to ensure is_set
     * checks for keys will work as expected.
     *
     * If a key's value was originally true, the a string 'true' will be returned instead. This is because cookies
     * and other cache keys can only contain string values.
     *
     * @return array
     */
    public function getKeyValues()
    {
        ksort(self::$varyKeys);

        return self::$varyKeys;
    }

    /**
     * Checks whether any keys have been set.
     *
     * @return bool Whether or not there are any keys.
     */
    public function hasKeys()
    {
        return count(self::$varyKeys) > 0;
    }

    /**
     * Returns a string that can be used for a vary cookie.
     *
     * @return string
     */
    public function getVaryString()
    {
        $keys      = $this->getKeyValues();
        $keyValues = array();
        foreach ($keys as $key => $value) {
            if ($value === true) {
                $keyValues[] = $key;
            } else {
                $keyValues[] = $key . self::ASSIGN_CHAR . $value;
            }
        }
        $varyString = implode(self::GLUE_CHAR, $keyValues);

        return $varyString;
    }

    /**
     * Replaces all existing keys with the keys supplied in $varyString.
     *
     * @param $varyString
     *
     * @return $this
     */
    public function setKeysFromVaryString($varyString)
    {
        $keys      = explode(self::GLUE_CHAR, $varyString);
        $keyValues = array();
        foreach ($keys as $key) {
            $keyValue = explode(self::ASSIGN_CHAR, $key);
            $key      = null;
            $value    = null;
            switch (count($keyValue)) {
                case 1:
                    $key   = array_shift($keyValue);
                    $value = null;
                    break;
                case 2:
                    $key   = array_shift($keyValue);
                    $value = array_shift($keyValue);
                    break;
                default:
                    Mage::logException(
                        new RuntimeException(srptintf('Unable to decode AligentVary cookie %s', $varyString))
                    );
                    continue 2; // Continue with next iteration of loop, @see http://php.net/manual/en/control-structures.continue.php
            }
            $keyValues[] = array($key => $value);
        }
        $this->setKeys($keyValues);

        return $this;
    }
}