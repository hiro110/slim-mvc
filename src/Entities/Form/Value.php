<?php
namespace App\Entities;

class Value
{
    private $id;
    private $submit_id;
    private $label_name;
    private $schema_name;
    private $str;
    private $int;
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getSubmitId(): ?int
    {
        return $this->submit_id;
    }

    public function setSubmitId(?int $submit_id): void
    {
        $this->submit_id = $submit_id;
    }

    public function getLabelName(): ?string
    {
        return $this->label_name;
    }

    public function setLabelName(?string $label_name): void
    {
        $this->label_name = $label_name;
    }

    public function getSchemaName(): ?string
    {
        return $this->schema_name;
    }

    public function setSchemaName(?string $schema_name): void
    {
        $this->schema_name = $schema_name;
    }

    public function getStr(): ?string
    {
        return $this->str;
    }

    public function setStr(?string $str): void
    {
        $this->str = $str;
    }

    public function getInt(): ?int
    {
        return $this->int;
    }

    public function setNum(?int $int): void
    {
        $this->int = $int;
    }

    public function getDate(): ?date
    {
        return $this->date;
    }

    public function setDate(?date $date): void
    {
        $this->date = $date;
    }
}
