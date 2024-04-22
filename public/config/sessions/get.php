<?php
namespace config\sessions;

use config\sessions;
use config\sessions\integrations\results;

class get {
    private sessions $sessions;
    public int $BRANCH_ID = 0,
        $BRANCH_ID_MAIN = 0,
        $BRANCH_MAIN_ID = 0,
        $LANGUAGE_ID = 0,
        $USER_ID = 0;
    public string $LANGUAGE_TAG = "tr",
        $CURRENCY = "",
        $USER_NAME = "",
        $BRANCH_NAME = "";
    public bool $IS_MAIN = false,
        $TOKEN = false,
        $CALLER_ID_ACTIVE = false;
    public mixed $BRANCHES = array(),
        $BRANCHES_NAMES = array(),
        $PERMISSION;
    public mixed $INTEGRATION = array();

    public function INTEGRATION(string $key): results {
        return (isset($this->INTEGRATION[$key])) ? unserialize($this->INTEGRATION[$key]) : new results("", "", false);
    }

    public function __construct(sessions $sessions)
    {
        $this->sessions = $sessions;
    }
}