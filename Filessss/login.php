<?php 

    //check username in database ------------------------ done
    //to start session and cookies ------------------------- done
    //if exists, home page + avatar+logout button
    //if not, then redirect to register page
    //for order and subscription, login compulsory


    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['loginn'])){
        
        $name=$_POST['UserLogin'];
        $passwd=$_POST['passLogin'];


        /*-----------------------------JSON file checker---------------------*/
        // $file="users.json";
        // if(file_exists($file)){
        //     $contents=json_decode(file_get_contents($file), true);
        // }
        
        // $found=false;

        // $login_successful = false;
        
        // foreach($contents as $user){
        //     if($user['username']===$name){
        //         $found=true;
        //         if(password_verify($passwd,$user['password'])){
        //             $login_successful = true;
        //             // session_start();
        //             // $_SESSION['username']=$user['username'];
        //             // setcookie("username", $_SESSION['username'], time() + 3600, "/");
        //             // echo'login successfull';
        //             // exit;
        //         }
        //         else{
        //             echo "<p>Incorrect username or password</p>";
        //             exit;
        //         }
        //     }
        // }

        // if(!$found){
        //     echo "<p>Incorrect username or password</p>";
        //     }
        
        /*------------------------------------------------------------------------------------------------*/


        /*--------------------------------------DB checker--------------------------------------*/
        $conn = mysqli_connect("localhost","root","","localcafee");
        if(!$conn){
            die("error connecting database: ".mysqli_connect_error());
        }
        else{
            $stmt=$conn->prepare("SELECT name, password FROM registered_users WHERE name = ? LIMIT 1");
            if (!$stmt) {
                echo "<p>Database error: Unable to prepare statement.</p>";
                exit;
            }

            $stmt->bind_param("s",$name);
            $stmt->execute();                            //or conn->query($sql)



            $result=$stmt->get_result();                  

            if($result->num_rows>0){
                $row=$result->fetch_assoc();
                // Use the correct variable for supplied password
                if(password_verify($passwd, $row['password'])){
                    $login_successful = true;
                    session_start();
                    $_SESSION['username']=$row['name'];
                    setcookie("username", $_SESSION['username'], time() + 3600, "/");
                    echo'login successfull';
                    exit;
                }
                else{
                    echo "<p>Incorrect username or password</p>";
                    exit;
                }

            }
            else {
                echo "<p>Incorrect username or password</p>";
                exit;
            }



        }


        

    }


    

?>