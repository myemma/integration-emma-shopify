INSTALLATION

This integration serves the purpose of synchronizing data between Shopify and Emma.  Download
and unzip the file at the URL below to your desktop.  Rename the folder from 
"integration-emma-shopify-master" to "emma-shopify".  Upload the "emma-shopify" folder to a 
server which supports PHP.

https://github.com/myemma/integration-emma-shopify/archive/master.zip

For the purpose of this documentation, replace the use of "sample.com" with the domain name 
of the server to which you uploaded the "emma-shopify" folder.  We'll assume the folder is
accessible at http://sample.com/emma-shopify.  This document describes how to sync with a 
test Shopify store.  The steps are the same for a "real" store.

Below are the steps on how to integrate this plugin with your Emma account and your Shopify store.

- Visit https://app.shopify.com/services/partners/auth/login, register as a partner.
- Goto 'testshops'
- Select a 'teststore' and get the store URL (e.g. http://bogan-llc3934.myshopify.com/)
- Go to http://sample.com/emma-shopify and paste the store URL 
- Enter your Account ID, Public API Key, and Private API Key which can be acquired from your 
Emma account by navigating to "Account & Billing > Account Settings > API Key".
- Select the desired Emma Group you would like to sync with Shopify.


Test your the synchronization by purchasing a product at the store URL.  Be sure to check the
box during checkout that verifies that you would like to receive email messages.

The Email ID used for purchasing will be added to the selected Emma groups.


CONFIGURING DEFAULT EMMA GROUPS
You can configure the default groups that will be available at
at http://sample.com/emma-shopify/config_groups.php. When an order is completed 
at the Shopify store, subscribed customers will be automatically added to the Emma Group(s) 
selected at the above URL.

END USER SUBSCRIBE / UNSUBSCRIBE
The end user is provided with an option to subscribe / unsubscribe after each purchase.
