<?php
/**
 * Created by PhpStorm.
 * User: Sourav
 * Date: 10/26/2019
 * Time: 2:03 PM
 */

require 'DBFunctions.php';
require_once 'slim/slim/slim/Slim/Slim.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
//require_once 'PushRequest.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'db.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->post('/login', 'loginUser');
$app->post('/login_student', 'loginStudents');
$app->post('/register_user', 'registerUser');
$app->post('/register_student', 'registerStudent');
$app->post('/register_quizee', 'registerQuizee');
$app->post('/update_answer','updateQuizeeAnswer');
$app->post('/my_profile_student', 'studentProfile');
$app->post('/my_profile_user','userProfile');
$app->post('/my_profile_students','studentProfile');
$app->post('/update_user_profile','updateUserProfile');
$app->post('/update_student_profile','updateStudentProfile');
$app->post('/reset_pass','resetPasswordRequest');
$app->post('/verify_otp','verifyOTP');
$app->post('/update_pass','updatePassword');
$app->post('/get_test_series','get_all_the_test_series');
$app->post('/send_email_complaint', 'sendEmailComplaint');
$app->post('/save_applicant','save_application');
$app->post('/get_syllabus','get_Pdfs');
$app->post('/user_quizee','userQuizee');
$app->post('/get_applicant','get_application_data');
$app->post('/videos_list','getVideos');
$app->post('/applicant_list','getApplicantsList');
$app->post('/get_preview','get_Pdfs_preview');
$app->post('/get_testimonial_videos','getTestimonialVideos');
$app->post('/get_testimonials','getTestimonials');
$app->post('/publish_announcement','publishAnnouncement');
$app->post('/get_anouncments','getAnnouncements');
$app->post('/nabh_chor_list','getNabhChorList');
$app->post('/create_nabh_chor','insertNabhChorList');
$app->post('/save_chapter_name','addChapterName');
$app->post('/pdf_course_preview','get_Pdfs_course_preview');
$app->post('/get_pdf_course','get_CoursePdfs');
$app->post('/check_profile_status','userProfileEmptyColumns');
$app->post('/get_chapter_list','getChapterList');
$app->post('/insert_unique_id','insertStudentUniqueId');
$app->post('/unique_nos','getUniqueNos');
$app->post('/quizee_answers','getQuizeeAnswers');
$app->post('/current_server_time','getCurrentServerTime');
$app->post('/user_details','getUserNameAndPassword');
$app->post('/delete_pics','deleteCoverImages');
$app->post('/cover_images','getCoverImages');
$app->post('/question_paper','getQuestionPaper');
$app->post('/home_images','getHomeImages');
$app->post('/qp_preview','get_Qp_preview');
$app->post('/check_user_existence_in_quizee','checkUserExistenceInQuizee');
$app->post('/view_finance_info','get_financial_data');
$app->post('/get_test_results','get_TestResults');
$app->post('/demo_video_list','getDemoVideos');
$app->post('/quizee_pdf_preview','get_QuizeePdf_preview');
$app->post('/quizee_pdf_download','download_QuizeePdfs');
$app->post('/preview_subject_modules','preview_SubjectModulePdfs');
$app->post('/get_subject_module_preview','getSubjectModulePreview');
$app->post('/attendance','get_Attendance');
$app->post('/check_code_status','findQRCode');
$app->post('/add_qr_links','add_Qr_Links');
$app->post('/get_test_preview','preview_TestSeries');
$app->post('/delete_announcement_on_button_click','delete_announcements');
$app->post('/get_announcement_list','getAnnouncementList');
$app->post('/convert_user_to_student','convertUserToStudent');
$app->post('/get_profile_pic','getProfilePic');
$app->post('/delete_videos','deleteVideosFromList');
$app->post('/send_code_to_verify_email','sendCodeToVerifyEmailOnRegister');
$app->post('/code_to_verify_email','verifyOTPForEmail');
$app->post('/check_user_activation', 'checkUserActivationStatus');
$app->post('/send_comment','send_a_comment_on_video');
$app->post('/get_all_comments', 'get_the_comments_on_videos');
$app->post('/get_quizee_results','get_QuizeePdf');
$app->post('/all_subject_modules','getSubjectModules');
$app->post('/delete_sm','delete_subject_modules');
$app->post('/chapter_wise_videos','getVideosChapterWise');
$app->post('/delete_ts','delete_test_series');
$app->post('/all_test_series','getAllTestSeriesForPreview');

$app->run();

function loginUser() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['email'] ) &&!empty ( $data ['password'] ) ) {
            $token = $data['token'];
            $email = $data['email'];


            $sql = "SELECT id, email, mobile, user_type, status
					FROM ACStudents
					WHERE email = '$email'";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();



            $count = $stmt->rowCount ();
            if ($count == 1) {
                $login_details = $stmt->fetchAll ( PDO::FETCH_OBJ );
                $login_details_json = json_decode(json_encode($login_details), true);
                $arr = array (
                    "status" => 200,
                    "message" => "Login Successful!!",
                    "data" => array(
                        "user_mobile" => $login_details_json[0]['mobile'],
                        "id" => $login_details_json[0]['id'],
                        "user_type" => $login_details_json[0]['user_type'],
                        "status" => $login_details_json[0]['status'],
                        "email" => $login_details_json[0]['email']
                    )


                );

                updateDevice((int)$login_details_json[0]['id'],$email, $token);
            }
            else {
                // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
                $arr = array (
                    "status" => 500,
                    "message" => "Login Failed, wrong Email Id or Password. ",

                );
            }

        }

        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}
function loginStudents() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['email'] ) &&!empty ( $data ['password'] ) ) {
            $token = $data['token'];
            $email = $data['email'];


            $sql = "SELECT id,email, mobile, user_type, unique_id, class, status
					FROM ACStudents
					WHERE email = '$email'";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();



            $count = $stmt->rowCount ();
            if ($count == 1) {
                $login_details = $stmt->fetchAll ( PDO::FETCH_OBJ );
                $login_details_json = json_decode(json_encode($login_details), true);
                $arr = array (
                    "status" => 200,
                    "message" => "Login Successful!!",
                    "data" => array(
                        "user_mobile" => $login_details_json[0]['mobile'],
                        "id" => $login_details_json[0]['id'],
                        "user_type" => $login_details_json[0]['user_type'],
                        "unique_id" => $login_details_json[0]['unique_id'],
                        "status" => $login_details_json[0]['status'],
                        "class" => $login_details_json[0]['class'],
                        "email" => $login_details_json[0]['email']
                    )


                );

                updateDevice((int)$login_details_json[0]['id'],$email, $token);
            }
            else {
                // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
                $arr = array (
                    "status" => 500,
                    "message" => "Login Failed, wrong Email Id or Password. "
                );
            }

        }

        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}
function registerUser() {
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['email'] )&&! empty ( $data ['name'] )&&! empty ( $data ['mobile'] ) &&! empty ( $data ['class'] )&&! empty ( $data ['password'] )&&! empty ( $data ['status'] )) {
            $email = $data ['email'];
            $mobile = $data ['mobile'];
            $user_type = $data ['user_type'];
            $status = $data ['status'];
            $user_exists = checkUserExictence ( $data ['email'] ,$mobile);
            // $token = ( $data ['token'] );

            if ($user_exists == TRUE) {
                $arr = array (
                    "status" => 500,
                    "message" => "Registration failed as user already exists.",
                    "data" => array ()
                );
            } else {
                $sql = "INSERT INTO ACStudents(email,name,mobile, password, class, user_type,status) VALUES (:email,:name,:mobile, :password, :class, :user_type, :status)";

                $db = getDB();
                $stmt = $db->prepare ( $sql );
                $stmt->bindParam(":email",$email);
                $stmt->bindParam ( ":mobile", $data ['mobile'] );
                $stmt->bindParam ( ":name", $data ['name'] );
                $stmt->bindParam ( ":password", $data ['password'] );
                $stmt->bindParam ( ":class", $data ['class'] );
                $stmt->bindParam ( ":user_type", $data ['user_type'] );
                $stmt->bindParam ( ":status", $status );
                $stmt->execute();


                $sql1 = "SELECT id, email,  mobile, user_type, status
					FROM ACStudents
					WHERE email = '$email'";

                $stmt1 = $db->prepare ( $sql1 );
                //  $stmt1->bindParam ( ":unique_id", $data ['unique_id'] );
                $stmt1->execute();
                $registration_details =  $stmt1->fetchAll ( PDO::FETCH_OBJ );
                $registration_details_json = json_decode(json_encode($registration_details), true);
                $registered_data = array (
                    "email" => $registration_details_json[0]['email'],
                    "user_mobile" => $registration_details_json[0]['mobile'],
                    "id" => $registration_details_json[0]['id'],
                    "status" => $registration_details_json[0]['status'],
                    "user_type" => $registration_details_json[0]['user_type']
                );
                $email = $registration_details_json[0]['email'];
                $mobile = $registration_details_json[0]['mobile'];
                $user_type = $registration_details_json[0]['user_type'];
                $status = $registration_details_json[0]['status'];
                $arr = array (
                    "status" => 200,
                    "message" => "Registration Successful!!",
                    "data" => $registered_data
                );
                // updateDevice($data ['unique_id'],$mobile, $token);
            }
        } else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Registration Failed, Mandatory Field Missing. "
            );
        }

    }
    $arr1 = array (
        "response" => $arr
    );
    // header('Content-Type: application/json');
    echo json_encode ( $arr1 );
}
function checkUserExictence($email, $mobile) {

    $sql = "SELECT COUNT(*) AS count FROM ACStudents where email = '$email' OR mobile = '$mobile'";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}
