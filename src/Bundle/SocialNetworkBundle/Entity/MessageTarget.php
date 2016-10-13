<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Entity;

/**
 * MessageTarget Class.
 */
class MessageTarget
{
    /**
     * @var bool
     */
    private $has_read = false;

    /**
     * @var int
     */
    private $id;

    /**
     * @var \DatetimeInterface
     */
    private $created_at;

    /**
     * @var \DatetimeInterface
     */
    private $updated_at;

    /**
     * @var Message
     */
    private $message;

    /**
     * @var User
     */
    private $target;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set has_read.
     *
     * @param bool $hasRead
     */
    public function setHasRead($hasRead)
    {
        $this->has_read = $hasRead;
    }

    /**
     * Get has_read.
     *
     * @return bool
     */
    public function getHasRead()
    {
        return $this->has_read;
    }

    /**
     * Set created_at.
     *
     * @param \DatetimeInterface $createdAt
     */
    public function setCreatedAt(\DatetimeInterface $createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at.
     *
     * @return \DatetimeInterface
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at.
     *
     * @param \DatetimeInterface $updatedAt
     */
    public function setUpdatedAt(\DatetimeInterface $updatedAt)
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * Get updated_at.
     *
     * @return \DatetimeInterface
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set message.
     *
     * @param Message $message
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get message.
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set target.
     *
     * @param User $target
     */
    public function setTarget(User $target)
    {
        $this->target = $target;
    }

    /**
     * Get target.
     *
     * @return User
     */
    public function getTarget()
    {
        return $this->target;
    }
}
