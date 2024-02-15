<?php

//importing dbDetails file
require_once 'dbDetails.php';

//this is our upload folder
$upload_path = 'ProfilePic/';

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
if(isset($_FILES['pdf']['name']) and isset($_POST['user_id'])){
    echo "Inside Server";
    //connecting to the database
    $con = mysqli_connect(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME) or die('Unable to Connect...');
    //$con = getDB();
    //getting name from the request
    $user_id = $_POST['user_id'];

    echo $user_id;
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
        $temp = explode(" ", $_FILES["pdf"]["tmp_name"]);
        echo "TEMP ==".$temp;
        $newfilename = round(microtime(true)) . '.' . end($temp);
        $newfile = $temp['temp_name'].'_'.time();
        $file_path = $upload_path . $newfile . '.'. $extension;
        echo "FILE PATH ==" .$file_path;
        move_uploaded_file($_FILES['pdf']['tmp_name'],$file_path);
        $file_url = $upload_url . $newfile . '.' . $extension;
        echo "FILE URL".$file_url;
        $result = mysqli_query($con,"SELECT * FROM ProfilePic WHERE user_id ='$user_id' ");

        if( mysqli_num_rows($result) > 0) {
            mysqli_query($con,"UPDATE ProfilePic SET url = '$file_url' WHERE user_id = '$user_id' ");
        }
        else{
            //saving the file
            date_default_timezone_set('Canada/Pacific');
            $today = date('m/d/Y h:i:s a', time());
            $sql = "INSERT INTO ProfilePic( user_id, url) VALUES ('$user_id','$file_url');";

            echo "$sql";
            //adding the path and name to database
            if(mysqli_query($con,$sql)){
                echo "Inside Query";
                //filling response array with values
                $response['error'] = false;
                $response['url'] = $file_url;

            }
        }
        //if some error occurred
    }catch(Exception $e){
        $response['error']=true;
        $response['message']=$e->getMessage();
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
    $sql = "SELECT max(id) as id FROM ProfilePic";
    $result = mysqli_fetch_array(mysqli_query($con,$sql));

    mysqli_close($con);
    if($result['id']==null)
        return 1;
    else
        return ++$result['id'];
}
?>
