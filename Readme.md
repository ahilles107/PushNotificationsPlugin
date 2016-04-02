Push Notifications in Newscoop
==============================

Schedule and publish push notifications directly from Newscoop backend (also from article edit screen).

### Features

 * Define applications
 * Create notifications
 * List notifications

### Support different providers with PushHnadlers system

PushHandlers are classes called when notification is created. Plugin pass notification object to it. PushHandler is responsible for talking with push notifications provider api and scheduling it there. Plugin by default have PushHandler for OneSignal.com API (free service).

### Commands
#### Install the plugin

``` bash
$ php application/console plugins:install "newscoop/send-feedback-plugin" --env=prod
$ php application/console assets:install public/
```

#### Update the plugin

``` bash
$ php application/console plugins:update "newscoop/send-feedback-plugin" --env=prod
```

#### Remove the plugin

``` bash
$ php application/console plugins:remove "newscoop/send-feedback-plugin" --env=prod
```
