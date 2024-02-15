<?php

//importing dbDetails file
require_once 'dbDetails.php';
require_once 'db.php';
require_once 'Push.php';
require_once 'Firebase.php';
require_once 'slim/slim/slim/Slim/Slim.php';
//this is our upload folder
$upload_path = 'uploads/';

//Getting the server ip
$server_ip = gethostbyname(gethostname());

//creating the upload url
$upload_url = 'http://' . 'astromonk.in' . '/'. $upload_path;
echo $upload_url;
//response array
$response = array();

echo "Entry";
//if($_SERVER['REQUEST_METHOD']=='POST'){

//checking the required parameters from the request
if(isset($_FILES['pdf']['name']) and isset($_POST['name'])){
    echo "Inside Server";
    //connecting to the database
    $con = mysqli_connect(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME) or die('Unable to Connect...');
    //$con = getDB();
    //getting name from the request
    $name = $_POST['name'];
    $duration = $_POST['duration'];
    $class = $_POST['class'];
    $subject = $_POST['subject'];
    $chapter = $_POST['chapter'];
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
        move_uploaded_file($_FILES['pdf']['tmp_name'],$file_path);
        $sql = "INSERT INTO VideosList( url, name, duration, class, subject, chapter) VALUES ('$file_url', '$name', '$duration', '$class', '$subject', '$chapter');";
        echo "$sql";
        sendPushNotifications($class);
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }

        //adding the path and name to database
        if(mysqli_query($con,$sql)){
            echo "Inside Query";
            //filling response array with values
            $response['error'] = false;
            $response['url'] = $file_url;
            $response['class'] = $class;
            $response['subject'] = $subject;
            $response['chapter'] = $subject;
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
    $sql = "SELECT max(id) as id FROM VideosList";
    $result = mysqli_fetch_array(mysqli_query($con,$sql));

    mysqli_close($con);
    if($result['id']==null)
        return 1;
    else
        return ++$result['id'];
}


function sendPushNotifications($class) {

            $sql2 = "Select id, name FROM ACStudents WHERE user_type = 'Student' AND class='$class'";
            $db = getDB();
            $stmt2 = $db->prepare ( $sql2 );
            $stmt2->execute();
            $assigned_project_details1 =  $stmt2->fetchAll ( PDO::FETCH_OBJ );
            $row = array();
            foreach ($assigned_project_details1 as $row) {
                $assigned_details_json1 = json_decode(json_encode($assigned_project_details1), true);


                $assigned_data1 = array(
                    "id" => $row->id,

                    "name" => $row->name,




                );

                $type = "Video";

                $title = "Hello,".$row->name;
                $message = "A New Video Has Been Uploaded";
                sendPushMessage( $row->id, $title, $message);

            }            //$patient_id = $assigned_details_json1[0]['patient_id'];
            //echo "PATIENT_ID".$patient_id;




    //header('Content-Type: application/json');
   // echo json_encode ( $arr1 );
}
function  sendPushMessage($user_id, $title, $message)
{
    //echo "Inside send single push";
    if($title!=null || $message!=null||$user_id!=null){
        //if the push don't have an image give null in place of image
        $push = new Push(
            $title,
            $message,
            null,
            $user_id

        );
//        echo"".$title;
//        echo"".$message;
//        echo"".$type;
//        echo"".$user_id;
//        echo"".$int_project_id;

        //getting the push from push object
        $mPushNotification = $push->getPush();

        //getting the token from database object
      //  $db1 = new DBFunctions();

        $devicetoken = getTokenByEmail($user_id);

        //echo"Token".$devicetoken;
        //creating firebase class object
        $firebase = new Firebase();

        //sending push notification and displaying result

        $response =  $firebase->send(($devicetoken), $mPushNotification);

    }
    else{
        $response['error']=true;
        $response['message']='Parameters missing';
    }


    echo json_encode($response);
}
function getTokenByEmail($user_id){
    // 		$stmt = $this->con->prepare("SELECT token FROM devices WHERE email = ?");
    // 		$stmt->bindParam("s",$email);
    // 		$stmt->execute();
    // 		$result = $stmt->get_result()->fetch_assoc();
    // 		return array($result['token']);

    $sql = ("SELECT token FROM devices WHERE user_id = $user_id");
    echo"SQL ".$sql;
    $db = getDB ();
    $stmt = $db->prepare( $sql );
    $stmt->bindParam ( ":user_id", $user_id );
    $stmt->execute ();
    $result = $stmt->fetch();
    return $result['token'];
}


?>