<?php
session_start();
include 'functions.php';
include 'connect.php';

if(!isset($_SESSION['S_type']))
header('Location: login.php');

$conn = new mysqli('localhost', 'root', '', 'unimaildb');

$my_id = $conn->real_escape_string($_SESSION['S_id']);
$my_groupe = $conn->real_escape_string($_SESSION['S_groupe']);

if($_SESSION['S_type']<2)
{
  $sql = $conn->query("SELECT * FROM `memb` WHERE `memb`.`id` != $my_id ORDER BY `memb`.`type` ASC");
}
else if($_SESSION['S_type']==2)
{
  $sql = $conn->query("SELECT * FROM `memb` WHERE  (`memb`.`id` != $my_id) AND (  (`memb`.`type`<2) || ((`memb`.`type` = 3) && (`memb`.`groupe` = $my_groupe))  )  ORDER BY `memb`.`type` ASC");
}
else
{
  $sql = $conn->query("SELECT * FROM `memb` WHERE  (`memb`.`id` != $my_id) AND (  (`memb`.`type`<2) || ((`memb`.`type` = 2) && (`memb`.`groupe` = $my_groupe))  )  ORDER BY `memb`.`type` ASC");
}

  $membs = "";

  while($data = $sql->fetch_array()) {

    if ($data['groupe']==0){
    if($data['type']<2)
    $groupe = ' Administration';
    else
    $groupe = ' Administration "unconfirmed account"';
    }
    else
    {
      if($data['type']==2)
      $groupe = ' G'.$data['groupe'].' group administrator';
      else
      $groupe = ' G'.$data['groupe'];
    }

    if ($data['type']==0)
    $site_role = ' - Site admin';
    else
    $site_role = '';

    // get laste message and it date
    $memb_id = $conn->real_escape_string($data['id']);
    $sql2 = $conn->query("SELECT * FROM `msgs` WHERE (`send` = $my_id OR res = $my_id) AND (`send` = $memb_id OR res = $memb_id) ORDER BY `msgs`.`date` DESC");

    $data2 = $sql2->fetch_array();
    if ($sql2->num_rows == 0) {
      $text = 'No messages.';
      $date = '';
      }
      else
      {
        if($data2['send']==$_SESSION['S_id'])
        {
          $text = 'you : ';
        }
        else
        {
          $text = $data['name'].' : ';
        }
        $text .= $data2['contexts'];
        $date = substr($data2['date'], 0, 16);
        if(strlen($text) >40)
        {
          $text = substr($data2['contexts'],0,40).' .....';
        }
      }
    $membs .='
    <div class="row memb" onclick="get_messages('.$data['id'].')" style="cursor: pointer;" id ="'.$data['id'].'">
    <div class=" col-md-4 offset-md-8 rounded border-bottom chat_user" style="padding-top: 1rem;">
    <h6 class ="other_name"style="font-weight: bold;">'.$data['name'].' '.$data['surname'].'<a class="text-muted other_groupe" style="font-size: 85%;font-weight: 400;text-decoration: none;">'.$groupe.' '.$site_role.'</a></h6> 
    <div class="row">
    <div class="col">
    <p class="text-muted">'.$text.'</p> 
    </div>
    <div class="col-4">
    <p class="text-end text-muted">'.$date.'</p>  
    </div>
  </div>
</div>
</div>
    ';
  }


?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!--styles-->
  <link rel="stylesheet" href="styles/bootstrap/bootstrap.css">
  <link rel="stylesheet" href="styles/style.css">
  <!--font poopins -->
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap">

  <title>Uni Mail</title>
  <link rel="icon" href="res/unimail_icon.png"> 

</head>

  <body style="margin-bottom: 0px;">
    <!-- nav bar -->
  <nav class="navbar fixed-top navbar-expand-md shadow-sm " id="my-nav-bar">
  <div class="container-fluid">
  
    <a class="navbar-brand" id="my-brand" href="main.php">Uni Mail</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon " id="burger-menu">
      
      <!-- navbar berguer menu icon -->

      <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
