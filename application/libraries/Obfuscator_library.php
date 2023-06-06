<?php
//Load Composer's autoloader
require APPPATH.'third_party/HunterObfuscator.php';

defined('BASEPATH') or exit('No direct script access allowed');
class Obfuscator_library
{
    public function __construct()
    {
        log_message('Debug', 'HunterObfuscator class is loaded.');
    }
}