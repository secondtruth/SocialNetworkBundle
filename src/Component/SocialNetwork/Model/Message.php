<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Component\SocialNetwork\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Kiboko\Component\SocialNetwork\Model\AttachmentInterface;
use Kiboko\Component\SocialNetwork\Model\MessageInterface;
use Kiboko\Component\SocialNetwork\Model\MessageRecipientInterface;
use Kiboko\Component\SocialNetwork\Model\UserInterface;

/**
 * Message.
 */
class Message implements MessageInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $attachments;

    /**
     * @var string
     */
    private $messageType;

    /**
     * @var \DateTimeInterface
     */
    private $created;

    /**
     * @var \DateTimeInterface
     */
    private $updated;

    /**
     * @var MessageRecipient[]|Collection
     */
    private $recipients;

    /**
     * @var MessageInterface[]|Collection
     */
    private $children;

    /**
     * @var MessageInterface
     */
    private $parent;

    /**
     * @var UserInterface
     */
    private $sender;

    /**
     * @var bool
     */
    private $allowAnswer;

    /**
     * Message constructor.
     */
    public function __construct()
    {
        $this->allowAnswer = true;
        $this->recipients = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set subject.
     *
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get subject.
     *
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Set content.
     *
     * @param string $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param bool $allowAnswer
     */
    public function setAllowAnswers(bool $allowAnswer)
    {
        $this->allowAnswer = $allowAnswer;
    }

    /**
     * @return bool
     */
    public function allowAnswers(): bool
    {
        return $this->allowAnswer;
    }

    /**
     * @param string $messageType
     */
    public function setMessageType(string $messageType)
    {
        $this->messageType = $messageType;
    }

    /**
     * @return string
     */
    public function getMessageType(): string
    {
        return $this->messageType;
    }

    /**
     * @return MessageRecipient[]|Collection
     */
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    /**
     * @param MessageRecipient[]|Collection $recipients
     */
    public function setRecipients(Collection $recipients)
    {
        $this->recipients = $recipients;
    }

    /**
     * @param MessageRecipientInterface $recipient
     */
    public function addRecipient(MessageRecipientInterface $recipient)
    {
        $this->recipients->add($recipient);
    }

    /**
     * @param MessageInterface[]|Collection $children
     */
    public function setChildren(Collection $children)
    {
        $this->children->add($children);
    }

    /**
     * @return MessageInterface[]|Collection
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param MessageInterface $children
     */
    public function addChild(MessageInterface $children)
    {
        $this->children->add($children);
    }

    /**
     * @param MessageInterface $children
     */
    public function removeChild(MessageInterface $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * @param MessageInterface $parent
     */
    public function setParent(MessageInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return MessageInterface
     */
    public function getParent(): MessageInterface
    {
        return $this->parent;
    }

    /**
     * @param UserInterface $sender
     */
    public function setSender(UserInterface $sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return UserInterface
     */
    public function getSender(): UserInterface
    {
        return $this->sender;
    }

    /**
     * @param MessageRecipientInterface $recipient
     */
    public function removeRecipient(MessageRecipientInterface $recipient)
    {
        $this->recipients->removeElement($recipient);
    }

    /**
     * @param AttachmentInterface[]|Collection $attachments
     */
    public function setAttachments(Collection $attachments)
    {
        $this->attachments = $attachments;
    }

    /**
     * @return AttachmentInterface[]|Collection
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    /**
     * @param AttachmentInterface $attachment
     */
    public function addAttachment(AttachmentInterface $attachment)
    {
        $this->attachments->add($attachment);
    }

    /**
     * @param AttachmentInterface $attachment
     */
    public function removeAttachment(AttachmentInterface $attachment)
    {
        $this->attachments->removeElement($attachment);
    }
}
