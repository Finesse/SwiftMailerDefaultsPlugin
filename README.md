# [Swift Mailer](https://swiftmailer.symfony.com/) Defaults Plugin

This plugin adds a possibility to set default parameters for sent Messages (for example, default from address).

```php
// Set up a Mailer
$transport = new \Swift_SmtpTransport();
$mailer = new \Swift_Mailer($transport);
$mailer->registerPlugin(new \Finesse\SwiftMailerDefaultsPlugin\SwiftMailerDefaultsPlugin([
    'from' => ['johndoe@example.com' => 'John Doe']
]));

// Use the Mailer many times
$mailer->send(
    (new \Swift_Message())
        ->setTo('bjohnson@example.com', 'Bill Johnson')
        ->setSubject('Hi')
        ->setBody('This is awesome, I don't need to specify the from address!')
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

// Setup you mailing transport
$transport = new \Swift_SmtpTransport();

// Create a plugin instance
$defaultsPlugin = new SwiftMailerDefaultsPlugin(/* default properties */);

// Assemble it
$mailer = new \Swift_Mailer($transport);
$mailer->registerPlugin($defaultsPlugin);
```

When you need to send a email, just send it without specifying the default properties you set in the plugin instance.

```php
$message = new \Swift_Message();
$mailer->send($message);
```

If you specify, the specified properties will override the default properties.

### Supported properties

#### From address

```php
$defaultsPlugin = new SwiftMailerDefaultsPlugin([
    'from' => 'johndoe@example.com' // ['johndoe@example.com' => 'John Doe'], ['johndoe@example.com' => 'John Doe', 'jackdoe@example.com' => 'Jack Doe']
]);
```

The `from` value has the same format as the first argument of the `\Swift_Message::setFrom` method.

Or

```php
$defaultsPlugin->setFrom('johndoe@example.com', 'John Doe');
```

The arguments has the same format as the `\Swift_Message::setFrom` arguments.


## Versions compatibility

The project follows the [Semantic Versioning](http://semver.org).


## License

MIT. See [the LICENSE](LICENSE) file for details.