function checkQuizeeExictence($email, $mobile) {

    $sql = "SELECT COUNT(*) AS count FROM Quizee_Details where email = '$email' OR mobile = '$mobile'";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}
function checkRcpExictence($rcp_id) {

    $sql = "SELECT COUNT(*) AS count FROM Quizee_Details where receipt_no = '$rcp_id'";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}
function checkUserExictenceQuizeeMobile($mobile) {

    $sql = "SELECT COUNT(*) AS count FROM ACStudents where  mobile = '$mobile'";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}
function checkUserUniqueIDExictence($unique_id, $mobile) {

    $sql = "SELECT COUNT(*) AS count FROM ACStudents where unique_id = '$unique_id'";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}
function checkStudentExictence($email, $mobile) {
    $sql = "SELECT COUNT(*) AS count FROM ACStudents where email = '$email' OR mobile = '$mobile'";

    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}

function checkQuizeeExictenceById($user_id, $mobile) {
    $sql = "SELECT COUNT(*) AS count FROM Quizee_Details where id = '$user_id' OR Mobile ='$mobile' ";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}
function checkQuizeeEExictence($rcp_id, $mobile) {
    $sql = "SELECT COUNT(*) AS count FROM Quizee_Details where receipt_no = '$rcp_id' OR Mobile ='$mobile' ";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}

function checkQuizeeExictenceFoSameUser($rcp_id, $mobile, $email) {
    $sql = "SELECT COUNT(*) AS count FROM Quizee_Details where receipt_no = '$rcp_id' AND Mobile ='$mobile' AND Email = '$email' ";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}

function checkQuizeeIdExictence($rcp_id) {
    $sql = "SELECT COUNT(*) AS count FROM NabhChor where receipt_id = '$rcp_id' ";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}

function checkUniqueIdExictence($rcp_id) {
    $sql = "SELECT COUNT(*) AS count FROM StuUnique where unique_id = '$rcp_id' ";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}
function checkUserAppearedInQuizeeThisMonth($user_id) {
    $sql = "SELECT COUNT(*) AS count FROM Quizee_Details WHERE date_appeared>=DATE_FORMAT(NOW() ,'%Y-%m-01') AND id ='$user_id' ";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}
function registerStudent() {
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['email'] )&&! empty ( $data ['name'] )&&! empty ( $data ['mobile'] ) &&! empty ( $data ['class'] )&&! empty ( $data ['password'] )&&! empty ( $data ['unique_id'] )&&! empty ( $data ['user_type'] ) &&! empty ( $data ['status'] )) {
            $email = $data ['email'];
            $mobile = ($data ['mobile']);
            $status = ($data ['status']);
            $unique_id = ($data ['unique_id']);
            $unique_id_exists = checkUniqueIdExictence($unique_id);
            $user_id_exists = checkUserExictence($email, $mobile);
            $unique_user_id_exists = checkUserUniqueIDExictence($unique_id, $mobile);
            $token = ($data ['token']);

            if ($unique_id_exists == FALSE) {
                $arr = array(
                    "status" => 500,
                    "message" => "Oops! Provided Unique ID is not a Valid Id!",
                    "data" => array()
                );
            }
            else if ($unique_user_id_exists == TRUE ) {


                $arr = array(
                    "status" => 500,
                    "message" => "Oops! Unique Id Already Exists",
                    "data" => array()
                );
            }

            else
            {
                // $token = ( $data ['token'] );

                if ($user_id_exists == TRUE ) {
                    $arr = array(
                        "status" => 500,
                        "message" => "Oops! Registration Failed. User Already Exists.",
                        "data" => array()
                    );
                } else {
                    $sql = "INSERT INTO ACStudents(unique_id,email,name,mobile, password, class, user_type, status) VALUES (:unique_id,:email,:name,:mobile, :password, :class, :user_type, :status)";

                    $db = getDB();
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(":email", $email);
                    $stmt->bindParam(":mobile", $data ['mobile']);
                    $stmt->bindParam(":name", $data ['name']);
                    $stmt->bindParam(":password", $data ['password']);
                    $stmt->bindParam(":class", $data ['class']);
                    $stmt->bindParam(":unique_id", $data ['unique_id']);
                    $stmt->bindParam(":user_type", $data ['user_type']);
                    $stmt->bindParam(":status", $data ['status']);
                    $stmt->execute();


                    $sql1 = "SELECT id,email,  mobile, user_type, unique_id, class, status
					FROM ACStudents
					WHERE email = '$email'";

                    $stmt1 = $db->prepare($sql1);
                    //  $stmt1->bindParam ( ":unique_id", $data ['unique_id'] );
                    $stmt1->execute();
                    $registration_details = $stmt1->fetchAll(PDO::FETCH_OBJ);
                    $registration_details_json = json_decode(json_encode($registration_details), true);
                    $registered_data = array(
                        "email" => $registration_details_json[0]['email'],
                        "user_mobile" => $registration_details_json[0]['mobile'],
                        "id" => $registration_details_json[0]['id'],
                        "user_type" => $registration_details_json[0]['user_type'],
                        "unique_id" => $registration_details_json[0]['unique_id'],
                        "status" => $registration_details_json[0]['status'],
                        "class" => $registration_details_json[0]['class']
                    );
                    $email = $registration_details_json[0]['email'];
                    $mobile = $registration_details_json[0]['mobile'];
                    $user_type = $registration_details_json[0]['user_type'];
                    $id = $registration_details_json[0]['id'];
                    $unique_id = $registration_details_json[0]['unique_id'];
                    $class = $registration_details_json[0]['class'];
                    $status = $registration_details_json[0]['status'];
                    $arr = array(
                        "status" => 200,
                        "message" => "Registration Successful!!",
                        "data" => $registered_data
                    );
                    updateDevice($id, $mobile, $token);
                }
            }
        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array(
                "status" => 500,
                "message" => "Registration Failed, Mandatory Field Missing. "
            );
        }


    }
    $arr1 = array (
        "response" => $arr
    );
    // header('Content-Type: application/json');
    echo json_encode ( $arr1 );
}

function registerQuizee() {
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();

    if ($json != null) {
        $data = json_decode ( $json, true );

        if (! empty ( $data ['name'] )&&! empty ( $data ['mobile'] )&&! empty ( $data ['parents'] ) &&! empty ( $data ['whatsapp'] )&&! empty ( $data ['email'] )&&! empty ( $data ['rcp_id'] )&&! empty ( $data ['address'] )&&!empty ( $data ['o_status'] )&&!empty ( $data ['password'] )) {
            $rcp_id = $data ['rcp_id'];
            $mobile = $data ['mobile'];
            $o_status = $data['o_status'];

            $user_exists = checkUserExictence( $data ['rcp_id'], $mobile );
            $quizee_exists = checkQuizeeEExictence( $data ['rcp_id'], $mobile );
            $rcp_id_exists = checkRcpExictence( $data ['rcp_id']);
            // $token = ( $data ['token'] );

            if ($quizee_exists == TRUE) {
                $arr = array (
                    "status" => 500,
                    "message" => "Already Registered With Quizee!",
                    "data" => array ()

                );
            }

            else {
                $sql = "INSERT INTO Quizee_Details(name, occupation, Parents, Mobile,Whatsapp, Email, receipt_no, address, o_status) VALUES (:name, :occupation, :Parents, :Mobile,:Whatsapp, :Email, :receipt_no, :address, :o_status)";

                $db = getDB();
                $stmt = $db->prepare ( $sql );
                $stmt->bindParam( ":name",$data['name']);
                $stmt->bindParam( ":occupation",$data['o_status']);
                $stmt->bindParam ( ":Parents", $data ['parents'] );
                $stmt->bindParam ( ":Mobile", $data ['mobile'] );
                $stmt->bindParam ( ":Whatsapp", $data ['whatsapp'] );
                $stmt->bindParam ( ":Email", $data ['email'] );
                $stmt->bindParam ( ":receipt_no", $data ['rcp_id'] );
                $stmt->bindParam ( ":address", $data ['address'] );
                $stmt->bindParam ( ":o_status", $data ['o_status'] );
                $stmt->execute();
                if($user_exists==FALSE) {
                    $sql = "INSERT INTO ACStudents(email,name,mobile, password, class, user_type, status) VALUES (:email,:name,:mobile, :password, :class, :user_type, 'InActive')";

                    $db = getDB();
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(":email", $data ['email']);
                    $stmt->bindParam(":mobile", $data ['mobile']);
                    $stmt->bindParam(":name", $data ['name']);
                    $stmt->bindParam(":password", $data ['password']);
                    $stmt->bindParam(":class", $data ['o_status']);
                    $stmt->bindParam(":user_type", $data ['user_type']);
                    $stmt->execute();
                }
                $email = $data ['email'];
                $sql1 = "SELECT id, Email,  Mobile
					FROM Quizee_Details
					WHERE Email = '$email'";

                $stmt1 = $db->prepare ( $sql1 );
                //  $stmt1->bindParam ( ":unique_id", $data ['unique_id'] );
                $stmt1->execute();
                $registration_details =  $stmt1->fetchAll ( PDO::FETCH_OBJ );
                $registration_details_json = json_decode(json_encode($registration_details), true);
                $registered_data = array (
                    "id" => $registration_details_json[0]['id'],
                    "email" => $registration_details_json[0]['Email'],
                    "user_mobile" => $registration_details_json[0]['Mobile'],
                );
                $email = $registration_details_json[0]['Email'];
                $mobile = $registration_details_json[0]['Mobile'];

                $arr = array (
                    "status" => 200,
                    "message" => "Registration Successful!!",
                    "data" => $registered_data
                );
                // updateDevice($data ['unique_id'],$mobile, $token);
            }
        } else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Registration Failed, Mandatory Field Missing. "
            );
        }

    }
    $arr1 = array (
        "response" => $arr
    );
    // header('Content-Type: application/json');
    echo json_encode ( $arr1 );
}