</svg>
      
      </span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">

    <ul class="navbar-nav me-auto mb-2 mb-lg-0">

    <li class="nav-item">
         <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="main.php">Announcements</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="discussion.php">Discussion</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active text-success" style="font-weight: 700;" aria-current="page" href="chat.php">Chat</a>
        </li>
      </ul>


      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

        
        <li class="nav-item dropdown ms-auto"><a class="nav-link pe-0" id="userInfo" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span style="font-weight: 550;"><?php  echoName(); echoSurname();?></span></a>
            <div class="dropdown-menu dropdown-menu-end " aria-labelledby="userInfo">
              <div class="dropdown-header text-gray-700">
                <h6 class="text-uppercase font-weight-bold"><?php  echoName(); echoSurname();?></h6><small> <?php groupe(); ?></small>
              </div>
              <div class="dropdown-divider"></div><a class="dropdown-item" href="myposts.php">My posts</a>
              <div class="dropdown-divider"></div><a class="dropdown-item" href="settings.php">Settings</a>
              <div class="dropdown-divider"></div><a class="dropdown-item" href="contact.php">Contact us</a>
              <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="logout.php">Logout</a>
            </div>
          </li>
      </ul>
    </div>
  </div>
</nav>
<div class="navmarg"></div>
<br>


<div class="container-fluid ">
<!--
  chat memb
    <div class="row memb2" onclick="location.href='#';" style="cursor: pointer;">
        <div class=" col-md-4 offset-md-8 rounded border-bottom chat_user" style="padding-top: 1rem;">
                <h6 style="font-weight: bold;">aimen mansouri <a class="text-muted" style="font-size: small;"> | G7</a></h6> 
                <div class="row">
                <div class="col">
                <p class="text-muted">Quickly design and customize ...</p> 
                </div>
                <div class="col-4">
                <p class="text-end text-muted">10/2/2020</p>  
                </div>
              </div>
        </div>
    </div>
-->
    <?php 
    echo $membs;
    ?>

</div>

<!--footer -->


<button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#chat" id="toggle_offcanves" hidden ></button>


<div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-keyboard="false" data-bs-backdrop="false" tabindex="-1" id="chat" aria-labelledby="offcanvasScrollingLabel" style="margin-top: 57px;" shown.bs.offcanvas>
  <div class="offcanvas-header border-bottom" style="background-color: #cfe2ff;">
    <h6 class="offcanvas-title" id="chat_title" style="font-weight: bold;">aimen mansouri</h6>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" id = "close_chat"></button>
  </div>

  <div class="offcanvas-body" id="messages_div">
  
    <!--sended message html -->
        <div class="containter messages_cont" id="messages_div" style="padding: 10px;">
              <div class="row ">
                <div class="col-10 offset-2 mb-2  text-end ">
                  <div class="rounded-3 send"><div> sended</div></div>
                </div>
              </div>
    <!--received message html-->
              <div class="row ">
                <div class="col-10 mb-2 ">
                  <div class="rounded-3 receive"> <div>receved</div></div>
                </div>
              </div>
        </div>
  </div>
            <form class="form-floating" style="margin-left: 10px; margin-right: 10px;">

            <div class="input-group mb-3">                
              <textarea name = "contexts" class="form-control"  id="msg" style="height: 100px ; resize : none ; padding-top : 10px ;"></textarea>
              <button type="submit" class="btn btn-primary">Send</button>
            </div>
                <input name="other_id" value="1" hidden id="form_other_id">  
            </form>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>



  <script text="text/javascript">

  $("#toggle_offcanves").click();
  $(".memb")[0].click();

  function get_messages(id)
  {
    if(screen.width<=767)
    {
      $("#toggle_offcanves").click();
    }
    $("#chat_title").html("");
    var chat_header = document.getElementById(id).getElementsByClassName("other_name")[0].innerHTML;
    $("#chat_title").append(chat_header);
    $("#form_other_id").val(id);
    getData(id);
  }
  function getData(id) {
                $.ajax({
                   url: 'get_messages.php',
                   method: 'POST',
                   dataType: 'text',
                   data: {
                       id : id,
                   },
                   success: function(response) {
                            $(".messages_cont").html("");
                            $(".messages_cont").append(response);
                            scrollDown();
                    }
                });
            }

  </script>


<script text="text/javascript">

	var scrollDown = function(){
        let chatBox = document.getElementById('messages_div');
        chatBox.scrollTop = chatBox.scrollHeight;
	}

$("form").on( "submit", function(e) {
 
 var dataString = $(this).serialize();
if($("#msg").val().trim().length >= 1){
 $.ajax({
   type: "POST",
   url: "send_message.php",
   data: dataString,
   success: function (response) {
     $(".messages_cont").append(response);
     $("#msg").val('');
     scrollDown();
   }
 });
}
else
{
  alert("message is empty")
}
 e.preventDefault();
});


</script>

  </body>
</html>
