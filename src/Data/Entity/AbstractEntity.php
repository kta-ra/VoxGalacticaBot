<?php 

namespace KtaraDev\VoxGalacticaBot\Data\Entity;

abstract class AbstractEntity {
    /** @var int */
    private $id;

    public function __construct() {}
    

    public function getId() : int
    {
        return $this->id;
    }

    public function setId(int $id) : AbstractEntity
    {
        if (is_null($this->id)) $this->id = $id;
        return $this;
    }

    public function getArray() : array
    {
        return get_object_vars($this);
    }

    public function getJson() : string
    {
        return json_encode($this->getArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