function updateQuizeeAnswer() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );


//        date_default_timezone_set('Asia/Kolkata');
//        $date = date('Y-m-d H:i:s');

        if (!empty ( $data ['rcp_id'] ) &&!empty ( $data ['user_id'] ) &&!empty ( $data ['opt_1'] )&&!empty ( $data ['opt_2'] )&&!empty ( $data ['opt_3'] )&&!empty ( $data ['opt_4'] )&&!empty ( $data ['opt_5'] )) {
            //  $token = $data['token'];
            $rcp_no = $data['rcp_id'];

            $quizee_id = $data ['user_id'] ;

            $opt_a = $data ['opt_1'] ;

            $opt_b = $data ['opt_2'] ;

            $opt_c = $data ['opt_3'] ;

            $opt_d = $data ['opt_4'] ;

            $opt_e = $data ['opt_5'] ;
            $date = $data ['date'] ;

            $sql = "UPDATE Quizee_Details SET opt_A = '$opt_a', opt_B = '$opt_b',opt_C = '$opt_c',opt_D = '$opt_d',opt_E = '$opt_e', date_appeared = '$date' WHERE receipt_no = '$rcp_no' AND id = '$quizee_id'";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();


        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. "
            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}

function studentProfile() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['user_id'] )  ) {
            //$token = $data['token'];
            $user_id = $data['user_id'];


            $sql = "SELECT name, class, email, mobile, IFNULL(session_year, '2019-20') AS session_year, IFNULL(address, 'Your Address') AS address FROM ACStudents
					WHERE id = '$user_id'";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}
function userProfile() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['user_id'] )  ) {
            //$token = $data['token'];
            $user_id = $data['user_id'];


            $sql = "SELECT *FROM ACStudents
					WHERE id = '$user_id'";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}
function updateUserProfile() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (! empty ( $data ['email'] )&&! empty ( $data ['user_id'] )&&! empty ( $data ['name'] )&&! empty ( $data ['mobile'] ) &&! empty ( $data ['class_name'] )&&! empty ( $data ['session_year'] )&&! empty ( $data ['address'] )) {
            //$token = $data['token'];
            $user_id = $data['user_id'];
            $email = $data['email'];
            $name = $data['name'];
            $mobile = $data['mobile'];
            $class_name = $data['class_name'];
            $password = $data['password'];
            $session = $data['session_year'];
            $address = $data['address'];
            $address = $data['pass'];
            $address = $data['confirm_pass'];
            $sql = "UPDATE ACStudents SET email = '$email', name = '$name', mobile = '$mobile', class_name= '$class_name', password = '$password', session = '$session', address = '$address'
					WHERE id = '$user_id'";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}
function updateStudentProfile() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (! empty ( $data ['email'] )&&! empty ( $data ['user_id'] )&&! empty ( $data ['name'] )&&! empty ( $data ['class_name'] )&&! empty ( $data ['session_year'] )&&! empty ( $data ['address'] )) {
            //$token = $data['token'];
            $user_id = $data['user_id'];
            $email = $data['email'];
            $name = $data['name'];
            $mobile = $data['mobile'];
            $class_name = $data['class_name'];
            $session = $data['session_year'];
            $address = $data['address'];
            $password = $data['pass'];
            if($password!=null || $password!=""){
                $sql = "UPDATE ACStudents SET email = '$email', name = '$name', mobile = '$mobile', class= '$class_name', password = '$password', session_year = '$session', address = '$address'
					WHERE id = '$user_id'";
            }else {
                $sql = "UPDATE ACStudents SET email = '$email', name = '$name', mobile = '$mobile', class= '$class_name', session_year = '$session', address = '$address'
					WHERE id = '$user_id'";
            }

            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            //$my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => ""
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}
function resetPasswordRequest(){
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data['email'] ) ) {

            $email = ( $data['email'] );
            $mobile = '990990099';
            $user_exists = checkUserExictenceEmail ( $email );



            if ($user_exists == TRUE) {
                $email = $data["email"];
                if(substr( $email, 0, 4 ) === "admin"||substr( $email, 0, 4 ) === "Admin"){
                    $mail_result = sendEmailPhpMailerToAsc($data["email"]);

                }
                else {
                    $mail_result = sendEmailPhpMailer($email);

                }


                $arr = array (
                    "status" => 200,
                    "message" => "An Email has been sent to You. Please check Inbox!",
                    "data" => ''
                );
            }
            else
            {
                $arr = array (
                    "status" => 500,
                    "message" => "Failed! Email Id is not present in our Database!",
                    "data" => ''
                );
            }
            $arr1 = array (
                "response" => $arr
            );

            echo json_encode ( $arr1 );

        }
    }
}


function sendEmailToAdminUsingPhpMailer($email, $subject, $solution)
{

    $mail = new PHPMailer(true);

    try {
        // $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = "care.acian@gmail.com";
//Password to use for SMTP authentication
        $mail->Password = "Aspirehigh";
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('care.acian@gmail.com', 'Aspiring Careers Support');
        $mail->addAddress($email);


        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $solution;
        $mail->AltBody = $solution;

        $mail->send();
        $mail->clearAllRecipients();
        // echo "Mail has been sent successfully!";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    exit();

    // echo 'Message has been sent';

}

function sendEmail($email){


    $code = getOTP($email);
    $name="";
    $subject = "Password Reset - Aspiring Careers";
    $message = "Hello $name,\n\n Hi, $email.\n\nPlease Enter this code: $code. to recreate Password!\n\n\n\nRegards,\nAspiring Careers Team.";
    $from = "resetpassword@aspcareera.com";
    $headers = "From:" . $from;
    mail($email,$subject,$message,$headers);
}
function sendEmailPhpMailer($email){

    $name = "";
    $code = getOTP($email);
    //  echo "CODE ".$code;
    $subject = "Password Reset - Aspiring Careers";
    $message = "Hello $name,\n\n Hi, $email.\n\nPlease Enter this code: $code. to recreate Password!\n\n\n\nRegards,\nAspiring Careers Team.";
    $from = "resetpassword@aspcareers.com";
    $headers = "From:" . $from;

    sendEmailToAdminUsingPhpMailer($email,$subject, $message);
    //mail($email,$subject,$message,$headers);

}


function generateOTP($email)
{

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );


        $code = rand(100000, 999999);
        $check_otp_exists_query = 'SELECT * FROM OTP WHERE email=:email';

        $email = $data ['email'];

        $stmt = $db->prepare ( $check_otp_exists_query);
        $stmt->bindParam ( ":email", $data ['email']);

        $stmt->execute ();

        $existing_otp_data = $stmt->fetchAll(PDO::FETCH_OBJ);
        $rt =  json_decode(json_encode($existing_otp_data), true);
        if (empty($rt)){
            $sql = "INSERT INTO OTP (email, code) VALUES (:email, $code)";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            $stmt->bindParam ( ":email", $data ['email'] );


            $stmt->execute();

        }

        else
        {

            $update = "UPDATE OTP SET code = :code WHERE email = :email";
            $stmt = $db->prepare ( $update );
            $stmt->bindParam ( ":code", $code);
            $stmt->bindParam ( ":email", $email);
            $stmt->execute ();

        }
    }
}
function getOTP($email)
{
    generateOTP($email);
    $sql = "SELECT code FROM OTP where email = '$email'";
    $db = getDB ();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $otp = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $otp_json = json_decode(json_encode($otp), true);



    return (int)$otp_json[0]['code'];



}

function verifyOTP(){

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();
    $ret =0;
    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['code'] ) && ! empty ( $data ['email'] )){

            $otpMatches = verifyOTPF($data ['code'] , $data ['email'] );
            if ($otpMatches== TRUE) {
                $arr = array (
                    "status" => 200,
                    "message" => "Success.",
                    "data" => array ()
                );
            }
            else
            {
                $arr = array (
                    "status" => 500,
                    "message" => "Failed! Please Enter Valid Code",
                    "data" => array ()
                );
            }
            $arr1 = array (
                "response" => $arr
            );

            echo json_encode ( $arr1 );
        }
    }
}
function verifyOTPF($code, $email){

    $sql = "SELECT code FROM OTP where email = '$email'";

    $db = getDB ();


    $stmt = $db->prepare ( $sql );
    //$stmt->bindParam ( ":email", $email);
    //$stmt->bindParam ( ":code", $code);
    $stmt->execute ();
    $user_code = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_code_json = json_decode(json_encode($user_code), true);

    if ($code == (int)$user_code_json[0]['code']) {

        return true;
    }

    return false;
}
function updatePassword()
{
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();
    $value = verifyOTP();
    $data = json_decode($json,true);
    if (! empty ( $data ['email'] ) && !empty($data['password']))
    {
        $email =  $data ['email'];
        $password =  $data['password'];

        $sql = "UPDATE ACStudents set password= '$password'  where email = '$email'";

        $db = getDB ();

        $stmt = $db->prepare ( $sql );
        $stmt->bindParam ( ":email", $email);
        $stmt->bindParam ( ":password", $password);
        $stmt->execute ();
    }
}
function get_all_the_test_series(){
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['class_name'] )  ) {
            //$token = $data['token'];
            // $user_id = $data['user_id'];

            $class_name = ( $data ['class_name'] );
            $sql = "SELECT *FROM AllTestSeries WHERE class = '$class_name'";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }

}

