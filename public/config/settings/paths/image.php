<?php
namespace config\settings\paths;

class image {
    private string $main_path;
    private string $folder_branch = "branches";
    private string $folder_user = "users";

    public function __construct($main_path) {
        $this->main_path = $main_path;
    }

    public function PRODUCT(int $id) : string{
        return "{$this->main_path}/images/{$this->folder_branch}/{$id}/product/";
    }

    public function BRANCH_LOGO(int $id, bool $show_file_name = true) : string{
        return "{$this->main_path}/images/{$this->folder_branch}/{$id}/logo/".(($show_file_name) ? "logo.webp" : "");
    }

    public function BRANCH_SLIDER(int $id) : string{
        return "{$this->main_path}/images/{$this->folder_branch}/{$id}/slider/";
    }

    public function USER_AVATAR(int $id) : string{
        return "{$this->main_path}/images/{$this->folder_user}/{$id}/avatar/";
    }
}