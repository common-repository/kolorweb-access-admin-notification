=== KolorWeb Access Admin Notification: extreme rescue for unauthorized admin logins ===
Contributors: vincent06
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl.html
Tags: admin login notification, unauthorized admin logins, track admin login, email notify on admin login, login notification
Requires at least: 5.2
Tested up to: 6.7
Stable tag: 1.0.1

Extreme rescue for unauthorized admin logins.

== Description ==

**What sets this plugin apart?**

In a sea of admin login notification plugins, KolorWeb Access Admin Notification stands out for a few key reasons:

* **Simplicity**: This plugin is designed to be lightweight, clean, and simple to use in just 20kb (I challenge you to find something better).
* **Compatibility**: It is regularly updated to ensure compatibility with the latest WordPress versions.
* **Right checking**: Unlike many others, this plugin checks capabilities instead of roles, and this makes a really big big difference when an attacker modifies them by granting administrator privileges to roles that shouldn't have them.
* **Lightweight**: There are no options to manage and no information overload to store in the database.
* **Pratical & Quick**: One-click logout and password reset capability for unauthorized access directly from mail notification.

I created this plugin because I couldn't find any existing options that met my specific criteria. If you're looking for a no-nonsense solution that gets the job done without any extra frills, this is the plugin for you.

Specifically, if you have tried any of these plugins:

* [Simple Login Notification](https://wordpress.org/plugins/simple-login-notification/)
* [Email Notification on Login](https://wordpress.org/plugins/email-notification-on-login/)
* [Email notification on admin login](https://wordpress.org/plugins/email-notification-on-admin-login)
* [Kaya Login Notification](https://wordpress.org/plugins/kaya-login-notification/)

I think it is time to abandon them and move on to a better solution like this one.

**Protecting Your Privacy**

Your privacy is really really important, which is why KolorWeb Access Admin Notification is committed to safeguarding your data. This plugin does not collect or store any user information, set cookies, or connect to third-party sites. The only data it captures is related to admin-level logins, such as usernames, IP addresses, and user agents.
KolorWeb Access Admin Notification: extreme rescue for unauthorized admin logins is created and maintained by Vincenzo Casu, a seasoned WordPress developer with 20 years of experience.

**Updates**

If you don't find any updates, it means that everything is working correctly with the latest WordPress release. But if you have an idea to improve this plugin, write to me.

**Intro to the problem and my simple solution**

Every day I receive many emails of attempted access to the sites I manage. So I asked myself: "And if suddenly one attempt among the millions of those executed were to be successful, how could I know if not when it is already too late?"

I thought the only way to know is to track admin account logins.

If the login is successful, an email is sent containing the account data and the IP of origin. So as soon as you log in through the email and two links inside it, I can immediately disconnect the sessions of the compromised account, and also reset the password for that account, which will be notified by email with a second sending.

In short, a solution that could save the life of your site because it allows you to become aware that there is some backdoor on the site that allows unauthorized access.

This plugin sends an email notification for every access that is made by the website administrators. When a login is detected by a site administrator, the login time is stored and a notification is sent containing the details of the account that is logged in. If access is not authorized, through a link it is possible to disconnect the account from all devices, or disconnect the account from all devices that have logged in and at the same time reset the access password for that account. In this second case, a new notification is sent containing the new password.

== Installation ==

= From your WordPress Dashboard =

1. ✅ Click on "Plugins > Add New" in the sidebar
2. ✅ Search for "KolorWeb Access Admin Notification"
3. ✅ Activate KolorWeb Access Admin Notification from the Plugins page

= From wordpress.org =

1. ✅ Search for "KolorWeb Access Admin Notification"
2. ✅ Download the Plugin to your local computer
3. ✅ Upload the kolorweb-access-admin-notification directory to your "/wp-content/plugins/" directory using your favorite ftp/sftp/scp program
4. ✅ Activate KolorWeb Access Admin Notification from the Plugins page

= Once Activated =

The plugin will start sending emails for each admin account access. I recommend: Keep your eyes open.

= Requirements =
* ✅ PHP 7.2 or greater
* ✅ Wordpress 5.2 or above


== Frequently Asked Questions ==

= ❓ How to I access the plugin? =

✅ Once activated, the plugin will start automatically sending emails for each admin account access.

= ❓ Is this plugin enough on its own to make my site secure? =

✅ Believe me, I wish I could say yes, but we all know that security depends on a lot of factors, and that it absolutely cannot depend solely on software protections.

This plugin is not a security system, but only a possibility to limit the damage when you realize that unauthorized access has occurred.

So, once you have ascertained that there was something that did not work and that allowed access to an attacker, you must be quick to understand what could have opened the door of your site, before it is too late.

This plugin could be a valid help used perhaps in conjunction with other plugins that detect failed access attempts, so already in those cases we have notifications of attempted accesses and a notification of an access performed would only be proof that we need to take action, and quickly.

= ❓ I received an unauthorized access email. What I do? =

✅ If you have found that access is actually not attributable to you, the only thing you absolutely must do is click on the link received in the email to reset the password and force the logout of that account from all devices.

= ❓ I have reset my password but I do not receive the email, what do I do? =

✅ Follow this guide to learn how to reset your password in several ways: https://wordpress.org/support/article/resetting-your-password/

= ❓ I receive too many emails, how can I extend the sending times between one login notification and another? =

✅ Notifications are sent per account, so each user will have their own notification counter. For each login, the time between one notification and another is set at 15 minutes. This means that if the same account logs in 5 in less than 15 minutes, you will only receive one notification. And the next one only after 15 minutes have elapsed starting from the first one relating to the first access.

I have made available a filter that allows you to change the time that must pass between one notification login and another.

To change this time frame to 30 minutes, for example, you can use this snippet of code that you can paste at the end of the currently active theme's functions.php file:

add_filter( 'kolorweb_notify_interval', function( $interval ) { return 30; } );

If you want receive notifications for every single admin access, set $interval value to -1

add_filter( 'kolorweb_notify_interval', function( $interval ) { return -1; } );

= ❓ Where can I report bugs? =

✅ Report bugs and suggest ideas at:  https://wordpress.org/support/plugin/kolorweb-access-admin-notification/


== Screenshots ==

1. Notification Example


== Changelog ==

= 1.0.1 =

* ✅ First Code Refactoring
* Feature:
	* ✅ Added new control that sends notification if logged in from an administrator account while another session is already active for the same user.
* Feature:
	* ✅ Introduction of geolocation information of the IP used by the user during login.
	* ✅ Among the available information we have Continent, Region, Country, City.

= 1.0.0 =

Release date: 2022-05-27
