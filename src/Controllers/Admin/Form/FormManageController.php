<?php
namespace App\Controllers\Admin\Form;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\Exception\NotFoundException;
use \Illuminate\Database\Capsule\Manager as DB;

use App\Entities\Admin;
use App\Daos\Admin\FormDAO;

use App\Controllers\Controller;

use App\Models\FormGroup;
use App\Models\FormItem;

class FormManageController extends Controller
{
	// 	private $view;
	// 	private $db;

	// // コンストラクタ
	// public function __construct(ContainerInterface $container)
	// {
	// 			$this->view = $container->get("view");
	// 			$this->db = $container->get("db");
	// }

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
            // $con = DB::connection();
            // $con->beginTransaction();

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
                $form_item = new FormItem;
                $form_item->form_group_id = $fid;
                $form_item->label_name = $form_item['label_name'];
                $form_item->schema_name = $form_item['schema_name'];
                $form_item->input_type = intVal($form_item['input_type']);
                $form_item->placeholder = $form_item['placeholder'];
                $form_item->is_required = intVal($form_item['is_required']);
                $form_item->choice_value = $form_item['choice_value'];
                $form_item->validate = $form_item['validate'];
                $form_item->save();
            }

            // $con->commit();
        } catch (\Exception $e) {
            // $con->rollBack();
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
                            $formDao = new FormDAO($this->db);
                            $group = $formDao->findGroupByPk($fg_id);
                            if (!$group) {
                                $msg = "Not found form";
                            }

                            $items = $formDao->findItemByGid($fg_id);
                            if (!$items) {
                                $msg = "Not found form items";
                            }

                    } catch(PDOException $ex) {
                            var_dump($ex->getMessage());
                    } finally {
                            // DB切断。
                            $this->db = null;
                    }

                    $response = $this->view->render($response,
                                                "admin/forms/edit.html",
                                                [
                                                        'form_group' => $group,
                                                        'form_items' => $items,
                                                        'msg' => $msg,
                                                        'itemtypes' => FormDAO::ITEM_TYPE,
                                                        'valid_types' => FormDAO::VALIDATE_TYPE,
                                                ]);
                    return $response;
            }

            $params = $request->getParsedBody();
            $form_group =[
                'id' => $fg_id,
                'name' => isset($params["group_name"]) ? $params["group_name"]: '',
                'base_uri' => isset($params["group_name"]) ? $params["group_name"]: '',
            ];

            $form_items = [];
            for($i = 0; $i < count($params["label_name"]); $i++) {
                $form_items[] = [
                    'label_name' => isset($params["label_name"][$i]) ? $params["label_name"][$i]: '',
                    'schema_name' => isset($params["schema_name"][$i]) ? $params["schema_name"][$i]: '',
                    'input_type' => isset($params["input_type"][$i]) ? $params["input_type"][$i]: '',
                    'placeholder' => isset($params["placeholder"][$i]) ? $params["placeholder"][$i]: '',
                    'is_required' => (intVal($params["is_required"][$i]) == 1) ? 1: 0,
                    'choice_value' => isset($params["choice_value"][$i]) ? $params["choice_value"][$i] : '',
                    'validate' => isset($params["validate"][$i]) ? $params["validate"][$i]: '',
                ];
            }

            try {
                    $formDao = new FormDAO($this->db);
                    $res = $formDao->updateForm($form_group, $form_items);

                    if (!$res) {
                            $msg = "Failed update form";
                    }
            } catch(PDOException $ex) {
                    var_dump($ex->getMessage());
            } finally {
                    // DB切断。
                    $this->db = null;
            }

            return $response->withHeader('Location', '/admin/forms')->withStatus(302);

            // $response = $this->view->render($response, "admin/forms/edit.html",
            // 						[
            // 							'user' => [
            // 								'username' => $username,
            // 								'role' => $role,
            // 							],
            // 							'msg' => $msg,
            // 							'roles' => UserDAO::ROLES
            // 						]);
            // return $response;
    }
}
