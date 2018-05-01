<?php

namespace Finesse\SwiftMailerDefaultsPlugin\Tests;

use Finesse\SwiftMailerDefaultsPlugin\SwiftMailerDefaultsPlugin;
use PHPUnit\Framework\TestCase;

/**
 * Test for the SwiftMailerDefaultsPlugin class.
 *
 * @author Surgie
 */
class SwiftMailerDefaultsPluginTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // Early SwiftMailer 4 versions don't support Composer autoload
        if (!class_exists('\Swift_Message')) {
            require __DIR__.'/../vendor/swiftmailer/swiftmailer/lib/swift_required.php';
        }
    }

    /**
     * Tests passing default values to Message
     */
    public function testDefaultsToMessage()
    {
        $plugin = new SwiftMailerDefaultsPlugin([
            'subject' => 'Hello',
            'description' => 'Hello, world',
            'sender' => ['sender@test.com' => 'John the Sender'],
            'from' => 'from@test.com',
            'replyTo' => [
                'reply1@test.com',
                'reply2@test.com' => 'A replier'
            ]
        ]);

        // A message without the properties above
        $message = new \Swift_Message();
        $message->setBody('Hello, world, I am a message');
        $message->setTo('to@test.com');
        $event = $this->createSendEvent($message);
        $plugin->beforeSendPerformed($event);
        $this->assertEquals('Hello', $event->getMessage()->getSubject());
        $this->assertEquals('Hello, world', $event->getMessage()->getDescription());
        $this->assertEquals('Hello, world, I am a message', $event->getMessage()->getBody());
        $this->assertEquals(['from@test.com' => null], $event->getMessage()->getFrom());
        $this->assertEquals(['sender@test.com' => 'John the Sender'], $event->getMessage()->getSender());
        $this->assertEquals(['to@test.com' => null], $event->getMessage()->getTo());
        $this->assertEquals(['reply1@test.com' => null, 'reply2@test.com' => 'A replier'], $event->getMessage()->getReplyTo());
        $plugin->sendPerformed($event);
        $this->assertEquals('', $message->getSubject());
        $this->assertNull($event->getMessage()->getDescription());
        $this->assertEquals('Hello, world, I am a message', $message->getBody());
        $this->assertEquals([], $message->getFrom());
        $this->assertEquals([], $message->getSender());
        $this->assertEquals(['to@test.com' => null], $message->getTo());
        $this->assertEquals([], $message->getReplyTo());

        // A message with the properties above
        $message = new \Swift_Message();
        $message->setSubject('Bonjour');
        $message->setDescription('0'); // '0' is an empty value according to PHP
        $message->setSender('sender2@test.com');
        $event = $this->createSendEvent($message);
        $plugin->beforeSendPerformed($event);
        $this->assertEquals('Bonjour', $event->getMessage()->getSubject());
        $this->assertEquals('0', $event->getMessage()->getDescription());
        $this->assertEquals(['from@test.com' => null], $event->getMessage()->getFrom());
        $this->assertEquals(['sender2@test.com' => null], $event->getMessage()->getSender());
        $this->assertEquals(['reply1@test.com' => null, 'reply2@test.com' => 'A replier'], $event->getMessage()->getReplyTo());
        $plugin->sendPerformed($event);
        $this->assertEquals('Bonjour', $message->getSubject());
        $this->assertEquals('0', $event->getMessage()->getDescription());
        $this->assertEquals([], $message->getFrom());
        $this->assertEquals(['sender2@test.com' => null], $message->getSender());
        $this->assertEquals([], $message->getReplyTo());
    }

    /**
     * Tests what happens to message properties without default values
     */
    public function testNoDefaults()
    {
        $plugin = new SwiftMailerDefaultsPlugin();

        $message = new \Swift_Message();
        $message->setTo('to@test.com');
        $event = $this->createSendEvent($message);
        $plugin->beforeSendPerformed($event);
        $this->assertEquals([], $event->getMessage()->getFrom());
        $this->assertEquals(['to@test.com' => null], $event->getMessage()->getTo());
        $plugin->sendPerformed($event);
        $this->assertEquals([], $message->getFrom());
        $this->assertEquals(['to@test.com' => null], $message->getTo());
    }

    /**
     * Tests changing default properties values
     */
    public function testChangeDefaults()
    {
        $plugin = new SwiftMailerDefaultsPlugin([
            'replyTo' => 'reply@to.me'
        ]);

        $message = new \Swift_Message();
        $event = $this->createSendEvent($message);
        $plugin->beforeSendPerformed($event);
        $this->assertEquals(['reply@to.me' => null], $event->getMessage()->getReplyTo());
        $plugin->sendPerformed($event);

        $plugin->setDefault('replyTo', 'other@address', 'Other person');
        $message = new \Swift_Message();
        $event = $this->createSendEvent($message);
        $plugin->beforeSendPerformed($event);
        $this->assertEquals(['other@address' => 'Other person'], $event->getMessage()->getReplyTo());
        $plugin->sendPerformed($event);

        $plugin->unsetDefault('replyTo');
        $message = new \Swift_Message();
        $event = $this->createSendEvent($message);
        $plugin->beforeSendPerformed($event);
        $this->assertEquals(null, $event->getMessage()->getReplyTo());
        $plugin->sendPerformed($event);
    }

    /**
     * Creates message send mock event.
     *
     * @param \Swift_Mime_SimpleMessage $message
     * @return \Swift_Events_SendEvent
     */
    protected function createSendEvent(\Swift_Mime_SimpleMessage $message)
    {
        /** @var \Swift_Events_SendEvent|\Mockery\MockInterface $event */
        $event = \Mockery::mock(\Swift_Events_SendEvent::class);
        $event->shouldIgnoreMissing();
        $event->shouldReceive('getMessage')
            ->zeroOrMoreTimes()
            ->andReturn($message);

        return $event;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        \Mockery::close();
    }
}
