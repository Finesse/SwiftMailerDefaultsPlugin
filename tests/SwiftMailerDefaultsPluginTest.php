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
     * Tests default from
     */
    public function testFromReceiving()
    {
        $plugin = new SwiftMailerDefaultsPlugin(['from' => 'foo@mail.com']);

        // Ordinary case
        $message = new \Swift_Message();
        $message->setBody('Hello, world');
        $event = $this->createSendEvent($message);
        $plugin->beforeSendPerformed($event);
        $this->assertEquals(['foo@mail.com' => null], $event->getMessage()->getFrom());
        $this->assertEquals('Hello, world', $event->getMessage()->getBody());
        $plugin->sendPerformed($event);
        $this->assertEquals([], $event->getMessage()->getFrom());
        $this->assertEquals('Hello, world', $event->getMessage()->getBody());

        // What if the Message has a from address?
        $message = new \Swift_Message();
        $message->setFrom(['bar@mail.com' => 'Laguna Bar']);
        $event = $this->createSendEvent($message);
        $plugin->beforeSendPerformed($event);
        $this->assertEquals(['bar@mail.com' => 'Laguna Bar'], $event->getMessage()->getFrom());
        $plugin->sendPerformed($event);
        $this->assertEquals(['bar@mail.com' => 'Laguna Bar'], $event->getMessage()->getFrom());

        // What if no default from is used?
        $plugin->setDefaultFrom(null);
        $message = new \Swift_Message();
        $event = $this->createSendEvent($message);
        $plugin->beforeSendPerformed($event);
        $this->assertEquals([], $event->getMessage()->getFrom());
        $plugin->sendPerformed($event);
        $this->assertEquals([], $event->getMessage()->getFrom());
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
}
