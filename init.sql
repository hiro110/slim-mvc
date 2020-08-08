CREATE USER app3 IDENTIFIED BY '<password>';
CREATE DATABASE app3 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';
GRANT ALL ON app3.* TO 'app3' identified by '<password>';


/*
  * 管理サイトユーザ
  * username:ユーザ名
  * password:パスワード
  * role:権限
  * is_active:有効・無効フラグ
*/
CREATE TABLE `users` (
  `id` int AUTO_INCREMENT,
  `username` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` int NOT NULL DEFAULT 1,
  `is_active` tinyint NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `users` (`username`, `password`, `role`) VALUES
('user', '$2y$10$Fn26scxVobuHaHz2sNtYDeP3x703QAsVjCLj4pdQdMpjGq78ma/MC', 0);
-- user / user@123

/*
  * フォーム管理
  * form_name:フォーム名
  * uri_path:フォームのURLパス
  * is_active:有効・無効フラグ
*/

drop table form_groups;
CREATE TABLE `form_groups` (
  `id` int AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `base_uri` varchar(255) NOT NULL UNIQUE,
  `is_active` tinyint NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

/*
  * フォーム定義
  *
  * form_group_id:フォームNp
  * label_name:フォームのラベル
  * schema_name:form_valuesのカラム名に利用
  * input_type:フォームのinput_typeに利用 ex) 1=text, 2=number, 3=tel, 4=email, 5=password, 7=radio, 8=checkbox 9= date
  * is_required:必須項目フラグ
  * choice_value:選択肢が必要な場合に選択項目を入力（radio, checkbox, select）
  * validate:バリデーション
    正規表現はこちら
  https://qiita.com/fubarworld2/items/9da655df4d6d69750c06
  ex) email /^\S+@\S+\.\S+$/
*/

drop table form_items;
CREATE TABLE `form_items` (
  `id` int AUTO_INCREMENT,
  `form_group_id` int NOT NULL,
  `label_name` varchar(255) NOT NULL DEFAULT '',
  `schema_name` varchar(255) NOT NULL DEFAULT '',
  `input_type` int NOT NULL DEFAULT 0,
  `is_required` boolean NOT NULL DEFAULT false,
  `choice_value` text NOT NULL,
  `validate` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

/*
  * input type 詳細
  * input_type_id:フォームのinput_typeに利用
  * type:int, form_valuesの型に利用 ex)1=string, 2=num, 3=datetime, 4=boolean
  * validate:バリデーション正規表現
  正規表現はこちら
  https://qiita.com/fubarworld2/items/9da655df4d6d69750c06
*/

-- drop table `item_details`;
-- CREATE TABLE `item_details` (
--   `id` int AUTO_INCREMENT,
--   `input_type_id` int NOT NULL DEFAULT 0,
--   `type` int  NOT NULL DEFAULT 0,
--   `validate`

-- );

-- INSERT INTO `form_items` (`label_name`, `schema_name`, `input_type`, `type`, `required`, `only_int`, `choice_name`, `choice_value`, `format_with`) VALUES
-- ('名前', 'name', 'text', 1, false, false, '', '', ''),
-- ('メールアドレス', 'email', 'email', 1, false, false, '', '', '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/'),
-- ('電話番号', 'tel', 'text', 2, false, true, '', '', '/^[0-9]+$/'),
-- ('性別', 'sex', 'radio', 1, false, false, '男性,女性', 'male,female', ''),
-- ('興味', 'interest', 'checkbox', 1, false, false, '技術、環境、経済', 'technology,environment,buissiness', ''),
-- ('備考', 'remarks', 'textarea', 1, false, false, '', '', '');

CREATE TABLE `form_submits` (
  `id` int AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `form_values` (
  `id` int AUTO_INCREMENT,
  `submit_id` int,
  `label_name` varchar(255),
  `colmun_name` varchar(255),
  `text` varchar(255),
  `textarea` varchar(510),
  `int` int,
  `date` date,
  `bool` boolean,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
