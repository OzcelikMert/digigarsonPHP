<?php
namespace matrix_library\php\db_helpers;

class results {
    public array $rows = array();
    public bool $status = false;
    public string $message = "";
    public string $sql = "";
    public int $insert_id = 0;
}