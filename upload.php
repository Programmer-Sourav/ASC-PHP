<?php

//importing dbDetails file
require_once 'dbDetails.php';

//this is our upload folder
$upload_path = 'uploads/';

//Getting the server ip
$server_ip = gethostbyname(gethostname());

//creating the upload url
$upload_url = 'http://' . 'astromonk.in' . '/'. $upload_path;
$upload_url1 = 'http://' . 'astromonk.in' . '/';
echo $upload_url;
//response array
$response = array();

echo "Entry";
//if($_SERVER['REQUEST_METHOD']=='POST'){

//checking the required parameters from the request
if(isset($_POST['student_id']) and isset($_FILES['pdf']['name']) and isset($_POST['roll_no']) and isset($_POST['name'])){
    echo "Inside Server";
    //connecting to the database
    $con = mysqli_connect(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME) or die('Unable to Connect...');
    //$con = getDB();
    //getting name from the request
    $name = $_POST['name'];
    $roll_no = $_POST['roll_no'];
    $student_id = $_POST['student_id'];
    $unique_id = $_POST['unique_key'];
    echo $name;
    echo ".";
    //getting file info from the request
    $fileinfo = pathinfo($_FILES['pdf']['name']);

    //getting the file extension
    $extension = $fileinfo['extension'];

    //file url to store in the database
    $file_url = $upload_url . getFileName() . '.' . $extension;
    echo "File URL";
    echo $file_url;
    //file path to upload in the server
    $file_path = $upload_path . getFileName() . '.'. $extension;

    echo "FILE PATH".$file_path;
    //trying to save the file in the directory
    try{
        //saving the file
        echo"INSIDE TRY";
        $temp = explode(" ", $_FILES["pdf"]["tmp_name"]);
        echo "TEMP ==".$temp;
        $newfilename = round(microtime(true)) . '.' . end($temp);
        $newfile = $temp['temp_name'].'_'.time();
        $file_path = $upload_path . $newfile . '.'. $extension;
        $file_url1 = $upload_url1 . $file_path;
        echo "FILE PATH ==" .$file_path;
        move_uploaded_file($_FILES['pdf']['tmp_name'],$file_path);


        $sql = "INSERT INTO ApplicationPhoto( student_id, roll_no, url, name, user_unique_key) VALUES ('$student_id', '$roll_no','$file_url1', '$name','$unique_id');";
        echo "$sql";
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }

        //adding the path and name to database
        if(mysqli_query($con,$sql)){
            echo "Inside Query";
            //filling response array with values
            $response['error'] = false;
            $response['url'] = $file_url1;
            $response['student_id'] = $student_id;
            $response['roll_no'] = $roll_no;
            $response['name'] = $name;
        }
        //if some error occurred
    }catch(Exception $e){
        $response['error']=true;
        $response['message']=$e->getMessage();
        echo"Error!";
    }
    //closing the connection
    //mysqli_close($con);
}else{
    $response['error']=true;
    $response['message']='Please choose a file';
}

//displaying the response
echo json_encode($response);
//}

/*
 We are generating the file name
 so this method will return a file name for the image to be upload
 */
function getFileName(){
    $con = mysqli_connect(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME) or die('Unable to Connect...');
    //$con = getDB();
    $sql = "SELECT max(id) as id FROM ApplicationPhoto";
    $result = mysqli_fetch_array(mysqli_query($con,$sql));

    mysqli_close($con);
    if($result['id']==null)
        return 1;
    else
        return ++$result['id'];
}
?>