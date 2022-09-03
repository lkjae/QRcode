<?php 
    error_reporting(E_ALL); 
    ini_set('display_errors',1); 
    include('dbcon.php');
    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {
        // 안드로이드 코드의 postParameters 변수에 적어준 이름을 가지고 값을 전달받습니다.
        $name=$_POST['name'];
        $country=$_POST['country'];
        $phone=$_POST['phone'];
	 $etc=$_POST['etc'];
        if(empty($name)){
            $errMSG = "이름을 입력하세요.";
        }
        else if(empty($country)){
            $errMSG = "비밀번호를 입력하세요.";
        }
        else if(empty($phone)){
            $errMSG = "폰번호를입력하세요.";
        }
        if(!isset($errMSG)) // 모든 입력값이 입력 되었다면
        {
            try{
                // SQL문을 실행하여 데이터를 MySQL 서버의 qr 테이블에 저장합니다. 
                $stmt = $con->prepare('INSERT INTO qr(제품명,수량, 가격, 기타) VALUES(:name, :country, :phone, :etc)');
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':country', $country);
                $stmt->bindParam(':phone', $phone);
		  $stmt->bindParam(':etc', $etc);
                if($stmt->execute())
                {
                    $successMSG = "새로운 사용자를 추가했습니다.";
                }
                else
                {
                    $errMSG = "사용자 추가 에러";
                }
            } catch(PDOException $e) {
                die("Database error: " . $e->getMessage()); 
            }
        }
    }
?>
<?php 
    if (isset($errMSG)) echo $errMSG;
    if (isset($successMSG)) echo $successMSG;
	$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
   
    if( !$android )
    {
?>
    <html>
       <body>
            <form action="<?php $_PHP_SELF ?>" method="POST">
                제품명: <input type = "text" name = "name" />
                수량: <input type = "text" name = "country" />
                가격: <input type = "text" name = "phone" />
		기타: <input type = "text" name = "etc" />
                <input type = "submit" name = "submit" />
            </form>
       
       </body>
    </html>
<?php 
    }
?>