function sendEmailComplaint(){
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data['email'] ) && ! empty ( $data['mobile'] ) && ! empty ( $data['name'] )&& ! empty ( $data['message'] )) {
            // echo "EMAIL ".( $data['email'] );
            $email = ( $data['email'] );
            $mobile = $data['mobile'];
            $name = $data['name'];
            $message = $data['message'];
            $contact = $mobile;
            $name1 = $name;
            $subject = "Feedback/Complaint....";
            $message = "Hello Peeyush,\n\n $email with Contact No - $mobile \n\n has a Complaint/Feedback on Aspiring Careers. Details,  \n\n\n\n Name: $name1 \n\n\n\n Message: $message \n\n\n\n Email: $email \n\n\n\n Contact: $mobile \n\n\n\n";
            $from = "care.acian@aspcareers.com";
            $headers = "From:" . $from;
            sendEmailComplaintToAdminUsingPhpMailer($email, $message, $subject, $name1);
            $arr = array (
                "status" => 200,
                "message" => "Email Successfully Sent",
                "data" => ""
            );
            //  $user_exists = checkUserExictence ( $email, $mobile );

            //  $mail_result = sendEmailPhpMailer($data["email"]);

            /*  if ($user_exists == TRUE) {
                  echo"USER EXIST".$data['email'] ;
                  $mail_result = sendEmailPhpMailer($data["email"]);



                  $arr = array (
                      "status" => 200,
                      "message" => "An Email has been sent to You. Please check Inbox!",
                      "data" => array ()
                  );
              }*/
        }
    }
}


function save_application() {
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['student_id'] )&& ! empty ( $data ['student_name'] )&&! empty ( $data ['dob'] )&&! empty ( $data ['father_name'] )&&! empty ( $data ['mother_name'] )&&! empty ( $data ['gender'] )&&! empty ( $data ['category'] )&&! empty ( $data ['city'] )&&! empty ( $data ['district'] )&&! empty ( $data ['country'] )&&! empty ( $data ['pin_code'] )&&! empty ( $data ['tel_no'] )&&! empty ( $data ['father_mobile'] )&&! empty ( $data ['email'] )&&! empty ( $data ['unique_key'] )) {
            $receipt_no = $data ['receipt_no'];
            $unique_key = $data['unique_key'];


            $sql = "INSERT INTO Deposit(roll_no,course_name,rest_material, receipt_no, remarks, date, authority, material_given_by_hand, student_id) VALUES (:roll_no,:course_name,:rest_material, :receipt_no, :remarks, :date, :authority, :material_given_by_hand, :student_id)";

            $db = getDB();
            $stmt = $db->prepare ( $sql );
            $stmt->bindParam(":roll_no",$data['roll_no']);
            $stmt->bindParam ( ":course_name", $data ['course_name'] );
            $stmt->bindParam ( ":rest_material", $data ['rest_material'] );
            $stmt->bindParam ( ":receipt_no", $data ['receipt_no'] );
            $stmt->bindParam ( ":remarks", $data ['remarks'] );
            $stmt->bindParam ( ":date", $data ['date'] );
            $stmt->bindParam ( ":authority", $data ['authority'] );
            $stmt->bindParam ( ":material_given_by_hand", $data ['material_g_h'] );
            $stmt->bindParam ( ":student_id", $data ['student_id'] );
            $stmt->execute();
            $sql1 = "INSERT INTO CourseDetailsForApplication(course_name,amount, cheque_no,bank, city, date, dateOf, academic_session, roll_no, pdc1, pdc2, cash, student_id,user_unique_key) VALUES (:course_name,:amount, :cheque_no,:bank, :city, :date, :dateOf, :academic_session, :roll_no, :pdc1, :pdc2, :cash, :student_id,:user_unique_key)";

            $db = getDB();
            $stmt1= $db->prepare ( $sql1 );
            $stmt1->bindParam(":roll_no",$data['roll_no']);
            $stmt1->bindParam ( ":course_name", $data ['course_name'] );
            $stmt1->bindParam ( ":cheque_no", $data ['cheque_no'] );
            $stmt1->bindParam ( ":bank", $data ['bank'] );
            $stmt1->bindParam ( ":city", $data ['city'] );
            $stmt1->bindParam ( ":date", $data ['date'] );
            $stmt1->bindParam ( ":dateOf", $data ['date_of'] );
            $stmt1->bindParam ( ":academic_session", $data ['academic_session'] );
            $stmt1->bindParam ( ":roll_no", $data ['roll_no'] );
            $stmt1->bindParam ( ":pdc1", $data ['pdc1'] );
            $stmt1->bindParam ( ":pdc2", $data ['pdc2'] );
            $stmt1->bindParam ( ":cash", $data ['cash'] );
            $stmt1->bindParam ( ":amount", $data ['deposit_amt'] );
            $stmt1->bindParam ( ":student_id", $data ['student_id'] );
            $stmt1->bindParam ( ":user_unique_key", $unique_key );
            $stmt1->execute();
            $sql3 = "INSERT INTO StudentMarksDetails(student_id,class1, class2,session1, session2,board1, board2, english1,english2,hindi1, hindi2,maths1, maths2, science1, science2, sst1, sst2, physics1, physics2,chemistry1, chemistry2,biology1, biology2,present_school, school_contact, school_address, user_unique_key) VALUES (:student_id,:class1, :class2,:session1, :session2, :board1,:board2, :english1,:english2,:hindi1, :hindi2,:maths1, :maths2, :science1, :science2, :sst1, :sst2, :physics1, :physics2,:chemistry1, :chemistry2,:biology1, :biology2,:present_school, :school_contact, :school_address,:user_unique_key)";

            $db = getDB();
            $stmt3= $db->prepare ( $sql3 );
            $stmt3->bindParam(":student_id",$data['student_id']);
            $stmt3->bindParam ( ":class1", $data ['current_class1'] );
            $stmt3->bindParam ( ":class2", $data ['course2'] );
            $stmt3->bindParam ( ":session1", $data ['session1'] );
            $stmt3->bindParam ( ":session2", $data ['session2'] );
            $stmt3->bindParam ( ":board1", $data ['board1'] );
            $stmt3->bindParam ( ":board2", $data ['board2'] );
            $stmt3->bindParam ( ":english1", $data ['english1'] );
            $stmt3->bindParam ( ":english2", $data ['english2'] );
            $stmt3->bindParam ( ":hindi1", $data ['hindi1'] );
            $stmt3->bindParam ( ":hindi2", $data ['hindi2'] );
            $stmt3->bindParam ( ":maths1", $data ['maths1'] );

            $stmt3->bindParam(":maths2",$data['maths2']);
            $stmt3->bindParam ( ":science1", $data ['science1'] );
            $stmt3->bindParam ( ":science2", $data ['science2'] );
            $stmt3->bindParam ( ":sst1", $data ['sst1'] );
            $stmt3->bindParam ( ":sst2", $data ['sst2'] );
            $stmt3->bindParam ( ":physics1", $data ['physics1'] );
            $stmt3->bindParam ( ":physics2", $data ['physics2'] );
            $stmt3->bindParam ( ":chemistry1", $data ['chemistry1'] );
            $stmt3->bindParam ( ":chemistry2", $data ['chemistry2'] );
            $stmt3->bindParam ( ":biology1", $data ['biology1'] );
            $stmt3->bindParam ( ":biology2", $data ['biology2'] );
            $stmt3->bindParam ( ":present_school", $data ['name_of_school'] );
            $stmt3->bindParam ( ":school_contact", $data ['contact'] );
            $stmt3->bindParam ( ":school_address", $data ['address'] );
            $stmt3->bindParam ( ":user_unique_key", $unique_key );
            $stmt3->execute();

            $sql4 = "INSERT INTO Student_details_application(student_id, name,mother_name, current_class,dob, gender,category, address,city,state,district, country,pin_code,father_mobile, email, contact_no, tel_no, roll_no, father_name,cor_address, counsellor_sig, student_sig, father_sig, mother_sig, user_unique_key) VALUES (:student_id, :name,:mother_name, :current_class,:dob, :gender,:category, :address,:city,:state,:district, :country,:pin_code,:father_mobile, :email, :contact_no, :tel_no, :roll_no,  :father_name,:cor_address, :counsellor_sig, :student_sig, :father_sig, :mother_sig, :user_unique_key)";

            $db = getDB();
            $stmt4= $db->prepare ( $sql4 );
            $stmt4->bindParam(":name",$data['student_name']);
            $stmt4->bindParam ( ":mother_name", $data ['mother_name'] );
            $stmt4->bindParam ( ":current_class", $data ['current_class'] );
            $stmt4->bindParam ( ":dob", $data ['dob'] );
            $stmt4->bindParam ( ":gender", $data ['gender'] );
            $stmt4->bindParam ( ":category", $data ['category'] );
            $stmt4->bindParam ( ":address", $data ['address'] );
            $stmt4->bindParam ( ":city", $data ['city'] );
            $stmt4->bindParam ( ":state", $data ['state'] );
            $stmt4->bindParam ( ":district", $data ['district'] );
            $stmt4->bindParam ( ":country", $data ['country'] );
            $stmt4->bindParam ( ":pin_code", $data ['pin_code'] );
            $stmt4->bindParam ( ":father_mobile", $data ['father_mobile'] );
            $stmt4->bindParam ( ":email", $data ['email'] );
            $stmt4->bindParam ( ":contact_no", $data ['contact'] );
            $stmt4->bindParam ( ":tel_no", $data ['tel_no'] );
            $stmt4->bindParam ( ":roll_no", $data ['roll_no'] );
            $stmt4->bindParam ( ":father_name", $data ['father_name'] );
            $stmt4->bindParam ( ":cor_address", $data ['cor_address'] );
            $stmt4->bindParam ( ":counsellor_sig", $data ['counsellor_sig'] );
            $stmt4->bindParam ( ":student_sig", $data ['student_sig'] );
            $stmt4->bindParam ( ":student_id", $data ['student_id'] );
            $stmt4->bindParam ( ":father_sig", $data ['father_sig'] );
            $stmt4->bindParam ( ":mother_sig", $data ['mother_sig'] );
            $stmt4->bindParam ( ":user_unique_key", $unique_key );
            $stmt4->execute();



            $arr = array (
                "status" => 200,
                "message" => "Registration Successful!!",
                "data" => ""
            );
            // updateDevice($data ['unique_id'],$mobile, $token);
        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Registration Failed, Mandatory Field Missing. "
            );
        }

    }
    $arr1 = array (
        "response" => $arr
    );
    // header('Content-Type: application/json');
    echo json_encode ( $arr1 );
}
function get_Pdfs() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['class'] ))
        {

            $int_class =  $data ['class'];
            //$state = $data['status'];

            $sql = "SELECT url FROM CourseSyllabus WHERE class = $int_class";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}


