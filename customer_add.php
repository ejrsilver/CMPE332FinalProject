<?php
require "vendor/autoload.php";

use Ethansilver\Restaurant\Customer;

$customer = new Customer();
echo $customer->render_create();
