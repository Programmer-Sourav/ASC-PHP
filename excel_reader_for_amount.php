<?php
//$conn = mysqli_connect("127.0.0.1","vmfoulyp_ACareer","AsCareer123","vmfoulyp_AspiringCareersDB");
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

                $total_pending_fees = "";
                if(isset($Row[4])) {
                    $total_pending_fees= mysqli_real_escape_string($conn,$Row[4]);
                }
                $amount_installment1_date = "";
                if(isset($Row[5])) {
                    $amount_installment1_date = mysqli_real_escape_string($conn,$Row[5]);
                }
                $amount_installment2_date = "";
                if(isset($Row[6])) {
                    $amount_installment2_date = mysqli_real_escape_string($conn,$Row[6]);
                }
                $amount_installment3_date = "";
                if(isset($Row[7])) {
                    $amount_installment3_date = mysqli_real_escape_string($conn,$Row[7]);
                }
                $amount_installment4_date = "";
                if(isset($Row[8])) {
                    $amount_installment4_date = mysqli_real_escape_string($conn,$Row[8]);
                }
                $amount_installment5_date = "";
                if(isset($Row[9])) {
                    $amount_installment5_date = mysqli_real_escape_string($conn,$Row[9]);
                }
                $amount_installment6_date = "";
                if(isset($Row[10])) {
                    $amount_installment6_date = mysqli_real_escape_string($conn,$Row[10]);
                }

                if (!empty($name) ) {
                    $query = "insert into FinancialData(id,UID,name,class_name, Total_Pending_Fees, Installment1, Installment2, Installment3, Installment4, Installment5, Installment6) values('".$id."','".$UID."','".$name."','".$class_name."','".$total_pending_fees."','".$amount_installment1_date."','".$amount_installment2_date."','".$amount_installment3_date."','".$amount_installment4_date."','".$amount_installment5_date."','".$amount_installment6_date."')";
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

