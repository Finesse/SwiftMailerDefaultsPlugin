# [Swift Mailer](https://swiftmailer.symfony.com/) Defaults Plugin

[![Latest Stable Version](https://poser.pugx.org/finesse/swiftmailer-defaults-plugin/v/stable)](https://packagist.org/packages/finesse/swiftmailer-defaults-plugin)
[![Total Downloads](https://poser.pugx.org/finesse/swiftmailer-defaults-plugin/downloads)](https://packagist.org/packages/finesse/swiftmailer-defaults-plugin)
[![Build Status](https://php-eye.com/badge/finesse/swiftmailer-defaults-plugin/tested.svg)](https://travis-ci.org/FinesseRus/SwiftMailerDefaultsPlugin)
[![Coverage Status](https://coveralls.io/repos/github/FinesseRus/SwiftMailerDefaultsPlugin/badge.svg?branch=master)](https://coveralls.io/github/FinesseRus/SwiftMailerDefaultsPlugin?branch=master)
[![Dependency Status](https://www.versioneye.com/php/finesse:swiftmailer-defaults-plugin/badge)](https://www.versioneye.com/php/finesse:swiftmailer-defaults-plugin)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c0423fb6-bfb0-47a4-8a0c-eaae3e400634/mini.png)](https://insight.sensiolabs.com/projects/c0423fb6-bfb0-47a4-8a0c-eaae3e400634)

This plugin adds a possibility to set default properties for the sent Messages 
(default from address, reply to, subject and so on).

```php
// Set up a Mailer
$transport = new Swift_SmtpTransport();
$mailer = new Swift_Mailer($transport);
$mailer->registerPlugin(new Finesse\SwiftMailerDefaultsPlugin\SwiftMailerDefaultsPlugin([
    'from' => ['johndoe@example.com' => 'John Doe'],
    'replyTo' => 'jackdoe@example.com'
]));

// Use the Mailer many times
$mailer->send(
    (new Swift_Message())
        ->setTo('bjohnson@example.com', 'Bill Johnson')
        ->setSubject('Hi')
        ->setBody('This is awesome, I don\'t need to specify the from address!')
);
```


## How to install

### Using [composer](https://getcomposer.org)

Run in a console

```bash
composer require finesse/swiftmailer-defaults-plugin
```


## How to use

Create and register a plugin instance when you setup a `Swift_Mailer` instance.

```php
use Finesse\SwiftMailerDefaultsPlugin\SwiftMailerDefaultsPlugin;
use Swift_Mailer;
use Swift_SmtpTransport;

// Setup an emails sending transport
$transport = new Swift_SmtpTransport();

// Create a plugin instance
$defaultsPlugin = new SwiftMailerDefaultsPlugin(/* default properties */);

// Assemble them with a mailer
$mailer = new Swift_Mailer($transport);
$mailer->registerPlugin($defaultsPlugin);
```

For [Symfony](https://github.com/symfony/swiftmailer-bundle) you can register plugin this way:

```yaml
services:
    # Swift Mailer plugins
    app.swiftmailer.defaults_plugin:
        class: Finesse\SwiftMailerDefaultsPlugin\SwiftMailerDefaultsPlugin
        tags:
              - { name: swiftmailer.default.plugin }
        arguments:
              $defaults:
                  from:
                      johndoe@example.com: John Doe
                  replyTo: jackdoe@example.com
     
```

When you need to send an email, just send it without specifying the parameters you set to the plugin instance.

```php
use Swift_Message;

$message = new Swift_Message();
$mailer->send($message);
```

If you specify, the specified parameters will override the default properties.

### __constructor

You can pass to the constructor all the properties that you can set to a `Swift_Mime_SimpleMessage` instance using the 
`set...` methods. For example:

```php
$defaultsPlugin = new SwiftMailerDefaultsPlugin([
    'from' => 'johndoe@example.com',
    'subject' => 'Notification'
]);
```

The array keys are the names of the properties that are the `Swift_Mime_SimpleMessage` methods names without the `set` 
word and with the lowercase first letter. For example, the `body` property corresponds to the `setBody` method, 
`readReceiptTo` to `setReadReceiptTo` and so on.

The array values are the first and the only arguments for the corresponding methods. Properties with the `null` value 
are ignored.

### setDefault

Sets a default value for a property.

```php
$defaultsPlugin->setDefault('sender', 'chasy@example.com', 'Chasy');
```

The first argument is the property name (see [__constructor](#__constructor) for reference). The rest arguments are the 
corresponding method arguments.

### unsetDefault

Removes a default value

```php
$defaultsPlugin->unsetDefault('sender');
```

The only argument is the property name (see [__constructor](#__constructor) for reference).


## Versions compatibility

The project follows the [Semantic Versioning](http://semver.org).


## License

MIT. See [the LICENSE](LICENSE) file for details.