function userQuizee()
{
    $app = \Slim\Slim::getInstance();
    $json = $app->request->getBody();
    $arr = array();

    if ($json != null) {
        $data = json_decode($json, true);

        if (!empty ($data ['rcp_id']) && !empty ($data ['user_id'])) {
            $rcp_id = $data ['rcp_id'];
            $user_id = $data ['user_id'];
            //$mobile = $data ['user_id'];
            //$o_status = $data['o_status'];
            $mobile = "0000000000000";
            $db = getDB();
            $sql2 = "SELECT  Email, Mobile FROM
					 Quizee_Details
					WHERE id = $user_id";

            $stmt2 = $db->prepare($sql2);
            //  $stmt1->bindParam ( ":unique_id", $data ['unique_id'] );
            $stmt2->execute();
            $registration_details2 = $stmt2->fetchAll(PDO::FETCH_OBJ);
            $registration_details_json2 = json_decode(json_encode($registration_details2), true);
            $registered_data2 = array(
                "id" => $registration_details_json2[0]['id'],
                "email" => $registration_details_json2[0]['Email'],
                "user_mobile" => $registration_details_json2[0]['Mobile'],

            );
            $id = $registration_details_json2[0]['id'];
            $email = $registration_details_json2[0]['Email'];
            $mobile = $registration_details_json2[0]['Mobile'];
            $user_exists = checkUserExictence($data ['rcp_id'], $mobile);
            $quizee_exists = checkQuizeeExictenceFoSameUser($data ['rcp_id'], $mobile, $email);
            $rcp_id_exists = checkRcpExictence($data ['rcp_id']);
            // $token = ( $data ['token'] );

            if ($quizee_exists == FALSE) {
                $arr = array(
                    "status" => 500,
                    "message" => "Please use the Nabhchor id You have been assigned!",
                    "data" => array()

                );
            } else {
                $sql2 = "SELECT *FROM
					 Quizee_Details
					WHERE id = $user_id";

                $stmt2 = $db->prepare($sql2);
                //  $stmt1->bindParam ( ":unique_id", $data ['unique_id'] );
                $stmt2->execute();
                $registration_details2 = $stmt2->fetchAll(PDO::FETCH_OBJ);
                $registration_details_json2 = json_decode(json_encode($registration_details2), true);
                $registered_data2 = array(
                    "id" => $registration_details_json2[0]['id'],
                    "email" => $registration_details_json2[0]['Email'],
                    "user_mobile" => $registration_details_json2[0]['Mobile'],
                    "parents" => $registration_details_json2[0]['Parents'],
                    "mobile" => $registration_details_json2[0]['Mobile'],
                    "address" => $registration_details_json2[0]['address'],
                    "whatsapp" => $registration_details_json2[0]['Whatsapp'],
                    "name" => $registration_details_json2[0]['name']

                );
                $id = $registration_details_json2[0]['id'];
                $email = $registration_details_json2[0]['Email'];
                $o_status = $registration_details_json2[0]['occupation'];
                $parents = $registration_details_json2[0]['Parents'];
                $mobile = $registration_details_json2[0]['Mobile'];
                $address = $registration_details_json2[0]['address'];
                $whatsapp = $registration_details_json2[0]['Whatsapp'];
                $name = $registration_details_json2[0]['name'];

                $sql = "INSERT INTO Quizee_Details(name, occupation, Parents, Mobile,Whatsapp, Email, receipt_no, address, o_status) VALUES (:name, :occupation, :Parents, :Mobile,:Whatsapp, :Email, :receipt_no, :address, :o_status)";

                $db = getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":occupation", $o_status);
                $stmt->bindParam(":Parents", $parents);
                $stmt->bindParam(":Mobile", $mobile);
                $stmt->bindParam(":Whatsapp", $whatsapp);
                $stmt->bindParam(":Email", $email);
                $stmt->bindParam(":receipt_no", $rcp_id);
                $stmt->bindParam(":address", $address);
                $stmt->bindParam(":o_status", $o_status);

                $stmt->execute();


                $arr = array(
                    "status" => 200,
                    "message" => "Registration Successful!!",
                    "data" => $registered_data2
                );
                // updateDevice($data ['unique_id'],$mobile, $token);
            }
        }
    }
    else {
        // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
        $arr = array (
            "status" => 500,
            "message" => "Registration Failed, Mandatory Field Missing. "
        );
    }


    $arr1 = array (
        "response" => $arr
    );
    // header('Content-Type: application/json');
    echo json_encode ( $arr1 );
}


function get_application_data() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];



            $sql = "SELECT Student_details_application.*, StudentMarksDetails.*, CourseDetailsForApplication.*, ApplicationPhoto.url FROM Student_details_application
 INNER JOIN StudentMarksDetails ON Student_details_application.user_unique_key = StudentMarksDetails.user_unique_key INNER JOIN CourseDetailsForApplication 
 ON StudentMarksDetails.user_unique_key = CourseDetailsForApplication.user_unique_key 
INNER JOIN ApplicationPhoto ON ApplicationPhoto.user_unique_key = Student_details_application.user_unique_key WHERE Student_details_application.user_unique_key=  '$int_class'";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}
function getVideos() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['class_name'] )&&! empty ( $data ['subject'] ))
        {

            $_class =  $data ['class_name'];
            $subject = $data['subject'];

            $sql = "SELECT id, name, url, duration, chapter, class FROM VideosList WHERE class = '$_class' AND subject = '$subject' GROUP BY chapter";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}


function getVideosChapterWise() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['chapter_name'] )&&! empty ( $data ['subject'] ))
        {

            $_class =  $data ['chapter_name'];
            $subject = $data['subject'];

            $sql = "SELECT id, name, url FROM VideosList WHERE chapter = '$_class' AND subject = '$subject'";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}
function getApplicantsList() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT id, student_id, name, roll_no ,user_unique_key FROM Student_details_application";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function get_Pdfs_preview() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT url FROM CourseSyllabus WHERE id = $int_class";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}


function getTestimonialVideos() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT url FROM Testimonial WHERE id = '$int_class'";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}
function getTestimonials() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT student_name, id , url, exams FROM Testimonials";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function publishAnnouncement() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['header'] )&&! empty ( $data ['body'] )&&! empty ( $data ['signature'] )&&! empty ( $data ['date'] ))
        {

            $_header =  $data ['header'];
            $_body =  $data ['body'];
            $_signature =  $data ['signature'];
            $_date =  $data ['date'];

            $sql = "INSERT INTO Announcements(header, body, signature, date) VALUES(:header, :body, :signature, :date)";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            $stmt->bindParam ( ":header", $_header );
            $stmt->bindParam ( ":body", $_body);
            $stmt->bindParam ( ":signature", $_signature );
            $stmt->bindParam ( ":date", $_date );
            $stmt->execute ();
            //  $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" =>""
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function getAnnouncements() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['class_id'] )  ) {
            //$token = $data['token'];
            $user_id = $data['class_id'];


            $sql = "SELECT *FROM Announcements";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $announcement_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $announcement_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}



function getNabhChorList() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['id'] )  ) {
            //$token = $data['token'];
            $user_id = $data['id'];


            $sql = "SELECT *FROM NabhChor";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $announcement_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $announcement_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}

