<?php

namespace Ethansilver\Restaurant;

use Exception;

class Customer
{
    private $connection;

    public function __construct()
    {
        $this->connection = Db::connect();
    }

    public function create()
    {
        $prep = $this->connection->prepare(
            "SELECT * FROM Customers WHERE Email=?;"
        );
        $prep->execute([$_POST["email"]]);
        $res = $prep->fetchAll();

        ob_start();
        if (
            count($res) > 0
        ) { ?><p>Customer already in the Database!</p><p><img style="width:50%" src="static/not-again-facepalm.gif"></p><?php } else {$prep = $this->connection->prepare(
                "INSERT INTO Customers values(?,?,?,?,?,?,?,?);"
            );
            try {
                $prep->execute([
                    $_POST["fname"],
                    $_POST["lname"],
                    $_POST["email"],
                    $_POST["phone"],
                    $_POST["street"],
                    $_POST["city"],
                    $_POST["postal"],
                    $_POST["credit"],
                ]); ?><p>Customer was added successfully!</p>
            <p><img style="width:50%" src="static/success.gif"></p><?php
            } catch (Exception $err) {
                ?><p>An unknown error occured: <?php echo $err->getMessage(); ?></p>
            <p><img style="width:50%" src="static/sqlerror.gif"></p><?php
            }}

        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function render_create()
    {
        $content = $this->create();
        return Generic::generate_page($content, "Success!");
    }

    public function render_page()
    {
        $form = $this->render_form();
        $table = $this->render_table();
        return Generic::generate_page(
            "<h1>Customer Centre</h1>" . $form . $table,
            "Customer Centre"
        );
    }

    public function render_form()
    {
        ob_start(); ?>
        <h2>Add a new customer:</h2><p><b style="color: red">* indicates a required field</b></p>
        <form action="customer_add.php" method="post">
            <div style="display: flex; flex-direction: column; gap: 4px; width: 40%;">
                <label for="fname">First Name<b style="color: red">*</b></label>
                <input type="text" name="fname" id="fname" maxlength="20" required>
                <label for="lname">Last Name<b style="color: red">*</b></label>
                <input type="text" name="lname" id="lname" maxlength="20" required>
                <label for="email">Email<b style="color: red">*</b></label>
                <input type="email" name="email" id="email" required>
                <label for="phone">Phone<b style="color: red">*</b></label>
                <input type="tel" name="phone" id="phone" pattern="[0-9]{10}" required>
                <label for="street">Street<b style="color: red">*</b></label>
                <input type="text" name="street" id="street" maxlength="40" required>
                <label for="city">City<b style="color: red">*</b></label>
                <input type="text" name="city" id="city" maxlength="40" required>
                <label for="postal">Postal<b style="color: red">*</b></label>
                <input type="text" name="postal" id="postal" maxlength="6" required>
                <label for="credit">Credit (Default $5)</label>
                <input type="number" name="credit" id="credit" value="5">
                <input type="submit" value="Add Customer">
            </div>
        </form>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function render_table()
    {
        $result = $this->connection->query(
            "SELECT * FROM Customers ORDER BY LastName ASC"
        );
        ob_start();
        ?><h2>Customer List</h2>
        <table style="table-layout: auto">
             <tr>
                 <th>First Name</th>
                 <th>Last Name</th>
                 <th>Email</th>
                 <th>Phone</th>
                 <th>Street</th>
                 <th>City</th>
                 <th>Postal</th
                 ><th>Credit</th>
             </tr>
         <?php while ($row = $result->fetch()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row["FirstName"]); ?></td>
                <td><?php echo htmlspecialchars($row["LastName"]); ?></td>
                <td><?php echo htmlspecialchars(
                    htmlspecialchars($row["Email"])
                ); ?></td>
                <td><?php echo htmlspecialchars($row["Phone"]); ?></td>
                <td><?php echo htmlspecialchars($row["Street"]); ?></td>
                <td><?php echo htmlspecialchars($row["City"]); ?></td>
                <td><?php echo htmlspecialchars($row["Postal"]); ?></td>
                <td><?php echo htmlspecialchars($row["Credit"]); ?></td>
            </tr>
          <?php } ?></table><?php
$content = ob_get_contents();
ob_end_clean();
return $content;
    }
}
