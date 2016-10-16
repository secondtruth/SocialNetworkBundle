<?php


namespace Kiboko\Component\SocialNetwork\Model;

interface MessageRecipientInterface extends TimestampableInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @param bool $wasRead
     */
    public function setWasRead(bool $wasRead);

    /**
     * @return bool
     */
    public function wasRead(): bool;

    /**
     * @param MessageInterface $message
     */
    public function setMessage(MessageInterface $message);

    /**
     * @return MessageInterface
     */
    public function getMessage(): MessageInterface;

    /**
     * Set target.
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * Get target.
     *
     * @return UserInterface
     */
    public function getUser(): UserInterface;
}
