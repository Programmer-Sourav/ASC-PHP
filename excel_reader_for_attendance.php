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

            foreach ($Reader as $Row) {

                $sl_no = "";
                if (isset($Row[0])) {
                    $sl_no = mysqli_real_escape_string($conn, $Row[0]);
                }

                $UID = "";
                if (isset($Row[1])) {
                    $UID = mysqli_real_escape_string($conn, $Row[1]);
                }
                $name = "";
                if (isset($Row[2])) {
                    $name = mysqli_real_escape_string($conn, $Row[2]);
                }

                $class_name = "";
                if (isset($Row[3])) {
                    $class_name = mysqli_real_escape_string($conn, $Row[3]);
                }

                $month = "";
                if (isset($Row[4])) {
                    $month = mysqli_real_escape_string($conn, $Row[4]);
                }
                $date = "";
                if (isset($Row[5])) {
                    $date = mysqli_real_escape_string($conn, $Row[5]);
                }
                $day = "";
                if (isset($Row[6])) {
                    $day = mysqli_real_escape_string($conn, $Row[6]);
                }
                $present_absent = "";
                if (isset($Row[7])) {
                    $present_absent = mysqli_real_escape_string($conn, $Row[7]);
                }
                $total_present = "";
                if (isset($Row[8])) {
                    $total_present = mysqli_real_escape_string($conn, $Row[8]);
                }



                if (!empty($name) ) {
                    $query = "insert into Attendance(sl_no, UID,Name,Class, Month, Date, Day, Present_Absent_Status, Total_Present) values('".$sl_no."','".$UID."','".$name."','".$class_name."','".$month."','".$date."','".$day."','".$present_absent."','".$total_present."')";
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

