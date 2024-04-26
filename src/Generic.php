<?php

namespace Ethansilver\Restaurant;

class Generic
{
    /**
     * @param string $content
     * @param string $title
     */
    public static function generate_page($content, $title = "Restaurant")
    {
        ob_start(); ?>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width">
            <title><?php echo $title; ?></title>
            <link href="static/style.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript" src="script.js"></script>
        </head>
        <body>
            <div id="mySidenav" class="sidenav">
                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                <a href="orders.php">Find Order(s)</a>
                <a href="customers.php">Add Customer</a>
                <a href="schedules.php">Show Schedules</a>
            </div>
            <div id="navbar">
                <table>
                <tr>
                    <th>
                    <a href="javascript:void(0)" onclick="openNav()"><img src="static/menu.png" style="width:30pt"></a>
                    </th>
                    <td>
                    <a href="index.php"><p style="color: black; text-decoration: none; font-weight: bold; font-size: 22pt">Home</p></a>
                    </td>
                </tr>
                </table>
            </div>
            <div id="main">
                <?php echo $content; ?>
            </div>
        </body>
        </html>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
