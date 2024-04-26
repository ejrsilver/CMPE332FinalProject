<?php

namespace Ethansilver\Restaurant;

class Schedule
{
    private $connection;
    public function __construct()
    {
        $this->connection = DB::connect();
    }

    public function render_page()
    {
        $page = $this->render_form();
        return Generic::generate_page($page, "Find Schedule");
    }

    public function render_result()
    {
        $prep = $this->connection->prepare(
            "SELECT FirstName, LastName FROM Employees WHERE ID = ?"
        );
        $prep->execute([$_POST["employees"]]);
        $res = $prep->fetch();
        $EmpName = $res["FirstName"] . " " . $res["LastName"];
        $res = $this->render_result_table();
        return Generic::generate_page(
            $res,
            "Schedule for Employee " . $EmpName
        );
    }

    private function render_list()
    {
        $result = $this->connection->query(
            "SELECT * FROM Employees ORDER BY ID ASC"
        );
        ob_start();
        ?>
        <p>Who are you looking up?</p>
        <label for="employees">Choose an Employee: </label>
        <select name="employees" id="employees">
            <?php while ($row = $result->fetch()) { ?>
        <option value="
            <?php echo htmlspecialchars($row["ID"]); ?>
            "><?php echo htmlspecialchars($row["FirstName"]) .
                " " .
                htmlspecialchars($row["LastName"]); ?></option>
             <?php } ?></select><?php
$content = ob_get_clean();
ob_end_clean();
return $content;
    }

    public function render_form()
    {
        $res = $this->render_list();
        ob_start();
        ?>
        <form action="schedule_get.php" method="post">
        <p><input type="checkbox" name="weekends" id="wkndbox" value="Show Weekends">
        <label for="wkndbox"> Show Weekends</label></p>
        <input type="submit" value="Check Schedule">
        <?php echo $res; ?>
        </form>
        <?php
        $content = ob_get_clean();
        ob_end_clean();
        return $content;
    }

    public function render_result_table()
    {
        $prep = $this->connection->prepare(
            "SELECT FirstName, LastName FROM Employees WHERE ID = ?"
        );
        $prep->execute([$_POST["employees"]]);
        $res = $prep->fetch();
        $EmpName = $res["FirstName"] . " " . $res["LastName"];
        $weekends = !empty($_POST["weekends"]);

        if (!$weekends) {
            $query =
                "SELECT WEEKDAY(Date) AS weekd, YEARWEEK(Date) as yw, Schedule.Date, StartTime, EndTime, ID FROM `Schedule` WHERE WEEKDAY(Schedule.Date) < 5  AND ID = ? ORDER BY Date ASC";
        } else {
            $query =
                "SELECT WEEKDAY(Date) AS weekd, YEARWEEK(Date) as yw, Schedule.Date, StartTime, EndTime, ID FROM `Schedule` WHERE ID = ? ORDER BY Date ASC";
        }
        $prep = $this->connection->prepare($query);
        $prep->execute([$_POST["employees"]]);

        ob_start();
        ?>
        <h1><?php echo htmlspecialchars($EmpName); ?>'s Schedule</h1>
        <?php
        $row = $prep->fetch();
        $count = $prep->rowCount();

        if (
            count($res) === 0
        ) { ?><p>Sorry, <?php echo $EmpName; ?> doesn't have any scheduled shifts on record!</p><?php } else {
            if ($weekends) { ?>
                <table>
                    <tr>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                        <th>Sunday</th>
                <?php $max = 7;} else { ?>
                <table>
                    <tr>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                <?php $max = 5;}
            $ywcurrent = $row["yw"];
            $inc = 0;
            while ($count > 0) { ?><tr><?php
while ($inc < $max) {
    if ($row["yw"] == $ywcurrent) {
        if ($row["weekd"] == $inc) { ?><td><?php echo htmlspecialchars(
    $row["Date"]
); ?><br><?php echo htmlspecialchars($row["StartTime"]) .
    " - " .
    htmlspecialchars($row["EndTime"]); ?></td>
                                                    <?php
                                                    $count--;
                                                    $row = $prep->fetch();
                                                    } else { ?><td></td><?php }
    } else {
         ?><td></td><?php
    }
    $inc++;
}
$ywcurrent = $row["yw"];
$inc = 0;
?></tr><?php }
            ?></table>
                            <?php }
        $content = ob_get_clean();
        ob_end_clean();
        return $content;
    }
}
