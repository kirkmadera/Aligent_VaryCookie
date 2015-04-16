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
 * Aligent_VaryCookie_Test_Model_Keys
 *
 * @category  Aligent
 * @package   Aligent_VaryCookie
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @license   All Rights Reserved.
 * @link      http://www.aligent.com.au/
 */
class Aligent_VaryCookie_Test_Model_Keys extends PHPUnit_Framework_TestCase
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
        $this->assertEmpty($keys->getKeys(), 'Expect the keys to be empty before test');
        $this->assertFalse($keys->isSetKey('foo'));
        $keys->addKey('foo');
        $this->assertTrue($keys->isSetKey('foo'), 'Expect "foo" key to be set in keys after adding it');
    }

    public function testSetKeysWithEmptyArrayClearsKeys()
    {
        $keys = $this->_varyKeys;
        $this->assertFalse($keys->hasKeys(), 'Expect keys to be empty before test');
        $keys->setKeys(array('foo', 'bar', 'baz'));
        $this->assertTrue($keys->hasKeys(), 'Expect keys to contain keys after setting with non empty array');
        $keys->setKeys(array());
        $this->assertFalse($keys->hasKeys(), 'Expect keys to be empty after setting with empty array');
    }

    public function testGetKeysReturnsTheSameKeysThatWereSet()
    {
        $keys = $this->_varyKeys;
        $keys->setKeys(array('bar', 'baz', 'foo'));
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys());
    }

    public function testGetKeysReturnsSortedKeysThatWereSet()
    {
        $keys = $this->_varyKeys;
        $keys->setKeys(array('baz', 'foo', 'bar'));
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys());
    }

    public function testSetKeysDoesntCreateDuplicates()
    {
        $keys = $this->_varyKeys;
        $keys->setKeys(array('baz', 'foo', 'bar', 'foo'));
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys());
    }

    public function testAddKeysDoesntCreateDuplicates()
    {
        $keys = $this->_varyKeys;
        $keys->addKey('baz')->addKey('foo')->addKey('bar')->addKey('foo');
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys());
    }

    public function testIsSetWithSetKeys()
    {
        $keys = $this->_varyKeys;
        $this->assertFalse($keys->isSetKey('foo'), "Don't expect key to be set prior to setting");
        $keys->setKeys(array('foo'));
        $this->assertTrue($keys->isSetKey('foo'), 'Expect key to be set after setting');
    }

    public function testIsSetWithAddKey()
    {
        $keys = $this->_varyKeys;
        $this->assertFalse($keys->isSetKey('foo'), "Don't expect key to be set prior to adding");
        $keys->addKey('foo');
        $this->assertTrue($keys->isSetKey('foo'), 'Expect ke to be set after adding');
    }

    public function testRemoveKey()
    {
        $keys = $this->_varyKeys;
        $keys->setKeys(array('foo', 'bar', 'baz'));
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys(), 'Expect key to exist before removing');
        $keys->removeKey('bar');
        $this->assertSame(array('baz', 'foo'), $keys->getKeys(), 'Do not expect key to exist after removing');
    }

    public function testGetVaryString()
    {
        $keys = $this->_varyKeys;
        $keys->setKeys(array('foo', 'bar', 'baz'));
        $this->assertSame('bar|baz|foo', $keys->getVaryString());
    }

    public function testSetKeysFromVaryString()
    {
        $keys = $this->_varyKeys;
        $this->assertFalse($keys->hasKeys(), 'Expect keys to be empty before test');
        $keys->setKeysFromVaryString('bar|baz|foo');
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys());
    }

    public function testSetKeysFromVaryStringProducesOrderedKeys()
    {
        $keys = $this->_varyKeys;
        $this->assertFalse($keys->hasKeys(), 'Expect keys to be empty before test');
        $keys->setKeysFromVaryString('foo|bar|baz');
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys());
    }

    public function testSetKeysFromVaryStringDoesntCreateDuplicates()
    {
        $keys = $this->_varyKeys;
        $this->assertFalse($keys->hasKeys(), 'Expect keys to be empty before test');
        $keys->setKeysFromVaryString('foo|bar|baz|foo');
        $this->assertSame(array('bar', 'baz', 'foo'), $keys->getKeys());
    }
}
