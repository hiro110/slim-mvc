<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use \Illuminate\Database\Capsule\Manager as DB;

use App\Controllers\BaseController;

use App\Models\FormItem;
use App\Models\FormGroup;
use App\Models\Submit;
use App\Models\SubmitValue;

class FormController extends BaseController
{
    public function getIndex(Request $request, Response $response, array $args): Response
    {
        $response = $this->view->render($response, "form/index.html");
        return $response;
    }

    public function getForm(Request $request, Response $response, array $args): Response
    {
        $uri = $args['uri'];
        $items = "";
        $msg = "";

        try {
            $fg = $request->getAttribute('form_group');

            $tmps = FormItem::where('form_group_id', $fg->id)
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
                    'input_type' => $tmp->input_type,
                    'placeholder' => $tmp->placeholder,
                    'is_required' => $tmp->is_required,
                    'choice_value' => explode("\n", str_replace(array("\r\n","\r","\n"), "\n", $tmp->choice_value)),
                    'validate' => $tmp->validate,
                ];
            }

        } catch(PDOException $ex) {
            var_dump($ex->getMessage());
        }

        $response = $this->view->render($response,
                    "form/form.html",
                    [
                        'uri' => $uri,
                        'form_items' => $items,
                        'msg' => $msg,
                    ]);
        return $response;
    }

    public function postConfirm(Request $request, Response $response, array $args): Response
    {
        $uri = $args['uri'];
        $items = "";
        $msg = "";
        $params = $request->getParsedBody();

        try {
            $fg = $request->getAttribute('form_group');

            $tmps = FormItem::where('form_group_id', $fg->id)
                ->get();
            if (!$tmps) {
                $msg = "Not found form items";
            }

            $items = [];
            foreach ($tmps as $tmp) {
                $items[] = [
                    'id' => $tmp->id,
                    'labelname' => $tmp->label_name,
                    'schemaname' => $tmp->schema_name,
                    'value' => gettype($params[$tmp->schema_name]) == 'array' ? implode(',', $params[$tmp->schema_name]): $params[$tmp->schema_name],
                ];
            }
        } catch(PDOException $ex) {
            var_dump($ex->getMessage());
        }

        $response = $this->view->render($response,
                "form/confirm.html",
                [
                    'uri' => $uri,
                    'form_items' => $items,
                    'msg' => $msg,
                ]);
        return $response;
    }

    public function postComplete(Request $request, Response $response, array $args): Response
    {
        $uri = $args['uri'];
        $params = $request->getParsedBody();

        try {
            $con = DB::connection();
            $con->beginTransaction();

            $fg = $request->getAttribute('form_group');

            $id = DB::table('submits')
                ->insertGetId([
                    'form_group_id' => $fg->id,
                    'is_active' => 1
                ]);

            $vals = [];
            foreach ($params as $key => $value) {
                switch (gettype($value)) {
                    case 'string':
                        $vals[] = [
                            'submit_id' => intVal($id),
                            'label_name' => '',
                            'schema_name' => $key,
                            'string' => $value,
                            'num' => 0,
                            'datetime' => null,
                        ];
                        break;

                    case 'integer':
                        $vals[] = [
                            'submit_id' => intVal($id),
                            'label_name' => '',
                            'schema_name' => $key,
                            'string' => '',
                            'num' => $value,
                            'datetime' => null,
                        ];
                        break;

                    default:
                        break;
                }
            }

            $res = DB::table('submit_values')
                ->insert($vals);
            $con->commit();

        } catch(PDOException $ex) {
            var_dump($ex->getMessage());
            $con->rollBack();
        }

        $response = $this->view->render($response, "form/complete.html");
        return $response;
    }
}
