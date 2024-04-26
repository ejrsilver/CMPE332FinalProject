<?php
require "vendor/autoload.php";

use Ethansilver\Restaurant\Schedule;

$sched = new Schedule();
echo $sched->render_page();
