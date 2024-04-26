<?php
require "vendor/autoload.php";

use Ethansilver\Restaurant\Order;

$order = new Order();
echo $order->render_result();
