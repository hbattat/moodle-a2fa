A2FA or Etouffee as I like to call it!
======================================
A2FA is multi-factor authentication plugin that uses time-based tokens generated every 60 seconds in Google Authenticator app.
A2FA Stands for **A**nother **T**wo-**F**actor **A**uthentication

This plugin requires another small plugin (profile field plugin) get it from here [link to afaqr]

The field is to add a QR code for the user to be able to sync Google Authenticator with the a2fa system.


##Installation:
To install these plugins use moodle plugin installation interface to upload a2fa.zip and follow installation steps (use *Authentication method* as plugin type).

Or upload the a2fa folder to /auth/ directory and follow installation steps after you visit your site's main page.

Then install the afaqr plugin by uploading the afaqr.zip using the plugin installation interface (Choose *profile field* as a plugin type)


* Once these plugins are installed, go to ***Site Administration > Users > Accounts > User profile fields*** 

* Add an ***a2fa QR code input*** with the shortname ***a2fasecret*** (This name is being used in the code and has to match for the system to work)

* Make this field ***Visible to user***

Now go to ***Site Administration > Plugins > Authentication > Manage authentication*** and enable ***A2FA***

Once the authentication method is enable go to the user that you want to force using this auth method and edit their authentication method.


