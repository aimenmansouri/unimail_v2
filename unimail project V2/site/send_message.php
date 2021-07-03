<?php
session_start();
include 'connect.php';
$response = '';
if(isset($_POST['other_id']))
{
		$other_id = $_POST['other_id'];
    $my_id = $_SESSION['S_id'];
    $message = $_POST['contexts'];


    $reg_stmt = $db->prepare('
    INSERT INTO msgs(send,res,contexts) 
    VALUES(?,?,?)');

    $reg_stmt->execute(array($my_id,$other_id,$message));

    $response .= '
    <div class="row ">
      <div class="col-10 offset-2 mb-2  text-end ">
        <div class="rounded-3 send" style="white-space: pre-wrap ; text-align: left;"><div>'.$message.'<br><p class ="text-muted">now</p></div></div>
      </div>
    </div>
    ';

  exit($response);
}
?>