<?php
namespace App\Entities\Admin;

class FormGroup
{
  private $id;
  private $form_group_id;
  private $label_name;
  private $schema_name;
  private $input_type;
  private $is_required;
  private $choice_value;
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
    return $this->form_group_id;
  }

  public function setFormGroupId(?int $form_group_id): void
  {
    $this->form_group_id = $form_group_id;
  }

  //  label name
  public function getLabelName(): ?string
  {
    return $this->label_name;
  }

  public function setLabelName(?string $label_name): void
  {
    $this->label_name = $label_name;
  }

  // schema name
  public function getSchemaName(): ?string
  {
    return $this->schema_name;
  }

  public function setSchemaName(?string $schema_name): void
  {
    $this->schema_name = $schema_name;
  }

  // input type
  public function getInputType(): ?int
  {
    return $this->input_type;
  }

  public function setInputType(?int $input_type): void
  {
    $this->input_type = $input_type;
  }

  // is_required
    public function getIsRequired(): ?bool
  {
    return $this->is_required;
  }

  public function setIsRequired(?bool $is_required): void
  {
    $this->is_required = $is_required;
  }

  // choice value
  public function getChoiceValue(): ?string
  {
    return $this->choice_value;
  }
  public function setChoiceValue(?string $choice_value): void
  {
    $this->choice_value = $choice_value;
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
