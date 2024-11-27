# WARNING: Deprecated!
Sending message to MS Teams using their Webhook API is being deprecated by microsoft so setting up "new connectors" this way isn't possible anymore. We personally switched to sending emails to teams channel because their new implementation is a pita to implement, so hereby this package is not maintained anymore.

# Microsoft Teams Logger
Send errors/notices/warnings etc to Microsoft Teams,
using this Class which implements the Prs/Log/LoggerInterface.

For more information about the logger interface or log levels see [their github](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md).

## Setup webhook
Before you can use this you need to setup a webhook in a Microsoft Teams channel.

1. Goto [https://teams.microsoft.com/](https://teams.microsoft.com/) and select a team.
2. Right mouse click on the desired channel and click "Connectors".
3. Search for "Incoming Webhook" and click "configure".
4. Provide a name and image.
5. Click on "Create".
6. A new field will now appear with the new webhook url in it which you can copy-paste.

## Example
```php
<?php

$slack = new \Devorto\Logger\MicrosoftTeams(
	'<paste-webhook-url-here>',
	'My Test App',
	'https://test.example.com' // Optional app url.
);

// Use one of the available log level methods:
$slack->critical('Help something went critical.');

// You can also drop an Exception in here.
$slack->critical(new Exception('Test'));
```
Produces this in Microsoft Teams:
![Example](example/example.png)
