<?php
namespace App\Daos\Admin;

use PDO;
use App\Entities\Admin\FormGroup;
use App\Entities\Admin\FormItem;

class FormDAO
{
    private $db;

    /*
    * 0=text
    * 1=number
    * 2=tel
    * 3=email
    * 4=password
    * 5=select
    * 6=radio
    * 7=checkbox
    * 8=date
    */
    public const ITEM_TYPE = ['テキスト', 'テキストエリア', '電話番号', 'Eメール', 'パスワード', 'セレクトボックス', 'ラジオボタン', 'チェックボックス', '日付'];
    public const VALIDATE_TYPE = [
        '' => 'なし',
        '[ァ-ヴー\s　]+' => 'フリガナ',
        '[\w\d_-]+@[\w\d_-]+\.[\w\d._-]+' => 'Eメール',
        // '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$' => 'Eメール',
        '^\d{10}$|^\d{11}$' => '電話番号（ハイフンなし）',
        '^\d{3,4}-\d{3,4}-\d{4}$' => '電話番号（ハイフンあり）',
        '^\d{10}$|^\d{11}$|^\d{3,4}-\d{3,4}-\d{4}$' => '電話番号（ハイフンあり・なし両方）',
        '^\d{3}$|^\d{5}$|^\d{7}$' => '郵便番号（ハイフンなし）',
        '^\d{3}[-]\d{4}$|^\d{3}[-]\d{2}$|^\d{3}$' => '郵便番号（ハイフンあり）',
        '^\d{3}[-]\d{4}$|^\d{3}[-]\d{2}$|^\d{3}$|^\d{5}$|^\d{7}$' => '郵便番号（ハイフンあり・なし両方）',
        '^[0-9]+$' => '全て数値（半角）',
        '^[0-9０-９]+$' => '全て数値（全角,半角）',
        '^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$' => 'クレジットカード'
    ];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findGroupByPk(int $id, int $is_active = 1): ?FormGroup

    {
        $sql = "select * from form_groups where id = :id and is_active = :is_active";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":is_active", $is_active, PDO::PARAM_INT);
        $res = $stmt->execute();

        $fg = new FormGroup();
        if ($res && $row = $stmt->fetch()) {
            // $id = $intVal(row["id"]);
            $name = $row["name"];
            $uri = $row["base_uri"];

            $fg->setName($name);
            $fg->setUri($uri);
        }