function insertNabhChorList() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['session'] )&&! empty ( $data ['class_name'] )) {

            $_id = $data ['session'];

            $milliseconds = round(microtime(true) * 1000);

            $ses = "2019-2020";
            $time = time();
            for ($i = 223; $i < 333; $i++) {
                $randomString = substr(MD5(time()),5);
                $rp = "NC".$randomString;
                $sub_mil = substr(time()*$i,0,5);
                $rcp_id = "NC" . $sub_mil;
                $sql = "INSERT INTO NabhChor(receipt_id, session) VALUES(:rcp_id, :ses)";

                $db = getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindParam(":rcp_id", $rcp_id);
                $stmt->bindParam(":ses", $ses);

                $stmt->execute();
            }


            //  $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array(
                "status" => 200,
                "message" => "",
                "data" => ""
            );
        }
        $res = json_encode ( $arr );

    }else{
        // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
        $arr = array (
            "status" => 500,
            "message" => "Error, No Profiles Found!."
        );

    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}


function sendEmailComplaintToAdminUsingPhpMailer($email, $message, $contact, $name)
{
    //echo"MAIL ";
    $mail = new PHPMailer;

    try{
//Tell PHPMailer to use SMTP
        $mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
        $mail->SMTPDebug = 2;
//Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';

// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
        $mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = "care.acian@gmail.com";
//Password to use for SMTP authentication
        $mail->Password = "Aspirehigh";
        //echo"MAIL USER ".$mail->Username;
        //Recipients
        $mail->setFrom('care.acian@gmail.com', 'ASPIRING CAREERS Support');
        $mail->addAddress('mehta.peeyush@gmail.com', 'Peeyush Mehta');     // Add a recipient
        $mail->addAddress('itsmesourav.freelancer@gmail.com','Sourav');               // Name is optional
        $mail->addAddress('care.acian@gmail.com', 'Peeyush Mehta');
        $mail->addAddress('msapra143@gmail.com', 'Peeyush Mehta');
        $mail->addReplyTo('care.acian@gmail.com', 'Reply');


        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $subject = "Feedback/Complaint";
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        $mail->clearAllRecipients();
        $mess =  'Message has been sent';
        echo $mess;

    } catch (Exception $e) {
        $mess =  'Message could not be sent. Mailer Error: '. $mail->ErrorInfo;
        echo $mess;
    }
    $arr = array (
        "status" => 200,
        "message" => "Email Successfully Sent",
        "data" => $mess
    );
}


function get_test_series() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT url FROM TestSeries WHERE id = $int_class";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}



function addChapterName() {
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['chapter_name'] )) {
            $chapter_name = ( $data ['chapter_name'] );
            $user_exists = checkChapterExictence ( $chapter_name);
            // $token = ( $data ['token'] );

            if ($user_exists == TRUE) {
                $arr = array (
                    "status" => 500,
                    "message" => "Already exists. Please Select from Dropdown menu",
                    "data" => array ()
                );
            } else {
                $sql = "INSERT INTO Chapters(chapter_name) VALUES (:chapter_name)";

                $db = getDB();
                $stmt = $db->prepare ( $sql );
                $stmt->bindParam(":chapter_name",$chapter_name);
                $stmt->execute();


                $arr = array (
                    "status" => 200,
                    "message" => "Successful!!",
                    "data" => ""
                );
                // updateDevice($data ['unique_id'],$mobile, $token);
            }
        } else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Registration Failed, Mandatory Field Missing. "
            );
        }

    }
    $arr1 = array (
        "response" => $arr
    );
    // header('Content-Type: application/json');
    echo json_encode ( $arr1 );
}

function get_CoursePdfs() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['class'] ))
        {

            $int_class =  $data ['class'];
            //$state = $data['status'];

            $sql = "SELECT url FROM CourseModule WHERE class = '$int_class'";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}


function get_Pdfs_course_preview() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT url FROM CourseModule WHERE code = '$int_class'";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}


function userProfileEmptyColumns() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['user_id'] )  ) {
            //$token = $data['token'];
            $user_id = $data['user_id'];


            $sql = "SELECT creation_date, ISNULL(name) + ISNULL(mobile) + ISNULL(email)+ ISNULL(class) + ISNULL(password) + ISNULL(session_year)+ ISNULL(address) + ISNULL(unique_id) + ISNULL(user_type)AS cnt FROM ACStudents WHERE id= '$user_id'";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }

}

function getChapterList(){

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['id'] )  ) {
            //$token = $data['token'];
            $user_id = $data['id'];


            $sql = "SELECT *FROM Chapters";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }

}

function checkChapterExictence( $chapter) {

    $sql = "SELECT COUNT(*) AS count FROM Chapters where chapter_name = '$chapter'";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}

function getQuizeeAnswers() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['id'] )  ) {
            //$token = $data['token'];
            $user_id = $data['id'];


            $sql = "SELECT *FROM Quizee_Details";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $announcement_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $announcement_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}
function insertStudentUniqueId() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['session'] )&&! empty ( $data ['class'] )) {

            $_class = $data ['class'];

            $milliseconds = round(microtime(true) * 1000);

            $ses = $data["session"];
            $time = time();
            for ($i = 1; $i < 50; $i++) {
                $randomString = substr(MD5(time()),3);
                $j = $i*39;
                //$rp = "AC".$randomString;
                $sub_mil = substr(time()*$j,0,3);
                $unique_id = "AC".$ses.$_class."_".$sub_mil;
                $sql = "INSERT INTO StuUnique(unique_id, session, class) VALUES(:unique_id, :session, :class)";

                $db = getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindParam(":unique_id", $unique_id);
                $stmt->bindParam(":session", $ses);
                $stmt->bindParam(":class", $_class);
                $stmt->execute();
            }

            //  $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array(
                "status" => 200,
                "message" => "",
                "data" => ""
            );
        }
        $res = json_encode ( $arr );

    }else{
        // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
        $arr = array (
            "status" => 500,
            "message" => "Error, No Profiles Found!."
        );

    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}


function getUniqueNos(){

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['id'] )  ) {
            //$token = $data['token'];
            $user_id = $data['id'];


            $sql = "SELECT *FROM StuUnique";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }

}
function getCurrentServerTime(){

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['user_id'] )  ) {
            //$token = $data['token'];
            $user_id = $data['user_id'];

            date_default_timezone_set('Asia/Kolkata');
            $currentTime = date('h:i:s A', time());
            $time = date("G:i", strtotime($currentTime));
            $array = array(
                "time" => $time
            );


            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $array
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "No ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }

}

function getUserNameAndPassword() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['user_id'] ))
        {

            $int_user =  $data ['user_id'];
            //$state = $data['status'];

            $sql = "SELECT name, email FROM ACStudents WHERE id = $int_user";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function deleteCoverImages() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_id =  $data ['id'];
            //$state = $data['status'];

            $sql = "Delete FROM HomeImages WHERE id = $int_id";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            // $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => ""
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}


function getCoverImages() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['user_id'] ))
        {

            $int_user =  $data ['user_id'];
            //$state = $data['status'];

            $sql = "SELECT id, url FROM HomeImages";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function getQuestionPaper() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['class_name'] ))
        {

            // $int_user =  $data ['user_id'];
            //$state = $data['status'];
            $class_name = ( $data ['class_name'] );
            $sql = "SELECT *FROM QuestionPaper WHERE class_name ='$class_name'";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}
function getHomeImages() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_user =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT *FROM  HomeImages";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function sendEmailPhpMailerToAsc($email){

    $name = "";
    $code = getOTP($email);
    //  echo "CODE ".$code;
    $subject = "Password Reset - Aspiring Careers";
    $message = "Hello $name,\n\n Hi, $email.\n\nPlease Enter this code: $code. to recreate Password!\n\n\n\nRegards,\nAspiring Careers Team.";
    $from = "resetpassword@aspcareers.com";
    $headers = "From:" . $from;
    sendEmailToAdminUsingPhpMailerToAsc($email,$subject, $message);
    //mail($email,$subject,$message,$headers);

}

function sendEmailToAdminUsingPhpMailerToAsc($email, $subject, $solution)
{

    $mail = new PHPMailer;

    try{
//Tell PHPMailer to use SMTP
        $mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
        $mail->SMTPDebug = 2;
//Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';

// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
        $mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = "care.acian@gmail.com";
//Password to use for SMTP authentication
        $mail->Password = "Aspirehigh";
        //  echo"MAIL USER ".$mail->Username;
        //Recipients
        $mail->setFrom('care.acian@gmail.com', 'ASPIRING CAREERS Support');
        $mail->addAddress('care.acian@gmail.com', 'ASPIRING CAREERS Support');     // Add a recipient
        // $mail->addAddress('care.acian@gmail.com');               // Name is optional
        $mail->addReplyTo('care.acian@gmail.com', 'ASPIRING CAREERS Support');


        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $solution;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        $mail->clearAllRecipients();
        // echo 'Message has been sent';
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}


function get_Qp_preview() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT url FROM QuestionPaper WHERE id = $int_class";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function checkUserExistenceInQuizee(){
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data['user_id'] ) ) {

            $user_id = ( $data['user_id'] );
            $mobile = '990990099';
            $user_exists = checkQuizeeExictenceById( $user_id, $mobile );


            $array = array(
                "flag" => $user_exists
            );

            if ($user_exists == TRUE) {

                $arr = array (
                    "status" => 200,
                    "message" => "User Registered for Quizee!",
                    "data" => $array
                );
            }
            else
            {

                $arr = array (
                    "status" => 500,
                    "message" => "Failed!",
                    "data" => $array
                );
            }
            $arr1 = array (
                "response" => $arr
            );

            echo json_encode ( $arr1 );

        }
    }
}


