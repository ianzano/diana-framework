<?php
/**
 * Created by PhpStorm.
 * User: Antonio Ianzano
 * Date: 01.12.2017
 * Time: 15:32
 */

namespace Diana\Support;

use Diana\Support\Bag;
use Diana\Support\Obj;

class View extends Obj
{

    /**
     * The source file
     * @var string
     */
    private $file = null;

    /**
     * The template string
     * @var string
     */
    private $template = "";

    /**
     * The variables
     * @var Bag
     */
    private $vars;

    public function __construct($template = "", $vars = [])
    {
        $this->vars = new Bag($vars);
        $this->template = $template;
    }

    public function toString(): string
    {
        return $this->render();
    }

    public function parse()
    {
        $arg = func_get_args();
        $set = false;

        for ($i = 0; $i < func_num_args(); $i++) {
            if (!$set) {
                if (is_array($arg[$i]))
                    foreach ($arg[$i] as $k => $v) {
                        if (is_int($k)) {
                            $this->vars[$v] = null;
                        } else
                            $this->vars[$k] = $v;
                    } else {
                    $this->vars[$arg[$i]] = isset ($arg[$i + 1]) ? $arg[$i + 1] : NULL;
                    $set = !$set;
                }
            }
        }

        return $this;
    }

    public function __make($array)
    {
        $output = '';

        // if assoc
        if (array_keys($array) !== range(0, count($array) - 1))
            foreach ($array as $k => $v)
                $output .= '\'' . $k . '\'=>\'' . $v . '\',';
        else {
            $output = '[';
            foreach ($array as $arr)
                $output .= $this->__make($arr);
            $output .= ']';
        }

        return $output;
    }

    public function render()
    {
        // TODO: Return cached version if exists

        /*if ($this->file) {

            $root = Environment::fetch(Project::class)->getPath() . 'tmp/';
            $file = $root . $this->file . '.php';

            if (!file_exists($file)) {
                $dirs = explode("/", $file);
                $current = '/';
                array_shift($dirs);
                array_pop($dirs);

                foreach ($dirs as $dir) {
                    $current .= $dir . '/';
                    try {
                        mkdir($current);
                    } catch (\Exception $e) {
                    }
                }
            }

            $tmp = fopen($file, "w");
        } else*/
        $tmp = tmpfile();

        foreach ($this->vars as $key => $value)
            $$key = $value;

        // $view = preg_replace([
        //     sprintf('/(?<!\\\\)%s(.*)%s/', '\/\/', '\n'),
        //     '/\\\\\/\/\s*/',
        //     sprintf('/%s(.*)%s/sU', '\/\*', '\*\/'),
        // ], [
        //     '',
        //     '',
        //     ''
        // ], $this->template);

        $view = $this->template;
        $view = preg_replace_callback("/{!!\s*(.*)\s*!!}/m", function ($match) {
            return "<?php " . $match[1] . " ?>";
        }, $view);

        /*$view = preg_replace_callback("/{{\s*(require|include)(?:\(|\s)('|\")(.*)('|\")(?:\)|\s)\s*}}/", function ($match) {
            $file = Environment::fetch(Project::class)->getPath() . 'tmp/' . $match[3] . '.php';
            try {
                View::byFile($match[3])->render(false);
            } catch (ViewNotFoundException $e) {
                throw new ViewNotFoundException($e->getMessage(), 0, 0, $this->file . '.php', -1);
            }
            return '<?php ' . $match[1] . ' ' . $match[2] . $file . $match[4] . '; ?>';
        }, $view);*/

        $view = preg_replace(sprintf('/%s\s*(.*?)\s*%s/s', '{{', '}}'), '<?=$1;?>', $view);

        $meta = stream_get_meta_data($tmp)["uri"];
        fwrite($tmp, $view);

        ob_start();

        include $meta;
        fclose($tmp);

        return ob_get_clean();
    }

}