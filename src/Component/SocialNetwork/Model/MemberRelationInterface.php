<?php

namespace Kiboko\Component\SocialNetwork\Model;

interface MemberRelationInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @param int $refusalCount
     */
    public function setRefusalCount(int $refusalCount);

    /**
     * @return int
     */
    public function getRefusalCount(): int;

    public function incrementRefusal();

    /**
     * @param string $status
     */
    public function setStatus(string $status);

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return UserInterface
     */
    public function getInitiator(): UserInterface;

    /**
     * @param UserInterface $initiator
     */
    public function setInitiator(UserInterface $initiator);

    /**
     * @return UserInterface
     */
    public function getRecipient(): UserInterface;

    /**
     * @param UserInterface $recipient
     */
    public function setRecipient(UserInterface $recipient);
}
