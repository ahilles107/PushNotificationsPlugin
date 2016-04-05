Push Notifications in Newscoop
==============================

Schedule and publish push notifications directly from Newscoop backend (also from article edit screen).

### Features

 * Define applications
 * Create notifications
  * Directly from article edit screen
  * From separate admin side UI
  * Send notifications for many applications
  * Collect recipients number for notifications (if provider returns it)
 * List notifications
  * Accept/Reject/Edit/Duplicate scheduled notifications
 * Permissions support (plugin settings/schedule/publish)

### Support different providers with PushHandlers system

PushHandlers are classes called when notification is created. Plugin pass notification object to it. PushHandler is responsible for talking with push notifications provider api and scheduling it there. Plugin by default have PushHandler for OneSignal.com API (free service).

### Custom switches in notifications

Plugin is build in a way to work with every Push Notifications provider - so you will don't find in it any ***Provider specific*** features. But it doesn't mean that you need resign from your Provider special features (like for example subscribers segments). To support it you will need to use ```Custom Switches``` feature. In plugin settings you can define own custom switches which will be visible in ```Notification create forms``` (as a checkboxes) and available for Push Handler as part of Notification object.

### Commands
#### Install the plugin

``` bash
$ php application/console plugins:install "ahs/pushnotifications-plugin-bundle" --env=prod
$ php application/console assets:install public/
```

#### Update the plugin

``` bash
$ php application/console plugins:update "ahs/pushnotifications-plugin-bundle" --env=prod
```

#### Remove the plugin

``` bash
$ php application/console plugins:remove "ahs/pushnotifications-plugin-bundle" --env=prod
```

### LICENSING

This code is free for personal use under the GPL-V3 license, for Commercial and other uses please contact me for a Commercial license. Details in LICENSE file.
