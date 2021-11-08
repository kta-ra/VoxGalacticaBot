<?php 

namespace KtaraDev\VoxGalacticaBot\Data\Entity;

class Article extends AbstractEntity {
    /** @var bool */
    private $isPosted = false;

    /** @var bool */
    private $isRus = false;


    /** @var int */
    private $dateCreated;

    /** @var int */
    private $dateUpdated;

    /** @var int */
    private $datePosted;


    /** @var string */
    private $contentTitle;

    /** @var string */
    private $contentDate;

    /** @var string */
    private $contentImage;

    /** @var string */
    private $contentText;

    /** @var int */
    private $contentLength;


    /** @var string */
    private $externalId;

    /** @var int */
    private $externalDateCreated;

    /** @var string */
    private $externalLink;


    public function getIsPosted() : bool
    {
        return $this->isPosted;
    }

    public function setIsPosted(bool $isPosted) : Article
    {
        $this->isPosted = $isPosted;
        return $this;
    }

    public function getIsRus() : bool
    {
        return $this->isRus;
    }

    public function setIsRus(bool $isRus) : Article
    {
        $this->isRus = $isRus;
        return $this;
    }

    public function getDateCreated() : int
    {
        return $this->dateCreated;
    }

    public function setDateCreated(int $dateCreated) : Article
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated() : int
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(int $dateUpdated) : Article
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getDatePosted() : int
    {
        return $this->datePosted;
    }

    public function setDatePosted(int $datePosted) : Article
    {
        $this->datePosted = $datePosted;
        return $this;
    }

    public function getContentTitle() : string
    {
        return $this->contentTitle;
    }

    public function setContentTitle(string $contentTitle) : Article
    {
        $this->contentTitle = $contentTitle;
        return $this;
    }

    public function getContentDate() : string
    {
        return $this->contentDate;
    }

    public function setContentDate(string $contentDate) : Article
    {
        $this->contentDate = $contentDate;
        return $this;
    }

    public function getContentImage() : string
    {
        return $this->contentImage;
    }

    public function setContentImage(string $contentImage) : Article
    {
        $this->contentImage = $contentImage;
        return $this;
    }

    public function getContentLength() : int
    {
        return $this->contentLength;
    }

    public function setContentLength(int $contentLength) : Article
    {
        $this->contentLength = $contentLength;
        return $this;
    }

    public function getExternalId() : string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId) : Article
    {
        $this->externalId = $externalId;
        return $this;
    }

    public function getExternalDateCreated() : int
    {
        return $this->externalDateCreated;
    }

    public function setExternalDateCreated(int $externalDateCreated) : Article
    {
        $this->externalDateCreated = $externalDateCreated;
        return $this;
    }

    public function getExternalLink() : string
    {
        return $this->externalLink;
    }

    public function setExternalLink(string $externalLink) : Article
    {
        $this->externalLink = $externalLink;
        return $this;
    }
}
