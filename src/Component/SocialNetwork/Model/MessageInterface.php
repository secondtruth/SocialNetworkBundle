<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Component\SocialNetwork\Model;

use Doctrine\Common\Collections\Collection;

interface MessageInterface extends TimestampableInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Set subject.
     *
     * @param string $subject
     */
    public function setSubject(string $subject);

    /**
     * Get subject.
     *
     * @return string
     */
    public function getSubject(): string;

    /**
     * Set content.
     *
     * @param string $content
     */
    public function setContent(string $content);

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Set allowAnswer.
     *
     * @param bool $allowAnswer
     */
    public function setAllowAnswers(bool $allowAnswer);

    /**
     * Get allowAnswer.
     *
     * @return bool
     */
    public function allowAnswers(): bool;

    /**
     * Set messageType.
     *
     * @param string $messageType
     */
    public function setMessageType(string $messageType);

    /**
     * Get messageType.
     *
     * @return string
     */
    public function getMessageType(): string;

    /**
     * Get message recipients.
     *
     * @return MessageRecipientInterface[]|Collection
     */
    public function getRecipients(): Collection;

    /**
     * Set message recipients.
     *
     * @param MessageRecipientInterface[]|Collection $recipients
     */
    public function setRecipients(Collection $recipients);

    /**
     * Add message recipient.
     *
     * @param MessageRecipientInterface $recipient
     */
    public function addRecipient(MessageRecipientInterface $recipient);

    /**
     * Add message recipient.
     *
     * @param MessageRecipientInterface $recipient
     */
    public function removeRecipient(MessageRecipientInterface $recipient);

    /**
     * Add children.
     *
     * @param MessageInterface[]|Collection $children
     */
    public function setChildren(Collection $children);

    /**
     * Get children.
     *
     * @return MessageInterface[]|Collection
     */
    public function getChildren(): Collection;

    /**
     * Add children.
     *
     * @param MessageInterface $children
     */
    public function addChild(MessageInterface $children);

    /**
     * Add children.
     *
     * @param MessageInterface $children
     */
    public function removeChild(MessageInterface $children);

    /**
     * Set parent.
     *
     * @param MessageInterface $parent
     */
    public function setParent(MessageInterface $parent);

    /**
     * Get parent.
     *
     * @return MessageInterface
     */
    public function getParent(): MessageInterface;

    /**
     * Set sender.
     *
     * @param UserInterface $sender
     */
    public function setSender(UserInterface $sender);

    /**
     * Get sender.
     *
     * @return UserInterface
     */
    public function getSender(): UserInterface;

    /**
     * Set file.
     *
     * @param AttachmentInterface[]|Collection $attachments
     */
    public function setAttachments(Collection $attachments);

    /**
     * Set file.
     *
     * @return AttachmentInterface[]|Collection
     */
    public function getAttachments(): Collection;

    /**
     * Set file.
     *
     * @param AttachmentInterface $attachment
     */
    public function addAttachment(AttachmentInterface $attachment);

    /**
     * Set file.
     *
     * @param AttachmentInterface $attachment
     */
    public function removeAttachment(AttachmentInterface $attachment);
}
