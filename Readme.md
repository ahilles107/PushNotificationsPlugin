Push Notifications
==================

What You can do with this plugin?

 * Define applications (usualy different end applications or publications)
 * Create notifications:
  * Choose application
  * Set title
  * Set content
  * Set publish date
 * Create notification from article edit screen
  * Choose article type field used for notification content.
 * List notifications
 * Create application and set service handler for it.

Service handlers are classes called when notification is created. Plugin pass notification object to it. Service handler is responsible for talking with push notifications provider api and scheduling it there. Plugin by default have service handler for OneSignal.com API (free service). Application can have enabled many Service handlers - all of them will be called when notification will be created.
