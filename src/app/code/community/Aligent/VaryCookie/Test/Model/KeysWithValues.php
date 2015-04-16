<?php
/**
 * KeysWithValues.php
 *
 * @category  Aligent
 * @package   Aligent_VaryCookie
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @license   All Rights Reserved.
 * @link      http://www.aligent.com.au/
 */

/**
 * Aligent_VaryCookie_Test_Model_Keys
 *
 * @category  Aligent
 * @package   Aligent_VaryCookie
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @license   All Rights Reserved.
 * @link      http://www.aligent.com.au/
 */

class Aligent_VaryCookie_Test_Model_KeysWithValues extends PHPUnit_Framework_TestCase
{

    /** @var Aligent_VaryCookie_Model_Keys */
    protected $_varyKeys;

    protected function setUp()
    {
        parent::setUp();
        $this->_varyKeys = Mage::getSingleton('aligent_varycookie/keys');
        $this->_varyKeys->setKeys(array());
    }

    public function testAddKey()
    {
        $keys = $this->_varyKeys;
        $keys->addKey('foo', 'bar');
        $this->assertTrue($keys->isSetKey('foo'), 'Expect "foo" key to be set in keys after adding it');
    }

    public function testGetKeyAfterAdd()
    {
        $keys = $this->_varyKeys;
        $keys->addKey('foo', 'bar');
        $this->assertSame('bar', $keys->getKey('foo'), "Expect key's value to be the same as what was set.");
    }

    public function testGetKeyAfterSet()
    {
        $keys = $this->_varyKeys;
        $keys->setKeys(array('foo', array('bar' => 'test'), 'baz'));
        $this->assertSame('test', $keys->getKey('bar'));
    }

    public function testGetKeyOnUnsetKeyReturnsFalse()
    {
        $keys = $this->_varyKeys;
        $this->assertFalse($keys->getKey('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddKeyOnlyAllowsScalarValues()
    {
        $keys = $this->_varyKeys;
        $keys->addKey('foo', array('bar'));
    }

    public function testAddKeyWithFalseValueConvertsToStringFalse()
    {
        $keys = $this->_varyKeys;
        $keys->addKey('foo', false);
        $this->assertSame('false', $keys->getKey('foo'));
    }

    public function testAddKeyWithTrueValueConvertsToStringTrue()
    {
        $keys = $this->_varyKeys;
        $keys->addKey('foo', true);
        $this->assertSame('true', $keys->getKey('foo'));
    }

    public function testGetKeysReturnsTheSameKeysThatWereSet()
    {
        $keys = $this->_varyKeys;
        $keys->setKeys(array('bar', array('baz' => 'test'), array('foo' => 'bar')));
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys());
        $this->assertSame(array('bar' => true, 'baz' => 'test', 'foo' => 'bar'), $keys->getKeyValues());
    }

    public function testGetKeysReturnsSortedKeysThatWereSet()
    {
        $keys = $this->_varyKeys;
        $keys->setKeys(array(array('baz' => 'test'), array('foo' => 'bar'), 'bar'));
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys());
        $this->assertSame(array('bar' => true, 'baz' => 'test', 'foo' => 'bar'), $keys->getKeyValues());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetKeysWithNonNumericIndexThrowsException()
    {
        $keys = $this->_varyKeys;
        $keys->setKeys(array('foo', 'bar' => 'baz', 'quz'));
    }

    public function testSetKeysLastKeySetWins()
    {
        $keys = $this->_varyKeys;
        $keys->setKeys(array('baz', array('foo' => 'test1'), 'bar', array('foo' => 'test2')));
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys());
        $this->assertSame(array('bar' => true, 'baz' => true, 'foo' => 'test2'), $keys->getKeyValues());
    }

    public function testAddKeysLastKeyAddedWins()
    {
        $keys = $this->_varyKeys;
        $keys->addKey('baz')->addKey('foo', 'test1')->addKey('bar')->addKey('foo', 'test2');
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys());
        $this->assertSame(array('bar' => true, 'baz' => true, 'foo' => 'test2'), $keys->getKeyValues());
    }

    public function testGetVaryString()
    {
        $keys = $this->_varyKeys;
        $keys->setKeys(array(array('foo' => 'bar'), array('bar' => 'test'), 'baz'));
        $this->assertSame('bar=test|baz|foo=bar', $keys->getVaryString());
    }

    public function testSetKeysFromVaryString()
    {
        $keys = $this->_varyKeys;
        $this->assertFalse($keys->hasKeys(), 'Expect keys to be empty before test');
        $keys->setKeysFromVaryString('bar=test|baz|foo=bar');
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys());
        $this->assertSame(array('bar' => 'test', 'baz' => true, 'foo' => 'bar'), $keys->getKeyValues());
    }

    public function testSetKeysFromVaryStringLastKeyWins()
    {
        $keys = $this->_varyKeys;
        $this->assertFalse($keys->hasKeys(), 'Expect keys to be empty before test');
        $keys->setKeysFromVaryString('foo=test1|bar|baz|foo=test2');
        $this->assertSame(array('bar' => true, 'baz' => true, 'foo' => 'test2'), $keys->getKeyValues());
    }
}
