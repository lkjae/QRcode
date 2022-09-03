<?php
  error_reporting(E_ALL);
  ini_set('display_errors',1);
  
  include('dbcon.php');

  $stmt = $con -> prepare('select * from qr');
  $stmt -> execute();

  if($stmt-> rowCount() > 0)
  {
    $data = array();
    
    while($row = $stmt -> fetch(PD0::FETCH_ASSOC))
    {
        extract($row);
        array_push($data,
                   array('제품명'=>$제품명,
                         '수량'=>$수량,
                         '가격'=>$가격
          ));
    }
    
    header('Content-Type: application/json; charset=utf8');
    $json = json_encode(array("qr_code"=>$data), JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
    echo $json;
  }
?>
