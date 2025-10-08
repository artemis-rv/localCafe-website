<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>password verify</title>
</head>
<body>
    <h1>Password verifier</h1>

    <form method="post">
        <input type="text" placeholder="enter the hash" name="hashed">
        <input type="submit" name="check">
    </form>



    <?php
    if(isset($_POST["check"])){
        $hashedPassword = $_POST["hashed"];
        $flag=0;

        for($i= 0;$i<999999;$i++){

            $try=password_verify($i,$hashedPassword);
            if($try== 1){
                $flag= 1;
                echo"<p>Password is: $i";
                break;
            }
        }
    }

    ?>
</body>
</html>