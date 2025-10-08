<?php

if($_SERVER["REQUEST_METHOD"] == "POST") {
  // Process the registration form
  $username = $_POST["username"];
  $email=trim($_POST["email"]);
  $phone=trim($_POST["phone"]);
  $password=trim($_POST["password"]);
  $confirm=trim($_POST["confirm_password"]);

  if(empty($email) || empty($phone) || empty($password) || empty($confirm)) {
    die("All fields are required.");
    
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format!");
    }

    if ($password !== $confirm) {
        die("Passwords do not match!");
    }

    // ✅ Hash password (important for security)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Store in a text/JSON file (temporary DB for now)
    $user = [
        "username"=> $username,
        "email" => $email,
        "phone" => $phone,
        "password" => $hashedPassword,
        // "normalPassword" => $password
    ];

    //json store
    $file = 'users.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
    } else {
        $data = [];
    }



    /* --------------------------------------//php file store*/
    
    $phpFile=fopen("users.php","a");
    fwrite($phpFile, "<?php //Username: $username | Password: $password ?> \n");
    fclose($phpFile); 
    /*--------------------------------------------------------------- */



    //------------------------------------------------------DB connection
    
    $conn=mysqli_connect('localhost','root','','localcafee');
    // $sql='Insert into registered_users values();';
    if(mysqli_connect_error()){
        die('Error connecting database: '. mysqli_connect_error());
    }
    else{

        /* - The ? is a placeholder, not part of the SQL string.
            - bind_param() safely escapes the input.
            - The database treats the entire string */

        $stmt=$conn->prepare("INSERT INTO registered_users (name,email,password,phone) VALUES(?,?,?,?)");
        $stmt->bind_param("ssss",$username,$email,$hashedPassword,$phone);
        $stmt->execute();
        $stmt->close();
        $conn->close();

    }

    /*------------------------------------------------------------------------------------------------------------*/

    // Add new user
    $data[] = $user;

    // Save back to file
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

    // echo "Registration successful for: " . htmlspecialchars($email);
    // echo "<br><a href='login.html#Login'>Click here to Login</a>";

    //redirect to home page
    header("Location: index(24dce).html");

}

?>