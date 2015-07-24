<?php
require 'system/bootstrap.php';
bootstrap::$debug=true;
bootstrap::start();
bootstrap::route("HelloWorld", "say");