Push Notifications
==================

What You can do with this plugin?

 * Define applications (usually different end applications or publications)
 * Create notifications:
  * Choose application
  * Set title
  * Set content
  * Set publish date
 * Create notification from article edit screen
  * Choose article type field used for notification content.
 * List notifications
 * Create application and set service handler for it.

PushHandlers are classes called when notification is created. Plugin pass notification object to it. PushHandler is responsible for talking with push notifications provider api and scheduling it there. Plugin by default have PushHandlers for OneSignal.com API (free service).
