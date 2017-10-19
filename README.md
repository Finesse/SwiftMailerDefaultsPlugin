# [Swift Mailer](https://swiftmailer.symfony.com/) Defaults Plugin

This plugin adds a possibility to set default parameters for sent Messages (for example, default from address).

```php
// Set up a Mailer
$transport = new \Swift_SmtpTransport();
$mailer = new \Swift_Mailer($transport);
$mailer->registerPlugin(new \Finesse\SwiftMailerDefaultsPlugin\SwiftMailerDefaultsPlugin([
    'from' => ['johndoe@example.com' => 'John Doe'],
    'replyTo' => 'jackdoe@example.com'
]));

// Use the Mailer many times
$mailer->send(
    (new \Swift_Message())
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

When you setup a `\Swift_Mailer` instance, create and register the plugin.

```php
use Finesse\SwiftMailerDefaultsPlugin\SwiftMailerDefaultsPlugin;

// Setup you email send transport
$transport = new \Swift_SmtpTransport();

// Create a plugin instance
$defaultsPlugin = new SwiftMailerDefaultsPlugin(/* default properties */);

// Assemble them
$mailer = new \Swift_Mailer($transport);
$mailer->registerPlugin($defaultsPlugin);
```

When you need to send a email, just send it without specifying the default properties you set in the plugin instance.

```php
$message = new \Swift_Message();
$mailer->send($message);
```

If you specify, the specified properties will override the default properties.

### __constructor

You can pass to the constructor all the properties which you can set to a `Swift_Mime_SimpleMessage` instance using the 
`set...` methods. For example:

```php
$defaultsPlugin = new SwiftMailerDefaultsPlugin([
    'from' => 'johndoe@example.com',
    'subject' => 'Notification'
]);
```

The array indexes are the name of the properties which are the `Swift_Mime_SimpleMessage` methods names without `set` and
with lowercase first letter. For example, the `body` property corresponds to the `setBody` method, `readReceiptTo` to
`setReadReceiptTo` and so on.

The array values are the first and the only arguments for the corresponding methods. Properties with the `null` value 
are discarded.

### setDefault

Sets a default value for a property.

```php
$defaultsPlugin->setDefault('sender', 'chasy@example.com', 'Chasy');
```

The first argument is the property name (see `__constructor` reference). The rest arguments are the corresponding method
arguments.

### unsetDefault

Removes a default value

```php
$defaultsPlugin->unsetDefault('sender');
```

The only argument is the property name (see `__constructor` reference).


## Versions compatibility

The project follows the [Semantic Versioning](http://semver.org).


## License

MIT. See [the LICENSE](LICENSE) file for details.
