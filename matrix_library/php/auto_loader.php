<?php
namespace matrix_library\php;

/**
 * Automatically pulls the called class
 */
class auto_loader {
    /**
     * If there are custom locations, they are entered here
     * @var array
     */
    private $paths; 

    public function __construct() {
        $document_root = $_SERVER["DOCUMENT_ROOT"];
        $this->paths = array(
            "$document_root/public",
            $document_root
        );
    }

    /**
     * Starts auto_loader
     * @return bool
     */
    public function initialize() : bool{
        return spl_autoload_register(function ($class) {
            foreach ($this->paths as $path) {
                $file = $path . "/{$class}.php";
                $file = str_replace("\\", "/", $file);
                if (file_exists($file)) {
                    require $file;
                    return true;
                }
            }
            return false;
        });
    }
}

$auto_loader = new auto_loader();
$auto_loader->initialize();