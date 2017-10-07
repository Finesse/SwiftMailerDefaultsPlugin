<?php

namespace Finesse\SwiftMailerDefaultsPlugin;

/**
 * Plugin for SwiftMailer that adds an ability to set default Message properties (from, etc.).
 *
 * @author Surgie
 */
class SwiftMailerDefaultsPlugin implements \Swift_Events_EventListener, \Swift_Events_SendListener
{
    /**
     * @var array|null Default from address and name
     */
    protected $defaultFrom;

    /**
     * @var \Swift_Mime_SimpleMessage|null Message that was received before sending and is not modified. It is kept to
     *     restore the original message after sending.
     */
    protected $originalMessage;

    /**
     * @param array $defaults Default Message properties. Possible keys and their values:
     *  - from - From address. Has the same value format as the first argument of the \Swift_Message::setFrom method.
     */
    public function __construct(array $defaults = [])
    {
        if (isset($defaults['from'])) {
            $this->setDefaultFrom($defaults['from']);
        }
    }

    /**
     * Sets default from address and name. The arguments has the same format as the \Swift_Mime_SimpleMessage::setFrom
     * method. You can pass null to reset the default from address.
     *
     * @return self Itself
     * @see \Swift_Mime_SimpleMessage::setFrom More amount the arguments format
     */
    public function setDefaultFrom($addresses, string $name = null): self
    {
        if (isset($addresses)) {
            if (!is_array($addresses) && isset($name)) {
                $addresses = array($addresses => $name);
            }

            $this->defaultFrom = (array)$addresses;
        } else {
            $this->defaultFrom = null;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSendPerformed(\Swift_Events_SendEvent $event)
    {
        $message = $event->getMessage();
        $this->originalMessage = clone $message;

        if ($this->defaultFrom !== null && empty($message->getFrom())) {
            $message->setFrom($this->defaultFrom);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function sendPerformed(\Swift_Events_SendEvent $event)
    {
        $message = $event->getMessage();

        $message->setFrom($this->originalMessage->getFrom());

        $this->originalMessage = null;
    }
}
