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

    protected static $varyKeys = array();

    /**
     * Add a key to the vary cookies. A duplicate key will be silently ignored.
     * 
     * @param $key
     *
     * @return $this
     */
    public function addKey($key)
    {
        self::$varyKeys[$key] = true;
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
     * Replaces any existing keys with those supplied here. The values of the array will be used for vary keys, the keys
     * of the array will be ignored. Any duplicates will be ignored.
     * 
     * @param array $keys
     *
     * @return $this
     */
    public function setKeys(array $keys)
    {
        self::$varyKeys = array_fill_keys($keys, true);
        return $this;
    }

    /**
     * Returns a sorted array of vary keys.
     * 
     * @return array
     */
    public function getKeys()
    {
        ksort(self::$varyKeys);
        return array_keys(self::$varyKeys);
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
    public function getVaryString() {
        $keys = $this->getKeys();
        $varyString = implode(self::GLUE_CHAR, $keys);
        return $varyString;
    }

}