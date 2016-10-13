<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Message.
 */
class Message
{
    const DIRNAME = 'messenger';

    /**
     * @var bool
     */
    private $allowAnswer = true;

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * Set file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        $this->filename .= '#CHANGE#';
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Return url of file.
     *
     * @return string
     */
    public function displayFile()
    {
        return '/'.$this->getUploadDir().$this->filename;
    }

    /**
     * Upload directory.
     *
     * @return string
     */
    public function getUploadDir()
    {
        $id = $this->getParent() ? $this->getParent()->getId() : $this->getId();

        return 'uploads/'.self::DIRNAME.'/'.$id.'/';
    }

    /**
     * Get absolut upload directory.
     *
     * @return string
     */
    public function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * Generate new filename if file is uploaded.
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $this->removeFile();
            $this->filename = $this->file->getClientOriginalName();
        }
    }

    /**
     * Move the uploaded file.
     */
    public function upload()
    {
        if (null !== $this->file) {
            $this->file->move($this->getUploadRootDir(), $this->filename);
        }
    }

    /**
     * Remove uploaded files.
     */
    public function removeFile()
    {
        if ($this->filename !== '#CHANGE#' && $this->filename !== '') {
            if (strstr($this->filename, '#CHANGE#')) {
                unlink($this->getUploadRootDir().substr($this->filename, 0, -strlen('#CHANGE#')));
            } else {
                unlink($this->getUploadRootDir().$this->filename);
            }
        }
    }

    /**
     * Remove uploaded files.
     */
    public function removeUpload()
    {
        $this->removeFile();
    }

    /***************************************************************************
     *                             GENERATED CODE                              *
     **************************************************************************/
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var text
     */
    private $content;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $typeOfMessage;

    /**
     * @var datetime
     */
    private $created_at;

    /**
     * @var datetime
     */
    private $updated_at;

    /**
     * @var MessageTarget
     */
    private $target;

    /**
     * @var Message
     */
    private $children;

    /**
     * @var Message
     */
    private $parent;

    /**
     * @var User
     */
    private $sender;

    public function __construct()
    {
        $this->target = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set subject.
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set content.
     *
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content.
     *
     * @return text
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set allowAnswer.
     *
     * @param bool $allowAnswer
     */
    public function setAllowAnswer($allowAnswer)
    {
        $this->allowAnswer = $allowAnswer;
    }

    /**
     * Get allowAnswer.
     *
     * @return bool
     */
    public function getAllowAnswer()
    {
        return $this->allowAnswer;
    }

    /**
     * Set filename.
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set typeOfMessage.
     *
     * @param string $typeOfMessage
     */
    public function setTypeOfMessage($typeOfMessage)
    {
        $this->typeOfMessage = $typeOfMessage;
    }

    /**
     * Get typeOfMessage.
     *
     * @return string
     */
    public function getTypeOfMessage()
    {
        return $this->typeOfMessage;
    }

    /**
     * Set created_at.
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at.
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at.
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * Get updated_at.
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Add target.
     *
     * @param MessageTarget $target
     */
    public function addMessageTarget(MessageTarget $target)
    {
        $this->target[] = $target;
    }

    /**
     * Get target.
     *
     * @return Collection
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Add children.
     *
     * @param Message $children
     */
    public function addMessage(Message $children)
    {
        $this->children[] = $children;
    }

    /**
     * Get children.
     *
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent.
     *
     * @param Message $parent
     */
    public function setParent(Message $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent.
     *
     * @return Message
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set sender.
     *
     * @param User $sender
     */
    public function setSender(User $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Get sender.
     *
     * @return User
     */
    public function getSender()
    {
        return $this->sender;
    }
}
