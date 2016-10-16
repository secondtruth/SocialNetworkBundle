<?php

namespace Kiboko\Component\SocialNetwork\Model;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface AttachmentInterface extends TimestampableInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getFilename(): string;

    /**
     * @param string $filename
     */
    public function setFilename(string $filename);

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @param string $path
     */
    public function setPath(string $path);

    /**
     * @return File
     */
    public function getFile(): File;

    /**
     * @param File $file
     */
    public function setFile(File $file);

    /**
     * @param UploadedFile $uploadedFile
     */
    public function uploadFile(UploadedFile $uploadedFile);
}
