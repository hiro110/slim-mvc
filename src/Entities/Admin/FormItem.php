<?php
namespace App\Entities\Admin;

class FormItem
{
  private $id;
  private $formgroupid;
  private $labelname;
  private $schemaname;
  private $inputtype;
  private $isrequired;
  private $choicevalue;
  private $validate;

  // id
  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(?int $id): void
  {
    $this->id = $id;
  }
  // form group id
  public function getFormGroupId(): ?id
  {
    return $this->formgroupid;
  }

  public function setFormGroupId(?int $formgroupid): void
  {
    $this->formgroupid = $formgroupid;
  }

  //  label name
  public function getLabelName(): ?string
  {
    return $this->labelname;
  }

  public function setLabelName(?string $labelname): void
  {
    $this->labelname = $labelname;
  }

  // schema name
  public function getSchemaName(): ?string
  {
    return $this->schemaname;
  }

  public function setSchemaName(?string $schemaname): void
  {
    $this->schemaname = $schemaname;
  }

  // input type
  public function getInputType(): ?int
  {
    return $this->inputtype;
  }

  public function setInputType(?int $inputtype): void
  {
    $this->inputtype = $inputtype;
  }

  // isrequired
    public function getIsRequired(): ?bool
  {
    return $this->isrequired;
  }

  public function setIsRequired(?bool $isrequired): void
  {
    $this->isrequired = $isrequired;
  }

  // choice value
  public function getChoiceValue(): ?string
  {
    return $this->choicevalue;
  }
  public function setChoiceValue(?string $choicevalue): void
  {
    $this->choicevalue = $choicevalue;
  }

  // validate
  public function getValidate(): ?string
  {
    return $this->validate;
  }

  public function setValidate(?string $validate): void
  {
    $this->validate = $validate;
  }
}
