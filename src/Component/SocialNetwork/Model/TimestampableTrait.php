<?php


namespace Kiboko\Component\SocialNetwork\Model;

trait TimestampableTrait
{
    /**
     * @var \DatetimeInterface
     */
    private $created;

    /**
     * @var \DatetimeInterface
     */
    private $updated;

    /**
     * @param \DateTimeInterface $created
     */
    public function setCreated(\DateTimeInterface $created)
    {
        $this->created = $created;
    }

    /**
     * Get created_at.
     *
     * @return \DateTimeInterface
     */
    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @param \DateTimeInterface $updatedAt
     */
    public function setUpdated(\DateTimeInterface $updatedAt)
    {
        $this->updated = $updatedAt;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getUpdated(): \DateTimeInterface
    {
        return $this->updated;
    }
}