        return $fg;
    }

    public function findItemByGid(int $gid): array
    {
        $sql = "select * from form_items where form_group_id = :form_group_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":form_group_id", $gid, PDO::PARAM_INT);
        $res = $stmt->execute();

        $items = [];
        if ($res) {
            while ($row = $stmt->fetch()) {
                $id = intVal($row["id"]);
                $labelname = $row["label_name"];
                $schemaname = $row["schema_name"];
                $inputtype = intVal($row["input_type"]);
                $placeholder = $row["placeholder"];
                $isrequired = intVal($row["is_required"]) == 1;
                $choicevalue = $row["choice_value"];
                $validate = $row["validate"];

                $fi = new FormItem();
                $fi->setId($id);
                $fi->setLabelName($labelname);
                $fi->setSchemaName($schemaname);
                $fi->setInputType($inputtype);
                $fi->setPlaceholder($placeholder);
                $fi->setIsRequired($isrequired);
                $fi->setChoiceValue($choicevalue);
                $fi->setValidate($validate);

                $items[$id] = $fi;
            }
        }

        return $items;
    }

    public function findAllForms(): ?array
    {
        $sql = "select * from form_groups where is_active = :is_active order by id asc";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":is_active", 1, PDO::PARAM_INT);
        $res = $stmt->execute();

        $forms = [];
        if ($res) {
            while ($row = $stmt->fetch()) {
                $id = intVal($row["id"]);
                $name = $row["name"];
                $uri = $row["base_uri"];

                $fg = new FormGroup();
                $fg->setId($id);
                $fg->setName($name);
                $fg->setUri($uri);

                $forms[$id] = $fg;
            }
        }

        return $forms;
    }

    public function addForm(array $form_group, array $form_item): bool
    {
        $this->db->beginTransaction();

        if (!$this->db->inTransaction()) {
          throw new Exception("deactive transaction", 1);
        }

        try {
          $sql = "insert into form_groups (name, base_uri, is_active) VALUES (:name, :base_uri, :is_active)";
          $stmt = $this->db->prepare($sql);
          $stmt->bindValue(":name", $form_group['name'], PDO::PARAM_STR);
          $stmt->bindValue(":base_uri", $form_group['base_uri'], PDO::PARAM_STR);
          $stmt->bindValue(":is_active", 1, PDO::PARAM_INT);
          $res = $stmt->execute();

          $gid = $this->db->lastInsertId();

          $sql = "insert into form_items (form_group_id, label_name, schema_name, input_type, is_required, choice_value, validate) VALUES (:form_group_id, :label_name, :schema_name, :input_type, :is_required, :choice_value, :validate)";
          $stmt = $this->db->prepare($sql);
          $stmt->bindValue(":form_group_id", intVal($gid), PDO::PARAM_INT);
          $stmt->bindValue(":label_name", $form_item['label_name'], PDO::PARAM_STR);
          $stmt->bindValue(":schema_name", $form_item['schema_name'], PDO::PARAM_STR);
          $stmt->bindValue(":input_type", intVal($form_item['input_type']), PDO::PARAM_INT);
          $stmt->bindValue(":placeholder", $form_item['placeholder'], PDO::PARAM_STR);
          $stmt->bindValue(":is_required", intVal($form_item['is_required']), PDO::PARAM_INT);
          $stmt->bindValue(":choice_value", $form_item['choice_value'], PDO::PARAM_STR);
          $stmt->bindValue(":validate", $form_item['validate'], PDO::PARAM_STR);
          $res = $stmt->execute();

          $this->db->commit();
        } catch (PDOException $e) {
          $this->db->rollback();
          throw $e;
        }

        return $res;
    }

    public function updateForm(array $form_group, array $form_items): bool
    {
        $this->db->beginTransaction();

        if (!$this->db->inTransaction()) {
          throw new Exception("deactive transaction", 1);
        }

        $gid = intVal($form_group['id']);

        try {
            // group 更新
            $sql = "update form_groups set name = :name, base_uri = :base_uri where id = :id and is_active = :is_active";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":name", $form_group['name'], PDO::PARAM_STR);
            $stmt->bindValue(":base_uri", $form_group['base_uri'], PDO::PARAM_STR);
            $stmt->bindValue(":id", $gid, PDO::PARAM_INT);
            $stmt->bindValue(":is_active", 1, PDO::PARAM_INT);
            $res = $stmt->execute();

            // items 削除
            $sql = "delete from form_items where form_group_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id", $gid, PDO::PARAM_INT);
            $res = $stmt->execute();

            // items 再登録
            $sql = "insert into form_items (form_group_id, label_name, schema_name, input_type, placeholder, is_required, choice_value, validate) VALUES (:form_group_id, :label_name, :schema_name, :input_type, :placeholder, :is_required, :choice_value, :validate)";
            $stmt = $this->db->prepare($sql);

            foreach ($form_items as $form_item) {
                $stmt->execute(array(
                    ':form_group_id' => $gid,
                    ':label_name' => $form_item['label_name'],
                    ':schema_name' => $form_item['schema_name'],
                    ':input_type' => intVal($form_item['input_type']),
                    ':placeholder' => $form_item['placeholder'],
                    ':is_required' => intVal($form_item['is_required']),
                    ':choice_value' => $form_item['choice_value'],
                    ':validate' => $form_item['validate'],
                ));
            }

            $res = $this->db->commit();
        } catch (PDOException $e) {
          $this->db->rollback();
          throw $e;
        }

        return $res;
    }

    public function findByUri(string $uri, int $is_active = 1): ?int
    {
        $sql = "select * from form_groups where base_uri = :uri and is_active = :is_active";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":uri", $uri, PDO::PARAM_STR);
        $stmt->bindValue(":is_active", $is_active, PDO::PARAM_INT);
        $res = $stmt->execute();

        $id = 0;
        if ($res && $row = $stmt->fetch()) {
            $id = intVal($row["id"]);
        }

        return $id;
    }
}
