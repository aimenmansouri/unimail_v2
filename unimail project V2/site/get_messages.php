<?php
session_start();
include 'connect.php';
	if (isset($_POST['id'])) {
		$conn = new mysqli('localhost', 'root', '', 'unimaildb');

		$other_id = $conn->real_escape_string($_POST['id']);
        $my_id = $conn->real_escape_string($_SESSION['S_id']);

		$sql = $conn->query("SELECT * FROM `msgs` WHERE (`send` = $my_id OR res = $my_id) && (`send` = $other_id OR res = $other_id)");

		if ($sql->num_rows > 0) {

			$response = "";

			while($data = $sql->fetch_array()) {
                if($data['send']==$my_id)
                {
                    $response .= '
                    <div class="row ">
                      <div class="col-10 offset-2 mb-2  text-end ">
                        <div class="rounded-3 send" style="white-space: pre-wrap ; text-align: left;"><div>'.$data['contexts'].'<br><p class ="text-muted">'.substr($data['date'], 0, 16).'</p></div></div>
                      </div>
                    </div>
                    ';
                }
                else
                {
                    $response .= '
                    <div class="row ">
                    <div class="col-10 mb-2 ">
                      <div class="rounded-3 receive" style="white-space: pre-wrap"> <div>'.$data['contexts'].'<br><p class ="text-muted">'.substr($data['date'], 0, 16).'</p></div></div>
                    </div>
                  </div>
                    ';
                }
			}

			exit($response);
		} else
			exit('<p class = "text-center text-muted">No meesages.</p>');
	}
?>