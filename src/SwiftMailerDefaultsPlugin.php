<?php

namespace Finesse\SwiftMailerDefaultsPlugin;

/**
 * Plugin for SwiftMailer that adds an ability to set default Message properties (from, etc.).
 *
 * @author Surgie Finesse <finesserus@gmail.com>
 */
class SwiftMailerDefaultsPlugin implements \Swift_Events_EventListener, \Swift_Events_SendListener
{
    /**
     * @var array Default message properties. The indexes are the properties names in CapitalCase (From, ReplyTo, ...).
     *     The values are lists of the arguments of the corresponding Swift_Mime_SimpleMessage methods (setFrom,
     *     setReplyTo, ...).
     */
    protected $defaults = [];

    /**
     * @var array The original values of a sent message properties which were replaced by the default properties. They
     *     are kept to restore the original message properties after the sending.
     */
    protected $originalValues = [];

    /**
     * @param array $defaults Default Message properties. See the readme for more information.
     */
    public function __construct(array $defaults = [])
    {
        foreach ($defaults as $property => $value) {
            if ($value !== null) {
                $this->setDefault($property, $value);
            }
        }
    }

    /**
     * Set a default property value.
     *
     * @param string $property The property name. See the readme for more information.
     * @param array ...$arguments The list of argument for the Swift_Mime_SimpleMessage property setter
     */
    public function setDefault($property, ...$arguments)
    {
        $this->defaults[ucfirst($property)] = $arguments;
    }

    /**
     * Removes the default property value.
     *
     * @param string $property The property name. See the readme for more information.
     */
    public function unsetDefault($property)
    {
        unset($this->defaults[ucfirst($property)]);
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSendPerformed(\Swift_Events_SendEvent $event)
    {
        $message = $event->getMessage();

        foreach ($this->defaults as $property => $arguments) {
            $originalValue = $message->{'get'.$property}();
            if ($originalValue === null || $originalValue === '' || $originalValue === []) {
                $this->originalValues[$property] = $originalValue;
                $message->{'set'.$property}(...$arguments);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function sendPerformed(\Swift_Events_SendEvent $event)
    {
        $message = $event->getMessage();

        foreach ($this->originalValues as $property => $originalValue) {
            $message->{'set'.$property}($originalValue);
        }

        $this->originalValues = [];
    }
}
