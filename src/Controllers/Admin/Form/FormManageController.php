<?php
namespace App\Controllers\Admin\Form;

use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\Exception\NotFoundException;
use \Illuminate\Database\Capsule\Manager as DB;

use App\Controllers\BaseController;

use App\Models\FormGroup;
use App\Models\FormItem;

class FormManageController extends BaseController
{
    public function getforms(Request $request, Response $response, array $args): Response
    {
        try {
            $forms = FormGroup::where('is_active', 1)
                    ->get();

            if (!$forms) {
                $msg = "Failed add form";
            }
        } catch(PDOException $ex) {
                var_dump($ex->getMessage());
        }

        $response = $this->view->render($response,
                    "admin/forms/index.html",
                    [
                        'forms' => $forms,
                    ]);

        return $response;
    }

    public function mapformsAdd(Request $request, Response $response, array $args): Response
    {

        if ($request->getMethod() == "GET") {
            $response = $this->view->render($response,
                        "admin/forms/edit.html",
                        ['itemtypes' => FormItem::ITEM_TYPE, 'valid_types' => FormItem::VALIDATE_TYPE,]);
            return $response;
        }
        $msg = "";
        $params = $request->getParsedBody();
        $name = isset($params["group_name"]) ? $params["group_name"]: '';
        $base_uri = isset($params["group_uri"]) ? $params["group_uri"]: '';
        $publishing_start = isset($params["publishing_start"]) ? date('Y-m-d H:i:s', strtotime($params["publishing_start"])): date("Y-m-d H:i:s");
        $publishing_end = isset($params["publishing_end"]) ? date('Y-m-d H:i:s', strtotime($params["publishing_end"])): date("Y-m-d H:i:s");

        try {
            $con = DB::connection();
            $con->beginTransaction();

            $form_group = new FormGroup;
            $form_group->name = $name;
            $form_group->base_uri = $base_uri;
            $form_group->is_active = 1;
            $form_group->publishing_start = $publishing_start;
            $form_group->publishing_end = $publishing_end;
            $form_group->save();
            $fid = $form_group->id;

            $form_items = [];
            for($i = 0; $i < count($params["label_name"]); $i++) {
                $form_items[] = [
                    'label_name' => isset($params["label_name"][$i]) ? $params["label_name"][$i]: '',
                    'schema_name' => isset($params["schema_name"][$i]) ? $params["schema_name"][$i]: '',
                    'input_type' => isset($params["input_type"][$i]) ? $params["input_type"][$i]: '',
                    'placeholder' => isset($params["placeholder"][$i]) ? $params["placeholder"][$i]: '',
                    'is_required' => intVal($params["is_required"][$i]) == 1 ? 1: 0,
                    'choice_value' => isset($params["choice_value"][$i]) ? $params["choice_value"][$i] : '',
                    'validate' => isset($params["validate"][$i]) ? $params["validate"][$i]: '',
                ];
            }

            foreach ($form_items as $form_item) {
                $form_item = FormItem::create([
                    'form_group_id' => $fid,
                    'label_name' => $form_item['label_name'],
                    'schema_name' => $form_item['schema_name'],
                    'input_type' => intVal($form_item['input_type']),
                    'placeholder' => $form_item['placeholder'],
                    'is_required' => intVal($form_item['is_required']),
                    'choice_value' => $form_item['choice_value'],
                    'validate' => $form_item['validate']
                ]);
            }

            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();
            echo $e->getMessage();
        }

        $response = $this->view->render($response,
                    "admin/forms/edit.html",
                    [
                        'msg' => $msg,
                        'itemtypes' => FormItem::ITEM_TYPE,
                        'valid_types' => FormItem::VALIDATE_TYPE,
                    ]);
        return $response;
    }

    public function mapformsId(Request $request, Response $response, array $args): Response
    {
        $fg_id = intVal($args['id']);

        if ($request->getMethod() == "GET") {
            $msg = "";
            try {
                $group = FormGroup::where('is_active', 1)
                    ->find($fg_id);
                if (!$group) {
                    $msg = "Not found form";
                }

                $tmps = FormItem::where('form_group_id', $fg_id)
                    ->get();
                if (!$tmps) {
                    $msg = "Not found form items";
                }
                $items = [];
                foreach ($tmps as $tmp) {
                   $items[] = [
                       'id' => $tmp->id,
                       'label_name' => $tmp->label_name,
                       'schema_name' => $tmp->schema_name,
                       'input_type' => intVal($tmp->input_type),
                       'placeholder' => $tmp->placeholder,
                       'is_required' => intVal($tmp->is_required) == 1,
                       'choice_value' => $tmp->choice_value,
                       'validate' => $tmp->validate,
                   ];
                }
            } catch(PDOException $ex) {
                var_dump($ex->getMessage());
            }
            $response = $this->view->render($response,
                        "admin/forms/edit.html",
                        [
                            'form_group' => $group,
                            'form_items' => $items,
                            'msg' => $msg,
                            'item_types' => FormItem::ITEM_TYPE,
                            'valid_types' => FormItem::VALIDATE_TYPE,
                        ]);
            return $response;
        }

        $params = $request->getParsedBody();

        $name = isset($params["group_name"]) ? $params["group_name"]: '';
        $base_uri = isset($params["group_uri"]) ? $params["group_uri"]: '';
        $publishing_start = isset($params["publishing_start"]) ? date('Y-m-d H:i:s', strtotime($params["publishing_start"])): date("Y-m-d H:i:s");
        $publishing_end = isset($params["publishing_end"]) ? date('Y-m-d H:i:s', strtotime($params["publishing_end"])): date("Y-m-d H:i:s");

        try {
            $con = DB::connection();
            $con->beginTransaction();

            $update_res = FormGroup::where('id', $fg_id)
                    ->update([
                            'name' => $name,
                            'base_uri' => $base_uri,
                            'is_active' => 1,
                            'publishing_start' => $publishing_start,
                            'publishing_end' => $publishing_end
                    ]);
            $del_res = FormItem::where('form_group_id', $fg_id)->delete();

            $form_items = [];
            for($i = 0; $i < count($params["label_name"]); $i++) {
                $form_items[] = [
                    'label_name' => isset($params["label_name"][$i]) ? $params["label_name"][$i]: '',
                    'schema_name' => isset($params["schema_name"][$i]) ? $params["schema_name"][$i]: '',
                    'input_type' => isset($params["input_type"][$i]) ? $params["input_type"][$i]: '',
                    'placeholder' => isset($params["placeholder"][$i]) ? $params["placeholder"][$i]: '',
                    'is_required' => intVal($params["is_required"][$i]) == 1 ? 1: 0,
                    'choice_value' => isset($params["choice_value"][$i]) ? $params["choice_value"][$i] : '',
                    'validate' => isset($params["validate"][$i]) ? $params["validate"][$i]: '',
                ];
            }

            $data = [];
            foreach ($form_items as $form_item) {
                $data[] = [
                    'form_group_id' => $fg_id,
                    'label_name' => $form_item['label_name'],
                    'schema_name' => $form_item['schema_name'],
                    'input_type' => intVal($form_item['input_type']),
                    'placeholder' => $form_item['placeholder'],
                    'is_required' => intVal($form_item['is_required']),
                    'choice_value' => $form_item['choice_value'],
                    'validate' => $form_item['validate']
                ];
            }
            $fi = DB::table('form_items')
                ->insert($data);

            $con->commit();
        } catch(PDOException $ex) {
            $con->rollBack();
            var_dump($ex->getMessage());
        }
        var_dump($params);
        return $response->withHeader('Location', '/admin/forms')->withStatus(302);
    }
}
