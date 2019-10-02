# magento 1.9.*
Sameday extension plugin

In order to install the extension you have to follow the next steps :  

Copy SamedayCourier folder and paste to /app/code/community. 

Copy the SamedayCourier_Shipping.xml registration file and paste to /app/etc/modules.  

Copy folder samedaycourier_shipping and paste to /app/design/adminhtml/default.

Copy the samedaycourier-php-sdk and paste to /lib folder. 

After copying all the files, you need to clean the cache for magento to be able indexing the new installed module.
For do this step you can simply remove the cache folder you found on /var folder.

If everything went well, now you can see the module on the list. 

Go to System->Configuration->Advanced and look for SamedayCourier_Shipping. If you see the module in the list, select "Enable" and press
"Save Config" button.

Usage: 

Before start to use the extension first you have to set up the configuration.
Go to System->Configuration and choose Shipping Methods. Select Sameday Courier and complete the form and then
press Save Config. Complete <User> and <Password> fields with the right credentials. For get the credentials, please contact us at
clientsupport@sameday.ro .

Import Pickup-Points and Services 

Go Sameday Courier->Show Pickup Points and press Refresh Pickup Points button.

Go Sameday Courier->Show Services and press Refresh Service button. After import the services assigned to your account, 
you are able to edit each of them. 
On the checkout page, your customer can select one of the sameday delivery services according to the availability set in the service menu.

If one of the customers has chosen as a delivery method one of the services offered by sameday in the menu of that order, will appear the button "Generate Awb"

Press "Generate Awb" complete the form and press "Save" button. Now the awb is generated. You can add new parcel in the same awb, show the awb history, show the awb
in .pdf format or delete the awb.


For further information, contact us at software@sameday.ro !


