<?php

//importing dbDetails file
require_once 'dbDetails.php';

//this is our upload folder
$upload_path = 'Testimonials/';
$target_dirvid = "Testimonials/";

//Getting the server ip
$server_ip = gethostbyname(gethostname());

//creating the upload url
$upload_url = 'http://' . 'vmfoundations.co.in' . '/'. $target_dirvid;
echo $upload_url;
//response array
$response = array();

echo "Entry";
//if($_SERVER['REQUEST_METHOD']=='POST'){

//checking the required parameters from the request
if(isset($_FILES['pdf']['name']) and isset($_POST['name'])and isset($_POST['achiever'])and isset($_POST['code'])) {

    echo "Inside Server";
    //connecting to the database
    $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die('Unable to Connect...');
    //$con = getDB();
    //getting name from the request
    $name = $_POST['name'];
    $chapter = $_POST['achiever'];
    $code = $_POST['code'];
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
    $file_path = $target_dirvid . getFileName() . '.' . $extension;
    echo "FILE PATH" . $file_path;
    //trying to save the file in the directory

    $targetvid = md5(time());


    $target_filevid = $targetvid . basename($_FILES["pdf"]["name"]);

    $uploadOk = 0;

    $videotype = pathinfo($target_filevid, PATHINFO_EXTENSION);

//these are the valid video formats that can be uploaded and
//they will all be converted to .mp4

    $video_formats = array(
        "mpeg",
        "mp4",
        "mov",
        "wav",
        "avi",
        "dat",
        "flv",
        "3gp",
        "png"
    );

    foreach ($video_formats as $valid_video_format) {

        //You can use in_array and it is better

        if (preg_match("/$videotype/i", $valid_video_format)) {
            $target_filevid = $targetvid . basename($_FILES["pdf"] . ".mp4");
            $uploadOk = 1;
            break;

        } else {
            //if it is an image or another file format it is not accepted
            $format_error = "Invalid Video Format!";
        }

    }

    if ($_FILES["media-vid"]["size"] > 500000000) {
        $uploadOk = 0;
        echo "Sorry, your file is too large.";
    }

// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0 && isset($format_error)) {
        $message = "Large";
        echo $message;

        // if everything is ok, try to upload file

    } else if ($uploadOk == 0) {


        echo "Sorry, your video was not uploaded.";

    } else {

        $target_filevid = strtr($target_filevid, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
        $target_filevid = preg_replace('/([^.a-z0-9]+)/i', '_', $target_filevid);

//        if (!move_uploaded_file($_FILES["media-vid"]["tmp_name"], $target_dirvid . $target_filevid)) {
//
//            echo "Sorry, there was an error uploading your file. Please retry.";
//        } else {
//
//            $vid = $target_dirvid . $target_filevid;
//
//        }

        try {
            $temp = explode(" ", $_FILES["pdf"]["tmp_name"]);
            echo "TEMP ==" . $temp;
            $newfilename = round(microtime(true)) . '.' . end($temp);
            $newfile = $temp['temp_name'] . '_' . time();
            $file_path = $target_dirvid . $newfile . '.' . $extension;
            echo "FILE PATH ==" . $file_path;
            move_uploaded_file($_FILES['pdf']['tmp_name'], $file_path);
            $file_url = $upload_url . $newfile . '.' . $extension;
            echo "FILE URL" . $file_url;
            $result = mysqli_query($con, "SELECT * FROM Testimonials WHERE code ='$code' ");

            if (mysqli_num_rows($result) > 0) {
                mysqli_query($con, "UPDATE Testimonials SET url = '$file_url' WHERE code = '$code' ");
            } else {
                //saving the file
                date_default_timezone_set('Canada/Pacific');
                $today = date('m/d/Y h:i:s a', time());
                $sql = "INSERT INTO Testimonials( url, student_name, exams, code) VALUES ($file_url, $name,'$chapter','code');";
                echo "$sql";
                //adding the path and name to database
                if (mysqli_query($con, $sql)) {
                    echo "Inside Query";
                    //filling response array with values
                    $response['error'] = false;
                    $response['url'] = $file_url;
                    $response['name'] = $name;
                    $response['achiever'] = $chapter;
                }
            }
            //if some error occurred
        } catch (Exception $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        }
        //closing the connection
        //mysqli_close($con);
    }
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
        $sql = "SELECT max(id) as id FROM Testimonials";
        $result = mysqli_fetch_array(mysqli_query($con,$sql));

        mysqli_close($con);
        if($result['id']==null)
            return 1;
        else
            return ++$result['id'];


}
?>
