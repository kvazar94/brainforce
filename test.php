<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>database connections</title>
</head>
<body>
<table border="2" style=" margin: 0 auto;">
    <thead>
    <tr>
        <th>Id</th>
        <th>Product</th>
        <th>Price</th>
        <th>Price whosale</th>
        <th>First stock</th>
        <th>Second stock</th>
        <th>Country</th>
        <th>Note</th>
    </tr>

    </thead>
    <tbody>
    <?php
    $servername = "localhost";
    $database = "listprice_db";
    $username = "user1";
    $password = "user1";

    // Creating a connection
    $conn = new mysqli($servername, $username, $password, $database);
    // Check connection

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    mysqli_query($conn, 'TRUNCATE TABLE price_list');

    require_once 'C:\Users\kvaza\OneDrive\Рабочий стол\fun\vendor\phpoffice\phpexcel\Classes\PHPExcel\IOFactory.php';

    $objPHPExcel = PHPExcel_IOFactory::load('pricelist.xls');
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        for ($row = 1; $row <= $highestRow; ++$row) {
            $cell1 = $worksheet->getCellByColumnAndRow(0, $row);
            $cell2 = $worksheet->getCellByColumnAndRow(1, $row);
            $cell3 = $worksheet->getCellByColumnAndRow(2, $row);
            $cell4 = $worksheet->getCellByColumnAndRow(3, $row);
            $cell5 = $worksheet->getCellByColumnAndRow(4, $row);
            $cell6 = $worksheet->getCellByColumnAndRow(5, $row);

            $sql = "INSERT INTO price_list (`name`,`price`,`price_Wholesale`,`stock_1`,`stock_2`,`country`) VALUES
	('$cell1','$cell2','$cell3','$cell4','$cell5','$cell6')";
            if ($conn->query($sql) === FALSE) echo "Error: " . $cell1 . " " . $conn->error;
        }
    }

    $sql = "SELECT MAX(price) FROM price_list";
    $result = $conn->query($sql);
    $maxPrice = mysqli_fetch_row($result)[0];

    $sql = "SELECT MIN(price_Wholesale) FROM price_list";
    $result = $conn->query($sql);
    $minPrice_Wholesale = mysqli_fetch_row($result)[0];

    $sql = "SELECT * FROM price_list";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $sumArr = array('stock_1' => 0, 'stock_2' => 0, 'price' => 0, 'price_Wholesale' => 0);
        $avgcount = 0;
        while ($row = $result->fetch_assoc()) {

            if ($row["stock_1"] < 20 && $row["stock_2"] < 20) {

                echo "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td>" . ($row["price"] == $maxPrice ? "<td style='background:red;'>" . $row["price"] : "<td>" . $row["price"]) . "</td>" . ($row["price_Wholesale"] == $minPrice_Wholesale ? "<td style='background:green;'>" . $row["price_Wholesale"] : "<td>" . $row["price_Wholesale"]) . "</td><td>" . $row["stock_1"] . "</td><td>" . $row["stock_2"] . "</td><td>" . $row["country"] . "</td><td> First and Second stocks, Осталось мало!! Срочно докупите!!! </td></tr>";
            } elseif ($row["stock_1"] < 20) {

                echo "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td>" . ($row["price"] == $maxPrice ? "<td style='background:red;'>" . $row["price"] : "<td>" . $row["price"]) . "</td>" . ($row["price_Wholesale"] == $minPrice_Wholesale ? "<td style='background:green;'>" . $row["price_Wholesale"] : "<td>" . $row["price_Wholesale"]) . "</td><td>" . $row["stock_1"] . "</td><td>" . $row["stock_2"] . "</td><td>" . $row["country"] . "</td><td> First stock, Осталось мало!! Срочно докупите!!! </td></tr>";

            } elseif ($row["stock_2"] < 20) {

                echo "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td>" . ($row["price"] == $maxPrice ? "<td style='background:red;'>" . $row["price"] : "<td>" . $row["price"]) . "</td>" . ($row["price_Wholesale"] == $minPrice_Wholesale ? "<td style='background:green;'>" . $row["price_Wholesale"] : "<td>" . $row["price_Wholesale"]) . "</td><td>" . $row["stock_1"] . "</td><td>" . $row["stock_2"] . "</td><td>" . $row["country"] . "</td><td> Second stock, Осталось мало!! Срочно докупите!!!  </td></tr>";
            } else {
                echo "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td>" . ($row["price"] == $maxPrice ? "<td style='background:red;'>" . $row["price"] : "<td>" . $row["price"]) . "</td>" . ($row["price_Wholesale"] == $minPrice_Wholesale ? "<td style='background:green;'>" . $row["price_Wholesale"] : "<td>" . $row["price_Wholesale"]) . "</td><td>" . $row["stock_1"] . "</td><td>" . $row["stock_2"] . "</td><td>" . $row["country"] . "</td><td> </td></tr>";
            }
            $avgcount++;
            $sumArr['stock_1'] += $row['stock_1'];
            $sumArr['stock_2'] += $row['stock_2'];
            $sumArr['price'] += $row['price'];
            $sumArr['price_Wholesale'] += $row['price_Wholesale'];

        }
        $sumArr['price'] = $sumArr['price'] / $avgcount;
        $sumArr['price_Wholesale'] = $sumArr['price_Wholesale'] / $avgcount;

        echo "<tr><td></td><td></td><td>" . round($sumArr['price'], 2) . "</td><td>" . round($sumArr['price_Wholesale'], 2) . " </td><td>" . $sumArr['stock_1'] . "</td><td>" . $sumArr['stock_2'] . "</td><td></td><td></td></tr>";
    } else {
        echo "0 results";
    }
    $conn->close();
    ?>
    </tbody>
</table>
</body>
</html>