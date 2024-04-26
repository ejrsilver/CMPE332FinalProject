<?php
require "vendor/autoload.php";

use Ethansilver\Restaurant\Generic;
use Ethansilver\Restaurant\Order;

$order = new Order();
$recent = $order->render_recent_orders();
ob_start();
?>
    <h1>Welcome to the Restaurant Database!</h1>
    <h2>Recent Orders</h2>
<?php
echo $recent;
$content = ob_get_contents();
ob_end_clean();
echo Generic::generate_page($content, "Home");

