<?php
namespace config\settings;

use config\settings\paths\image;

class paths {
    public image $image;
    private string $main_path;

    public function __construct() {
        $this->main_path = $_SERVER["DOCUMENT_ROOT"];
        $this->image = new image($this->main_path);
    }
}