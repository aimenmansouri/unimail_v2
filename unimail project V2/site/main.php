<?php
session_start();
include 'functions.php';
include 'connect.php';

if(!isset($_SESSION['S_type']))
header('Location: login.php');



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

  <body style="margin-bottom: 50px;">
    <!-- nav bar -->
  <nav class="navbar fixed-top navbar-expand-lg shadow-sm " id="my-nav-bar">
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

        
        <li class="nav-item">
          <a class="nav-link <?php setPubState();?>" href="create-post.php" tabindex="-1" aria-disabled="true">Publish</a>
        </li>

        
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

<?php wating_approval(); ?>

<br>
<!-- announcments section-->

<div class="container">
  <div class="row">
    <div class="col-md-9 ps-4">
      <div class="form-check">
      <form method="get" action="<?php echo $_SERVER['PHP_SELF'];?>" id="show_my_class">
        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" name="showmyclass" onchange=" this.form.submit()" <?php if(isset($_GET['showmyclass']))echo 'checked';?>>
        <label class="form-check-label" for="flexCheckDefault">
          Show only my class
        </label>
        </form>
      </div>
    </div>
    <div class="col text-muted text-end pe-4" id="date">
        <p id="date"><script>
        var date = new Date();
        document.getElementById("date").innerHTML = date.toDateString() ;
        </script></p>
      </div>
  </div>

  <div class="row"><div class="col"><br></div></div>
  
  <!--fixed-->

  <?php


if(!isset($_GET['showmyclass'])){
    $stmt_getfixed = $db->prepare
    ("SELECT id 
    FROM posts 
    WHERE fixed = 1");

    $stmt_getfixed->execute();
    $rows = $stmt_getfixed->fetchAll();


    if(count($rows)==0)
    {
      echo '
      <div class="row">
        <div class="col" Style = "text-align: center;"> <p class="fs-2 text-muted"> No posts.</p> </div>
      </div>
      ';
    }
    else
    {
      for($a=count($rows)-1 ; $a>=0 ; $a-- )
      {
        showann($rows[$a][0]);
      }
    }
  }
  else{

    $stmt_getfixed = $db->prepare
  ("SELECT id , class
  FROM posts 
  WHERE fixed = 1 AND (groupe = ? OR groupe = 0)");

  $stmt_getfixed->execute(array($_SESSION['S_groupe']));
    $rows = $stmt_getfixed->fetchAll();


    if(count($rows)==0)
    {
      echo '
      <div class="row">
        <div class="col" Style = "text-align: center;"> <p class="fs-2 text-muted"> No posts.</p> </div>
      </div>
      ';
    }
    else
    {
      for($a=count($rows)-1 ; $a>=0 ; $a-- )
      {
        showann($rows[$a][0]);
      }
    }

  }
    

  ?>

<hr>

<!--none fixed-->

<?php

if(!isset($_GET['showmyclass']))
{

  $stmt_getunfixed = $db->prepare
  ("SELECT id 
  FROM posts 
  WHERE fixed = 0");

  $stmt_getunfixed->execute();
  $rows = $stmt_getunfixed->fetchAll();

  if(count($rows)==0)
  {
    echo '
    <div class="row">
      <div class="col" Style = "text-align: center;"> <p class="fs-2 text-muted"> No posts.</p> </div>
    </div>
    ';
  }
  else
  {
    for($a=count($rows)-1 ; $a>=0 ; $a-- )
    {
      showann($rows[$a][0]);
    }
  }


}
else
{

  $stmt_getunfixed = $db->prepare
  ("SELECT id , class
  FROM posts 
  WHERE fixed = 0 AND (groupe = ? OR groupe = 0)");

  $stmt_getunfixed->execute(array($_SESSION['S_groupe']));
  $rows = $stmt_getunfixed->fetchAll();

  if(count($rows)==0)
  {
    echo '
    <div class="row">
      <div class="col" Style = "text-align: center;"> <p class="fs-2 text-muted"> No posts.</p> </div>
    </div>
    ';
  }
  else
  {
    for($a=count($rows)-1 ; $a>=0 ; $a-- )
    {
      showann($rows[$a][0]);
    }
  }

}

?>
  
</div>



<!--footer -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
  </body>
</html>