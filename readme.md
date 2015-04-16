Aligent VaryCookie Extension
=====================
Use this module with some custom Varnish rules to vary the Varnish cache based on a cookie.
Facts
-----
- version: 0.0.0
- extension key: Aligent_VaryCookie
- [extension on GitHub](https://github.com/aligent/Aligent_VaryCookie)

Description
-----------
Use this module with some custom Varnish rules to vary the Varnish cache based on a cookie. 

To get started, you will need to add the following changes to your Varnish config file, usually `/etc/varnish/default.vcl`.

- Convert any incoming `AligentVary` cookies to an `X-AligentVary` header, add the following lines somewhere near the end of
  the `vcl_recv` section:

```
   if (req.http.Cookie ~ "AligentVary=") {
     set req.http.X-AligentVary = regsub(req.http.cookie, ".*AligentVary=([^;]+);.*", "\1");
   }
```

- Tell Varnish to vary on the `X-AligentVary header`, add the following lines somewhere in the content delivery section of 
  the `vcl_fetch` section:
  
```
   if (beresp.http.Vary) {
     set beresp.http.Vary = beresp.http.Vary + ", X-AligentVary";
   } else {
     set beresp.http.Vary = "X-AligentVary";
   }
```

- Finally, Varnish should hide the existence of the custom header from downstream clients, add the following lines
  somewhere near the top of the `vcl_deliver` section:
  
```
   if (resp.http.Vary) {
     set resp.http.Vary = regsub(resp.http.Vary, "X-AligentVary", "Cookie");
   }
```

Usage
-----
*Example**: To vary the varnish cache on a customer's group, you can add an observer to the `customer_session_init`
event. Note, the `customer_session_init` event seems to always be triggered when a customer's group changes, and may be more reliable than observing the login or logout events:

In your module's `config.xml`:
```
            <customer_session_init>
                <observers>
                    <my_module_customer_session_init>
                        <type>model</type>
                        <class>yakima_vip/customer_observer</class>
                        <method>customerSessionInit</method>
                    </my_module_customer_session_init>
                </observers>
            </customer_session_init>
```

In your observer model:
```
   public function customerSessionInit(Varien_Event_Observer $observer)
    {
        /** @var Mage_Customer_Model_Session $customerSession */
        $customerSession = $observer->getCustomerSession();

        $groupId = $customerSession->getCustomerGroupId();
        $group     = Mage::getModel('customer/group')->load($groupId);
        $groupCode = $group->getCustomerGroupCode();

        $varyKeys = Mage::getSingleton('aligent_varycookie/keys');

        $varyKeys->setKey('customer_group', $groupCode);
    }
```


Requirements
------------
- PHP >= 5.2.0
- Mage_Core
- Varnish
- ...

Compatibility
-------------
- Magento >= 1.4

Installation Instructions
-------------------------
1. Install the extension via composer.
2. Make the necessary modifications to the Varnish vcl as described in the *Description* section.

Uninstallation
--------------
1. Remove the extension via composer.
2. Remove the custom Varnish rules.

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/aligent/Aligent_VaryCookie/issues).

Developer
---------
Luke Mills
[http://www.aligent.com.au](http://www.aligent.com.au)

Licence
-------
All Rights Reserved.

Copyright
---------
(c) 2015 Aligent
