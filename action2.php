<?php

//action.php

$connect = new PDO("mysql:host=localhost;dbname=phhsystem", "root", "5105458");
$received_data = json_decode(file_get_contents("php://input"));
$data = array();
$action = $received_data->action;

switch ($action) {
    case 'getMaterial':
        $specialShape = $received_data->specialShape;
        if ($specialShape == 'NORMAL') {
            $qr = 'SELECT * FROM material2020 WHERE company = "PST"';
        } else {
            $qr = "SELECT * FROM material2020 WHERE material LIKE '%ms plate%' AND company = 'PST'";
        }
        $statement = $connect->prepare($qr);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);

        break;
    case 'getThickList':
        $matcode = $received_data->matcode;
        $qr = "SELECT DISTINCT thickness FROM $matcode";
        $statement = $connect->prepare($qr);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row['thickness'];
        }
        echo json_encode($data);
        break;
    case 'getW1List':
           # echo json_encode("0;");
        $matcode = $received_data->matcode;
        $thickness = $received_data->thick;
        $qr = "SELECT DISTINCT width FROM $matcode WHERE thickness = '$thickness'";
        $statement = $connect->prepare($qr);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            #echo json_encode("1;");
            //check if there's two
            $rawwidth = strtolower($row['width']);
            $checkWidth = stripos($rawwidth, 'x');
            #echo "<script>console.log('checkWidth = $checkWidth')</script>";
            if (isset($checkWidth)) {
                $widthArr = explode('x', $rawwidth);
                $data[] = $widthArr[0];
            }
        }
        echo json_encode($data);
        break;
    case 'getW2List':
        $matcode = $received_data->matcode;
        $thickness = $received_data->thick;
        $W1 = $received_data->W1;
        $qr = "SELECT DISTINCT width FROM $matcode WHERE thickness = '$thickness' AND width LIKE '%$W1%'";
        $statement = $connect->prepare($qr);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            //check if there's two
            $rawwidth = strtolower($row['width']);
            $widthArr = explode('x', $rawwidth);
            $data[] = $widthArr[1];
        }
        echo json_encode($data);
        break;
    default:
        break;
};



if ($received_data->action == 'fetchall') {
    $query = "
 SELECT * FROM tbl_sample 
 ORDER BY id DESC
 ";
    $statement = $connect->prepare($query);
    $statement->execute();
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }
    echo json_encode($data);
}
if ($received_data->action == 'insert') {
    $data = array(
        ':first_name' => $received_data->firstName,
        ':last_name' => $received_data->lastName
    );

    $query = "
 INSERT INTO tbl_sample 
 (first_name, last_name) 
 VALUES (:first_name, :last_name)
 ";

    $statement = $connect->prepare($query);

    $statement->execute($data);

    $output = array(
        'message' => 'Data Inserted'
    );

    echo json_encode($output);
}
if ($received_data->action == 'fetchSingle') {
    $query = "
 SELECT * FROM tbl_sample 
 WHERE id = '" . $received_data->id . "'
 ";

    $statement = $connect->prepare($query);

    $statement->execute();

    $result = $statement->fetchAll();

    foreach ($result as $row) {
        $data['id'] = $row['id'];
        $data['first_name'] = $row['first_name'];
        $data['last_name'] = $row['last_name'];
    }

    echo json_encode($data);
}
if ($received_data->action == 'update') {
    $data = array(
        ':first_name' => $received_data->firstName,
        ':last_name' => $received_data->lastName,
        ':id' => $received_data->hiddenId
    );

    $query = "
 UPDATE tbl_sample 
 SET first_name = :first_name, 
 last_name = :last_name 
 WHERE id = :id
 ";

    $statement = $connect->prepare($query);

    $statement->execute($data);

    $output = array(
        'message' => 'Data Updated'
    );

    echo json_encode($output);
}

if ($received_data->action == 'delete') {
    $query = "
 DELETE FROM tbl_sample 
 WHERE id = '" . $received_data->id . "'
 ";

    $statement = $connect->prepare($query);

    $statement->execute();

    $output = array(
        'message' => 'Data Deleted'
    );

    echo json_encode($output);
}
?>