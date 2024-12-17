<?php

namespace Trophphic\Controllers;

use Trophphic\Core\TrophphicController;
use Trophphic\Core\Logger;

class FormController extends TrophphicController {
    public function submit() {
        if ($_ENV['LOGGING_ENABLED'] === 'true') {
            Logger::info("Form submitted.");
        }
        echo "Form submitted successfully.". $_POST['test'];
    }
}