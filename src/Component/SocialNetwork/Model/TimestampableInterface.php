<?php


namespace Kiboko\Component\SocialNetwork\Model;

interface TimestampableInterface
{
    /**
     * Set created.
     *
     * @param \DateTimeInterface $created
     */
    public function setCreated(\DateTimeInterface $created);

    /**
     * Get created.
     *
     * @return \DateTimeInterface
     */
    public function getCreated(): \DateTimeInterface;

    /**
     * Set updated.
     *
     * @param \DateTimeInterface $updated
     */
    public function setUpdated(\DateTimeInterface $updated);

    /**
     * Get updated date.
     *
     * @return \DateTimeInterface
     */
    public function getUpdated(): \DateTimeInterface;
}