function get_all_the_attandance(){
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['class_name'] )  ) {
            //$token = $data['token'];
            // $user_id = $data['user_id'];


            $sql = "SELECT *FROM AllTestSeries";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }

}

function get_financial_data(){
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['unique_id'] )  ) {
            //$token = $data['token'];
            // $user_id = $data['user_id'];


            $sql = "SELECT *FROM FinancialData";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }

}
function get_TestResults(){
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['unique_id'] )  ) {
            //$token = $data['token'];
            $user_id = $data['unique_id'];


            $sql = "SELECT * FROM TestResultsSW WHERE UID = '$user_id' GROUP BY TestNo";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }

}
function get_Attendance(){
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['unique_id'] )&&!empty ( $data ['month'] )  ) {
            //$token = $data['token'];
            $user_id = $data['unique_id'];
            $month = $data['month'];

            $sql = "SELECT * FROM Attendance WHERE UID = '$user_id' AND Month = '$month'";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }

}

function getDemoVideos() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT name, url, duration, chapter FROM DemoVideoList";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function get_QuizeePdf_preview() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT url FROM QuizeeResults WHERE class = $int_class";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}
function get_QuizeePdf() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT * FROM QuizeeResults";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function download_QuizeePdfs() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT url FROM QuizeeResults WHERE id = $int_class";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function updateDevice($user_id,$email,$token){
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();
    if ($json != null) {
        $data = json_decode ( $json, true );

// 		if (!empty($data ['user_email']) && !empty($data['token'] )&& !empty($data['user_id']))
// 		{

        $check_token_exists_query = 'SELECT * FROM devices
										  WHERE email = :email';

        $email = $data ['email'];

        $stmt = $db->prepare ( $check_token_exists_query );
        $stmt->bindParam ( ":email", $email);

        $stmt->execute ();

        $existing_token_data = $stmt->fetchAll(PDO::FETCH_OBJ);
        $rt =  json_decode(json_encode($existing_token_data), true);

        if (empty($rt)){

            $insert = "INSERT INTO devices (user_id,email, token)
						   VALUES (:user_id,:email, :token)";
            $stmt = $db->prepare ( $insert );
            $stmt->bindParam ( ":email", $email);
            $stmt->bindParam ( ":token", $token );
            $stmt->bindParam(":user_id",$user_id);
            $stmt->execute ();
            $arr = array (

                "status" => 200,
                "message" => "Success",
                "data" => ""

            );
        }
        else {
            $stmt = $db->prepare("UPDATE devices SET token =:token WHERE email =:email");

            $stmt->bindParam(":email",$email);
            $stmt->bindParam(":token",$token);
            $result = $stmt->execute();
        }
    }
    //}

}

function sendPushNotifications() {
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if ( !empty ( $data ['token_id'] ) )
        {

            $sql2 = "Select id, name FROM ACStudents WHERE user_type = 'student'";
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



            }            //$patient_id = $assigned_details_json1[0]['patient_id'];
            //echo "PATIENT_ID".$patient_id;


            $type = "Video";

            $title = "Hello,".$row->name;
            $message = "A New Video Has Been Uploaded";
            sendPushMessage( $row->id, $title, $message);


            $arr = array (
                "status" => 200,
                "message" => "Posting Successful!!"
            );
        } else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Posting Failed, Mandatory Field Missing. "
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );
    header('Content-Type: application/json');
    echo json_encode ( $arr1 );
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
        $db1 = new DBFunctions();

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

    $sql = ("SELECT token FROM devices WHERE user_id = :user_id");
    $db = getDB ();
    $stmt = $db->prepare( $sql );
    $stmt->bindParam ( ":user_id", $user_id );
    $stmt->execute ();
    $result = $stmt->fetch();
    return $result['token'];
}
function checkUserExictenceEmail($email){


    $sql = "SELECT COUNT(*) AS count FROM ACStudents where email = '$email'";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;


}

function preview_SubjectModulePdfs() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['code'] ) &&! empty ( $data ['class_name'] ))
        {

            $int_class =  $data ['code'];
            $class_name = $data['class_name'];

            $sql = "SELECT id, url, class, class_name AS name,code FROM SubjectModule WHERE code = '$int_class' AND class_name = '$class_name'";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}
function getSubjectModulePreview() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT url FROM SubjectModule WHERE id = '$int_class'";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function findQRCode() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['qr_code'] )  ) {
            $qr_code = $data['qr_code'];


            $sql = "SELECT id, url,qr_code 
					FROM QRCodeList
					WHERE qr_code = '$qr_code'";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();



            $count = $stmt->rowCount ();
            if ($count == 1) {
                $login_details = $stmt->fetchAll ( PDO::FETCH_OBJ );
                $login_details_json = json_decode(json_encode($login_details), true);
                $arr = array (
                    "status" => 200,
                    "message" => "Successful!!",
                    "data" => array(
                        "qr_code" => $login_details_json[0]['qr_code'],
                        "url" => $login_details_json[0]['url']
                    )


                );
                $qr_code = $login_details_json[0]['qr_code'];
                $url = $login_details_json[0]['url'];
            }
            else {
                // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
                $arr = array (
                    "status" => 500,
                    "message" => "Failed ",
                    "data"=>"no"

                );
            }

        }

        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}

function add_Qr_Links() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['qr_code'] )&&! empty ( $data ['id_code'] )&&! empty ( $data ['url'] ))
        {

            $_qr_code =  $data ['qr_code'];
            $_id_code =  $data ['id_code'];
            $_id_url=  $data ['url'];


            $sql = "INSERT INTO QRCodeList(qr_code, id_code, url) VALUES(:qr_code, :id_code, :url)";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            $stmt->bindParam ( ":qr_code", $_qr_code );
            $stmt->bindParam ( ":id_code", $_id_code);
            $stmt->bindParam ( ":url", $_id_url );

            $stmt->execute ();
            //  $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" =>""
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}
function preview_TestSeries() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];


            $sql = "SELECT url FROM AllTestSeries WHERE id = '$int_class'";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function delete_announcements() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];


            $sql = "DELETE FROM Announcements WHERE id = '$int_class'";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function getAnnouncementList() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];
            //$state = $data['status'];

            $sql = "SELECT id, header ,date FROM Announcements";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $projects
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function convertUserToStudent() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();

    if ($json != null) {
        $data = json_decode($json, true);

        if (!empty ($data ['id']) && !empty ($data ['unique_id'])) {
            $_id = $data ['id'];
            $unique_id = $data ['unique_id'];
            $user_exists = checkIfUniqueIdExists($unique_id);
            $unique_id_assigned = checkIfUniqueIdAlreadyAssigned($unique_id);


            if ($user_exists == FALSE || $unique_id_assigned == TRUE) {
                // echo"INSIDE IF";
                $arr = array(
                    "status" => 500,
                    "message" => "Oops!Invalid Student ID!",
                    "data" => ""

                );
            } else {
                $sql = "UPDATE ACStudents SET unique_id = '$unique_id', user_type= 'Student' WHERE id = '$_id'";

                $db = getDB();
                $stmt = $db->prepare($sql);
                //$stmt->bindParam( ":unique_id",$data['name']);
                $stmt->execute();

                $arr = array(
                    "status" => 200,
                    "message" => "Profile upgraded to Student...",
                    "data" => ""
                );
                // updateDevice($data ['unique_id'],$mobile, $token);
            }
        } else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array(
                "status" => 500,
                "message" => "Registration Failed, Unique ID Missing. "
            );
        }
    }


    $arr1 = array (
        "response" => $arr
    );
    // header('Content-Type: application/json');
    echo json_encode ( $arr1 );

}

function checkIfUniqueIdExists($unique_id){
    $sql = "SELECT COUNT(*) AS count FROM StuUnique WHERE unique_id = '$unique_id'";
    // echo"SQL 1 ".$sql;
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}
function checkIfUniqueIdAlreadyAssigned($unique_id){
    $sql = "SELECT COUNT(*) AS count FROM ACStudents WHERE unique_id = '$unique_id' ";
    //echo"SQL 2 ".$sql;
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}
function getProfilePic() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['user_id'] )  ) {
            //$token = $data['token'];
            $user_id = $data['user_id'];


            $sql = "SELECT url FROM ProfilePic WHERE user_id = '$user_id'";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}

function deleteVideosFromList() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_id =  $data ['id'];
            //$state = $data['status'];

            $sql = "Delete FROM VideosList WHERE id = $int_id";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );

            $stmt->execute ();
            // $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => ""
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function sendCodeToVerifyEmailOnRegister(){
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();

    if ($json != null) {
        $data = json_decode($json, true);
        if (!empty ($data['email'])) {
            $email = $data['email'];
            $mail_result = sendEmailToVerify($email);

            $arr = array(
                "status" => 200,
                "message" => "An Email has been sent to You. Please check Inbox!",
                "data" => ''
            );


            $arr1 = array(
                "response" => $arr
            );

            echo json_encode($arr1);

        } else {
            $arr = array(
                "status" => 500,
                "message" => "Something went wrong!",
                "data" => ''
            );
        }
    }

}

function sendEmailToVerify($email){

    $name = "";
    $code = getOTPForEmail($email);
    //  echo "CODE ".$code;
    $subject = "Email Verification - Aspiring Careers";
    $message = "\n\n Hi, $email.\n\nPlease Enter this code: $code. to verify Your Email!\n\n\n\nRegards,\nAspiring Careers Team.";
    $from = "care.acian@gmail.com";
    $headers = "From:" . $from;

    sendEmailToAdminUsingPhpMailer($email,$subject, $message);
    //mail($email,$subject,$message,$headers);

}

