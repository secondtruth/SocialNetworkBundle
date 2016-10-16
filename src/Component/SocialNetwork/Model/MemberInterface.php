<?php

namespace Kiboko\Component\SocialNetwork\Model;

use Doctrine\Common\Collections\Collection;

interface UserInterface extends TimestampableInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return AttachmentInterface
     */
    public function getAvatar(): AttachmentInterface;

    /**
     * @param AttachmentInterface $avatar
     */
    public function setAvatar(AttachmentInterface $avatar);

    /**
     * @return AttachmentInterface
     */
    public function getBanner(): AttachmentInterface;

    /**
     * @param AttachmentInterface $banner
     */
    public function setBanner(AttachmentInterface $banner);

    /**
     * @return MessageInterface[]|Collection
     */
    public function getMessages(): Collection;

    /**
     * @param MessageInterface[]|Collection $messages
     */
    public function setMessages(Collection $messages);

    /**
     * @param MessageInterface $message
     */
    public function addMessage(MessageInterface $message);

    /**
     * @param MessageInterface $message
     */
    public function removeMessage(MessageInterface $message);
}
