
function validateLogin(){
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value.trim();

    if(username === "" || password === ""){
        alert("Please fill in all fields.");
        return false;
    }



    return true;
}





//Login response
document.getElementById('login').addEventListener('submit', function(e){
  e.preventDefault();     //prevent default form submission

  console.log('Form submitted'); // Debug log

  // First validate the form 
  if(!validateLogin()) { 
    console.log('Validation failed'); // Debug log
    return false;
  }

  var form=e.target;    //Gets the form element that triggered the event.
  var formData=new FormData(form);    //Creates a FormData object containing all form fields and values.

  // Add the loginn parameter to match what login.php expects
  formData.append('loginn', '1');

  console.log('Sending data to login.php'); // Debug log

  // Sends the form data to login.php using a POST request
  fetch('login.php',{method: 'POST', body: formData}).then(response => {
    console.log('Response received:', response.status); // Debug log
    return response.text();
  }).then(data=>{
    console.log('Response data:', data); // Debug log

    if(data=='login successfull')
    {
      console.log('Login successful, redirecting...'); // Debug log
      window.location.href="index(24dce).html";
    }
    else{
      console.log('Login failed:', data); // Debug log
      document.getElementById('login-message').innerHTML=data;
    }
  }).catch(error => {
    console.error('Error:', error); // Debug log
    document.getElementById('login-message').innerHTML='An error occurred during login.';
  });


})




//logout function




// ----------------------
// Register validation
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const phoneRegex = /^\d{10}$/;
// ----------------------
function validateRegister() {
  const email = document.getElementById('registerEmail').value.trim();
  const phone = document.getElementById('registerPhone').value.trim();
  const password = document.getElementById('registerPassword').value;
  const confirm = document.getElementById('registerConfirm').value;


  if (!emailRegex.test(email)) {
    alert('Please enter a valid email address.');
    return false;
  }

  if (!phoneRegex.test(phone)) {
    alert('Please enter a valid 10-digit phone number.');
    return false;
  }

  if (password.length < 6) {
    alert('Password should be at least 6 characters long.');
    return false;
  }

  if (password !== confirm) {
    alert('Passwords do not match.');
    return false;
  }

  alert('Registration successful!');
  return true;
}
