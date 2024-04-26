<?php
namespace Ethansilver\Restaurant;

class Order
{
    private $connection;
    public function __construct()
    {
        $this->connection = Db::connect();
    }

    public function render_result()
    {
        $res = $this->render_search_orders();
        return Generic::generate_page($res, "Filtered Order Results");
    }

    public function render_page()
    {
        $form = $this->render_form();
        $table = $this->render_orders_table();
        return Generic::generate_page(
            "<h1>Search Orders</h1>" . $form . $table,
            "Search Orders"
        );
    }

    public function render_form()
    {
        ob_start(); ?>
            <h1>Order Finder</h1>
            <h2>Search for an order:</h2>
            <form action="order_get.php" method="post">
                Order Date: <input type="date" name="odate"><br><br>
                Customer Email: <input type="email" name="cemail"><br><br>
                Restaurant: <input type="text" name="rname"><br><br>
                <input type="submit" value="Search">
            </form>
            <h2>All Orders</h2>
          <?php
          $content = ob_get_contents();
          ob_end_clean();
          return $content;
    }

    function render_orders_table()
    {
        $query =
            "SELECT Orders.ID, Orders.RName, c.FirstName AS cfname, c.LastName AS clname, Orders.Email, OrderTime, DeliveryTime, OrderDate, Price, Tip, e.FirstName as dfname, e.LastName as dlname FROM `Orders` INNER JOIN `Employees` e ON e.ID=Orders.Driver INNER JOIN Customers c ON Orders.Email = c.Email";
        $result = $this->connection->query($query);
        ob_start();
        ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Restaurant</th>
                <th>Customer First Name</th>
                <th>Customer Last Name</th>
                <th>Email</th>
                <th>Order Date</th>
                <th>Driver First Name</th>
                <th>Driver Last Name</th>
            </tr>
            <?php while ($row = $result->fetch()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["ID"]); ?></td>
                    <td><?php echo htmlspecialchars($row["RName"]); ?></td>
                    <td><?php echo htmlspecialchars($row["cfname"]); ?></td>
                    <td><?php echo htmlspecialchars($row["clname"]); ?></td>
                    <td><?php echo htmlspecialchars($row["Email"]); ?></td>
                    <td><?php echo htmlspecialchars($row["OrderDate"]); ?></td>
                    <td><?php echo htmlspecialchars($row["dfname"]); ?></td>
                    <td><?php echo htmlspecialchars($row["dlname"]); ?></td>
                </tr>
        <?php } ?>
        </table>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    function render_recent_orders(): string
    {
        $query =
            "SELECT OrderDate, COUNT(ID) as numOrders FROM `Orders` GROUP BY OrderDate ORDER BY numOrders DESC, OrderDate DESC;";
        $result = $this->connection->query($query);
        ob_start();
        ?>
        <table>
            <tr>
                <th>Order Date</th>
                <th>Number of Orders</th>
            </tr>
        <?php while ($row = $result->fetch()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row["OrderDate"]); ?></td>
                <td><?php echo htmlspecialchars($row["numOrders"]); ?></td>
            </tr>
        <?php } ?>
        </table>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function render_search_orders()
    {
        $query =
            "SELECT c.FirstName, c.LastName, o2.Name, o.Price, o.Tip, e.FirstName AS EFName, e.LastName AS ELName, o2.OrderID FROM Employees e INNER JOIN Orders o ON e.ID = o.Driver INNER JOIN Customers c ON c.Email = o.Email INNER JOIN OrderItems o2 ON o2.OrderID = o.ID";

        if (!empty($_POST["odate"])) {
            if (!empty($_POST["rname"])) {
                if (!empty($_POST["cemail"])) {
                    $prep = $this->connection->prepare(
                        $query .
                            " WHERE o.OrderDate = ? AND o.RName = ? AND o.Email = ?"
                    );
                    $prep->execute([
                        $_POST["odate"],
                        $_POST["rname"],
                        $_POST["cemail"],
                    ]);
                } else {
                    $prep = $this->connection->prepare(
                        $query . " WHERE o.OrderDate = ? AND o.RName = ?"
                    );
                    $prep->execute([$_POST["odate"], $_POST["rname"]]);
                }
            } elseif (!empty($_POST["cemail"])) {
                $prep = $this->connection->prepare(
                    $query . " WHERE o.OrderDate = ? AND o.Email = ?"
                );
                $prep->execute([$_POST["odate"], $_POST["cemail"]]);
            } else {
                $prep = $this->connection->prepare(
                    $query . " WHERE o.OrderDate = ?"
                );
                $prep->execute([$_POST["odate"]]);
            }
        } elseif (!empty($_POST["rname"])) {
            if (!empty($_POST["cemail"])) {
                $prep = $this->connection->prepare(
                    $query . " WHERE o.RName = ? AND o.Email = ?"
                );
                $prep->execute([$_POST["rname"], $_POST["cemail"]]);
            } else {
                $prep = $this->connection->prepare(
                    $query . " WHERE o.RName = ?"
                );
                $prep->execute([$_POST["rname"]]);
            }
        } elseif (!empty($_POST["cemail"])) {
            $prep = $this->connection->prepare($query . " WHERE o.Email = ?");
            $prep->execute([$_POST["cemail"]]);
        } else {
            $prep = $this->connection->prepare($query);
            $prep->execute();
        }

        ob_start();
        if ($prep->rowCount() > 0) { ?>
            <table>
                <tr>
                    <th>Customer First Name</th>
                    <th>Customer Last Name</th>
                    <th>Price</th><th>Tip</th>
                    <th>Employee First Name</th>
                    <th>Employee Last Name</th>
                    <th>Order Items</th>
                </tr>
            <?php
            $oid = 0;
            while ($row = $prep->fetch()) {
                if ($oid != $row["OrderID"]) { ?></td></tr>
                    <tr>
                        <td><?php echo htmlspecialchars(
                            $row["FirstName"]
                        ); ?></td>
                        <td><?php echo htmlspecialchars(
                            $row["LastName"]
                        ); ?></td>
                        <td><?php echo htmlspecialchars($row["Price"]); ?></td>
                        <td><?php echo htmlspecialchars($row["Tip"]); ?></td>
                        <td><?php echo htmlspecialchars($row["EFName"]); ?></td>
                        <td><?php echo htmlspecialchars($row["ELName"]); ?></td>
                        <td><?php echo htmlspecialchars($row["Name"]); ?>
                        <?php $oid = $row["OrderID"];} else {echo ", " .
                        htmlspecialchars($row["Name"]);}
            }
            ?></table><?php } else { ?>
            <p>There are no orders on that date.</p>
            <p><img style="width:50%" src="static/sqlerror.gif"></p>
            <?php }
        $content = ob_get_clean();
        ob_end_clean();
        return $content;
    }
}