function getOTPForEmail($email)
{
    generateOTPForEmail($email);
    $sql = "SELECT code FROM OTPEmail where email = '$email'";
    $db = getDB ();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $otp = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $otp_json = json_decode(json_encode($otp), true);



    return (int)$otp_json[0]['code'];



}

function verifyOTPForEmail(){

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();
    $ret =0;
    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['code'] ) && ! empty ( $data ['email'] )){

            $otpMatches = matchOTPForEmail($data ['code'] , $data ['email'] );
            $email = $data ['email'];
            if ($otpMatches== TRUE) {
                $sql = "UPDATE ACStudents SET status = 'Active' WHERE email = '$email'";

                $db = getDB ();


                $stmt = $db->prepare ( $sql );
                //$stmt->bindParam ( ":email", $email);
                //$stmt->bindParam ( ":code", $code);
                $stmt->execute ();
                $arr = array (
                    "status" => 200,
                    "message" => "Success.",
                    "data" => array ()
                );
            }
            else
            {
                $arr = array (
                    "status" => 500,
                    "message" => "Failed! Please Enter Valid Code",
                    "data" => array ()
                );
            }
            $arr1 = array (
                "response" => $arr
            );

            echo json_encode ( $arr1 );
        }
    }
}
function matchOTPForEmail($code, $email){

    $sql = "SELECT code FROM OTPEmail where email = '$email'";

    $db = getDB ();


    $stmt = $db->prepare ( $sql );
    //$stmt->bindParam ( ":email", $email);
    //$stmt->bindParam ( ":code", $code);
    $stmt->execute ();
    $user_code = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_code_json = json_decode(json_encode($user_code), true);

    if ($code == (int)$user_code_json[0]['code']) {

        return true;
    }

    return false;
}

function checkUserActivationStatus(){
    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data['email'] ) ) {

            $email = ( $data['email'] );
            $user_exists = checkUserStatus( $email );


            $array = array(
                "flag" => $user_exists
            );

            if ($user_exists == TRUE) {

                $arr = array (
                    "status" => 200,
                    "message" => "User InActive!",
                    "data" => $array
                );
            }
            else
            {

                $arr = array (
                    "status" => 500,
                    "message" => "Failed!",
                    "data" => $array
                );
            }
            $arr1 = array (
                "response" => $arr
            );

            echo json_encode ( $arr1 );

        }
    }
}
function checkUserStatus($email) {
    $sql = "SELECT COUNT(*) AS count FROM ACStudents where email = '$email' AND status='InActive'";
    $db = getDB();
    $stmt = $db->prepare ( $sql );
    $stmt->execute ();
    $user_count = $stmt->fetchAll ( PDO::FETCH_OBJ );
    $user_count_json = json_decode(json_encode($user_count), true);

    if ((int)$user_count_json[0]['count'] > 0) {

        return true;
    }

    return false;

}


function generateOTPForEmail($email)
{

    $app = \Slim\Slim::getInstance();
    $json = $app->request->getBody();
    $arr = array();
    $db = getDB();

    if ($json != null) {
        $data = json_decode($json, true);


        $code = rand(100000, 999999);
        $check_otp_exists_query = 'SELECT * FROM OTPEmail WHERE email=:email';

        $email = $data ['email'];

        $stmt = $db->prepare($check_otp_exists_query);
        $stmt->bindParam(":email", $data ['email']);

        $stmt->execute();

        $existing_otp_data = $stmt->fetchAll(PDO::FETCH_OBJ);
        $rt = json_decode(json_encode($existing_otp_data), true);
        if (empty($rt)) {
            $sql = "INSERT INTO OTPEmail (email, code) VALUES (:email, $code)";

            $db = getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":email", $data ['email']);


            $stmt->execute();

        } else {

            $update = "UPDATE OTPEmail SET code = :code WHERE email = :email";
            $stmt = $db->prepare($update);
            $stmt->bindParam(":code", $code);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

        }
    }
}


function send_a_comment_on_video(){

    $app = \Slim\Slim::getInstance();
    $json = $app->request->getBody();
    $arr = array();

    if($json!=null) {
        $data = json_decode($json, true);

        if (!empty($data['comment']) && !empty($data['provider']) && !empty($data['video_id'])) {

            $int_provider_id = (int)($data['provider']);
            $int_receiver_id = (int)($data['video_id']);


            date_default_timezone_set('Asia/Kolkata');
            $new_date = date("Y-m-d H:i:s", time());


            $sql = "INSERT INTO  Comments( comment, provider, video_id, date) VALUES ( :comment, :provider,  :video_id, '$new_date')";

            $db = getDB();
            $stmt = $db->prepare($sql);
            //$stmt->bindParam(":project_id", $data['project_id']);
            $stmt->bindParam(":comment", $data['comment']);
            $stmt->bindParam(":provider", $int_provider_id);
            $stmt->bindParam(":video_id", $int_receiver_id);


            $stmt->execute();


            $arr = array("status" => 200, "message" => "Posting Successful!!");
        } else {
            //$res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array("status" => 500, "message" => "Posting Failed, Mandatory Field Missing. ");
        }

        $arr1 = array("response" => $arr);

        echo json_encode($arr1);
    }
}
function get_the_comments_on_videos(){
    $app = \Slim\Slim::getInstance();
    $json = $app->request->getBody();
    $arr = array();

    if($json!=null)
    {
        $data = json_decode($json,true);
        if(!empty($data['video_id']) )


        {
            $int_video_id = (int)($data['video_id']);
            $sql =  "SELECT C.date, C.comment, p.url FROM Comments C LEFT JOIN ProfilePic p ON C.provider = p.user_id where video_id = $int_video_id";


            $db = getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindParam(":user_id", $int_video_id);

            $stmt ->execute();

            $url_name =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array("status" => 200, "message" => "Posting Successful!!","data"=>$url_name);
        }
        else{
            //$res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array("status" => 500, "message" => "Posting Failed, Mandatory Field Missing. " );
        }
    }
    $arr1 = array("response" => $arr);

    echo json_encode($arr1);
}

function delete_subject_modules() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];


            $sql = "DELETE FROM SubjectModule WHERE id = '$int_class'";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            // $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => ""
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}

function getSubjectModules() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['id'] )  ) {
            //$token = $data['token'];
            // $user_id = $data['class_id'];


            $sql = "SELECT *FROM SubjectModule";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $announcement_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $announcement_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}


function rename_subject_modules() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] )&&! empty ( $data ['name'] ))
        {

            $int_class =  $data ['id'];


            $sql = "UPDATE SubjectModule SET name = :name WHERE id = '$int_class'";

            $db = getDB ();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt->execute ();
            // $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => ""
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}
function getAllTestSeriesForPreview() {

    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();


    if ($json != null) {

        $data = json_decode ( $json, true );



        if (!empty ( $data ['class_name'] )  ) {
            //$token = $data['token'];
            // $user_id = $data['user_id'];

            $class_name = ( $data ['class_name'] );
            $sql = "SELECT *FROM AllTestSeries";


            $db = getDB();
            $stmt = $db->prepare ( $sql );
            // $stmt->bindParam ( ":unique_id", $unique_id );
            $stmt->execute ();
            $my_profile_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => $my_profile_details
            );

            $res = json_encode ( $arr );

        }
        else {
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Login Failed, wrong Email Id or Password. ",

            );
        }



        $arr1 = array (
            "response" => $arr
        );
        // header('Content-Type: application/json');
        echo json_encode ( $arr1 );
    }
}
function delete_test_series() {


    $app = \Slim\Slim::getInstance ();
    $json = $app->request->getBody ();
    $arr = array ();
    $db = getDB();

    if ($json != null) {
        $data = json_decode ( $json, true );
        if (! empty ( $data ['id'] ))
        {

            $int_class =  $data ['id'];

            /* $sql = "SELECT url FROM AllTestSeries WHERE id = '$int_class'";

             $db = getDB ();
             $stmt = $db->prepare ( $sql );
             // $stmt->bindParam ( ":user_id", $int_user_id);
             //$stmt->bindParam ( ":state", $state);
             $stmt->execute ();

             $registration_details =  $stmt->fetchAll ( PDO::FETCH_OBJ );
             $registration_details_json = json_decode(json_encode($registration_details), true);
             $registered_data = array (
                 "path" => $registration_details_json[0]['url'],
             );
             $path = $registration_details_json[0]['url'];
             unlink($path);*/

            $sql = "DELETE FROM AllTestSeries WHERE id = '$int_class'";

            $db1 = getDB ();
            $stmt1 = $db1->prepare ( $sql );
            // $stmt->bindParam ( ":user_id", $int_user_id);
            //$stmt->bindParam ( ":state", $state);
            $stmt1->execute ();
            // $projects = $stmt->fetchAll ( PDO::FETCH_OBJ );
            $arr = array (
                "status" => 200,
                "message" => "",
                "data" => ""
            );

            $res = json_encode ( $arr );

        }else{
            // $res = '{"status" : 500, "message" : "Database transaction failed in the server. "' . $e->getMessage() . '}';
            $arr = array (
                "status" => 500,
                "message" => "Error, No Profiles Found!."
            );
        }
    }
    $arr1 = array (
        "response" => $arr
    );

    echo json_encode ( $arr1 );
}



?>