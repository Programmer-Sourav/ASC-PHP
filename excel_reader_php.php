<?php
$conn = mysqli_connect("127.0.0.1","sourav6666","sourav6666","astromonk");
require_once('vendor/php-excel-reader/php-excel-reader/excel_reader2.php');
require_once('vendor/php-excel-reader/SpreadsheetReader.php');

if (isset($_POST["import"]))
{


    $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if(in_array($_FILES["file"]["type"],$allowedFileType)){

        $targetPath = 'ExcelSheets/'.$_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

        $Reader = new SpreadsheetReader($targetPath);

        $sheetCount = count($Reader->sheets());
        for($i=0;$i<$sheetCount;$i++)
        {

            $Reader->ChangeSheet($i);

            foreach ($Reader as $Row)
            {

                $id = "";
                if(isset($Row[0])) {
                    $id = mysqli_real_escape_string($conn,$Row[0]);
                }

                $UID = "";
                if(isset($Row[1])) {
                    $UID = mysqli_real_escape_string($conn,$Row[1]);
                }
                $name = "";
                if(isset($Row[2])) {
                    $name = mysqli_real_escape_string($conn,$Row[2]);
                }

                $class_name = "";
                if(isset($Row[3])) {
                    $class_name = mysqli_real_escape_string($conn,$Row[3]);
                }

                $subject = "";
                if(isset($Row[4])) {
                    $subject= mysqli_real_escape_string($conn,$Row[4]);
                }
                $test_date = "";
                if(isset($Row[5])) {
                    $test_date = mysqli_real_escape_string($conn,$Row[5]);
                }
                $test_marks = "";
                if(isset($Row[6])) {
                    $test_marks = mysqli_real_escape_string($conn,$Row[6]);
                }
                $test_total_marks = "";
                if(isset($Row[7])) {
                    $test_total_marks = mysqli_real_escape_string($conn,$Row[7]);
                }
                $test_no = "";
                if(isset($Row[8])) {
                    $test_no = mysqli_real_escape_string($conn,$Row[8]);
                }

                if (!empty($name) ) {
                    $query = "insert into TestResultsSW(id,UID,Name,Class, Subject, TestDate, TestMarks, TestTotalMarks, TestNo) values('".$id."','".$UID."','".$name."','".$class_name."','".$subject."','".$test_date."','".$test_marks."','".$test_total_marks."','".$test_no."')";
                    echo"QUERY ".$query;
                    $result = mysqli_query($conn, $query);

                    if (! empty($result)) {
                        $type = "success";
                        $message = "Excel Data Imported into the Database";
                    } else {
                        $type = "error";
                        $message = "Problem in Importing Excel Data";
                    }
                }
            }

        }
    }
    else
    {
        $type = "error";
        $message = "Invalid File Type. Upload Excel File.";
    }
}
?>

