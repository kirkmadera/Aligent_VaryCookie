<?php
/**
 * Config.php
 *
 * @category  Aligent
 * @package   Aligent_VaryCookie
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @license   All Rights Reserved.
 * @link      http://www.aligent.com.au/
 */

/**
 * Aligent_VaryCookie_Test_Config_Config
 *
 * @category  Aligent
 * @package   Aligent_VaryCookie
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @license   All Rights Reserved.
 * @link      http://www.aligent.com.au/
 */
class Aligent_VaryCookie_Test_Config_Config extends EcomDev_PHPUnit_Test_Case_Config
{

    /**
     * A simple smoke test to ensure the unit tests are set up correctly.
     */
    public function testSmoke()
    {
        $this->assertModelAlias('aligent_varycookie/foo', 'Aligent_VaryCookie_Model_Foo');
    }

}
