<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

use App\Models\FormItem;
use App\Models\FormGroup;
use App\Models\Submit;
use App\Models\SubmitValue;

class FormController
{
    private $view;
    private $db;
    private $logger;

    public function __construct(ContainerInterface $container)
    {
            $this->view = $container->get("view");
            $this->db = $container->get("db");
            // $this->logger = $container->get("logger");
    }

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
                    'labelname' => $tmp->label_name,
                    'schemaname' => $tmp->schema_name,
                    'inputtype' => $tmp->input_type,
                    'placeholder' => $tmp->placeholder,
                    'isrequired' => $tmp->is_required,
                    'choicevalue' => explode("\n", str_replace(array("\r\n","\r","\n"), "\n", $tmp->choice_value)),
                    'validate' => $tmp->validate,
                ];
            }

        } catch(PDOException $ex) {
            var_dump($ex->getMessage());
        } finally {
            $this->db = null;
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
                    ];

                    $_SESSION['form'][$tmp->schema_name] = $params[$tmp->schema_name];
            }
        } catch(PDOException $ex) {
            var_dump($ex->getMessage());
        } finally {
            $this->db = null;
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

        DB::beginTransaction();
        try {
            $fg = $request->getAttribute('form_group');

            // $submitDao = new SubmitDAO($this->db);
            // $res = $submitDao->addSubmit($fg->id, $params);

            $id = DB::table('submits')->insertGetId([
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

                    case 'integer':
                        $vals[] = [
                            'submit_id' => intVal($id),
                            'label_name' => '',
                            'schema_name' => $key,
                            'string' => '',
                            'num' => $value,
                            'datetime' => null,
                        ];

                    default:
                        break;
                }
            }

            $res = DB::table('submit_values')->insert([
                $vals
            ]);
            DB::commit();

        } catch(PDOException $ex) {
            var_dump($ex->getMessage());
            DB::rollback();
        }

        unset($_SESSION['form']);
        $response = $this->view->render($response, "form/complete.html");
        return $response;
    }

    public function getTest(Request $request, Response $response, array $args): Response
    {
        $fg = FormGroup::where('base_uri', 'test1')
                ->where('is_active', 1)
                ->first();
        if (!$fg) {
            $msg = "Not found form";
            return $response->withHeader('Location', '/')->withStatus(302);
        }
        var_dump($fg);

        $tmps = FormItem::where('form_group_id', $fg->id)
            ->get();
        if (!$tmps) {
            $msg = "Not found form items";
        }

        var_dump($tmps);
        $response = $this->view->render($response, "form/test.html", ["user" => $fg->items()]);

        return $response;
    }
}
