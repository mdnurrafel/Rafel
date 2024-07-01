<?php
$servername = "localhost";
$username = "root";
$password = "";
$port = 3307;
$dbname = "lab-4";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert new row if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_number = isset($_POST['item_number']) ? $_POST['item_number'] : '';
    $make = isset($_POST['make']) ? $_POST['make'] : '';
    $model = isset($_POST['model']) ? $_POST['model'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : 0;
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 0;

    // Insert data into the database
    $insert_sql = "INSERT INTO inventory (item_number, make, model, price, quantity) VALUES ('" . $conn->real_escape_string($item_number) . "', '" . $conn->real_escape_string($make) . "', '" . $conn->real_escape_string($model) . "', '" . $conn->real_escape_string($price) . "', '" . $conn->real_escape_string($quantity) . "')";
    if ($conn->query($insert_sql) === TRUE) {
        echo "New record created successfully.";
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}


$make_sql = "SELECT DISTINCT make FROM inventory";
$make_result = $conn->query($make_sql);

$makes = [];
if ($make_result->num_rows > 0) {
    while ($row = $make_result->fetch_assoc()) {
        $makes[] = $row['make'];
    }
}


$selected_make = isset($_GET['make']) ? $_GET['make'] : 'all';

if ($selected_make == 'all') {
    $sql = "SELECT * FROM inventory";
} else {
    $sql = "SELECT * FROM inventory WHERE make = '" . $conn->real_escape_string($selected_make) . "'";
}

$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory</title>
    <style>
        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h1></h1>

<form method="GET" action="insert_data.php">
    <label for="make">Filter by Make:</label>
    <select name="make" id="make">
        <option value="all">All Makes</option>
        <?php foreach ($makes as $make): ?>
            <option value="<?= htmlspecialchars($make) ?>" <?= $selected_make == $make ? 'selected' : '' ?>>
                <?= htmlspecialchars($make) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Search</button>
</form>

<h2></h2>
<form method="POST" action="insert_data.php">
    <label for="item_number">Item Number:</label>
    <input type="text" id="item_number" name="item_number" required>
    <br><br>
    <label for="make">Make:</label>
    <input type="text" id="make" name="make" required>
    <br><br>
    <label for="model">Model:</label>
    <input type="text" id="model" name="model" required>
    <br><br>
    <label for="price">Price:</label>
    <input type="number" id="price" name="price" step="0.01" required>
    <br><br>
    <label for="quantity">Quantity:</label>
    <input type="number" id="quantity" name="quantity" required>
    <br><br>
    <button type="submit" style="color: green;">Add</button>
</form>

<table>
    <thead>
        <tr>
            <th>Item Number</th>
            <th>Make</th>
            <th>Model</th>
            <th>Price</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item_number']) ?></td>
                    <td><?= htmlspecialchars($row['make']) ?></td>
                    <td><?= htmlspecialchars($row['model']) ?></td>
                    <td><?= htmlspecialchars($row['price']) ?></td>
                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No records found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
