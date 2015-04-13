<?php
/**
 * Observer.php
 *
 * @category  Aligent
 * @package   Aligent_VaryCookie
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @license   All Rights Reserved.
 * @link      http://www.aligent.com.au/
 */

/**
 * Aligent_VaryCookie_Model_Observer
 *
 * @category  Aligent
 * @package   Aligent_VaryCookie
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @license   All Rights Reserved.
 * @link      http://www.aligent.com.au/
 */
class Aligent_VaryCookie_Model_Observer
{

    const COOKIE_NAME = 'AligentVary';

    /**
     * Observes the controller_front_send_response_before event, and sets the AligentVary cookie based on
     * the keys set in Aligent_VaryCookie_Model_VaryKeys
     *
     * @param Varien_Event_Observer $observer
     */
    public function setVaryCookie(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Controller_Varien_Front $front */
        $front = $observer->getFront();

        /** @var Aligent_VaryCookie_Model_Keys $varyKeys */
        $varyKeys = Mage::getSingleton('aligent_varycookie/varyKeys');

        // Allows custom modification of vary keys prior to setting the cookie.
        Mage::dispatchEvent('aligent_varycookie_cookie_set_before', array('keys' => $varyKeys));

        $cookie = Mage::getSingleton('core/cookie');
        $cookie->set(self::COOKIE_NAME, $varyKeys->getVaryString(), true);
    }
}