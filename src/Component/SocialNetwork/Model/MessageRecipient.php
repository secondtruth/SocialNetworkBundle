<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Component\SocialNetwork\Model;

/**
 * MessageRecipient Class.
 */
class MessageRecipient implements MessageRecipientInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var bool
     */
    private $wasRead;

    /**
     * @var MessageInterface
     */
    private $message;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * MessageRecipient constructor.
     */
    public function __construct()
    {
        $this->wasRead = false;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param bool $wasRead
     */
    public function setWasRead(bool $wasRead)
    {
        $this->wasRead = $wasRead;
    }

    /**
     * @return bool
     */
    public function wasRead(): bool
    {
        return $this->wasRead;
    }

    /**
     * @param MessageInterface $message
     */
    public function setMessage(MessageInterface $message)
    {
        $this->message = $message;
    }

    /**
     * @return MessageInterface
     */
    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    /**
     * Set target.
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Get target.
     *
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
