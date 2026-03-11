var wrongCount = 0;

function getEmail() {
  return document.getElementById("email").value;
}

function getPassword() {
  return document.getElementById("password").value;
}

function collectFormData() {
  var email = getEmail();
  var password = getPassword();

  document.getElementById("emailErr").innerHTML = "";
  var pwErr = document.getElementById("passwordErr");
  pwErr.innerHTML = "";

  var countDiv = document.getElementById("count");

  var hasError = false;

  if (email === "") {
    document.getElementById("emailErr").innerHTML = "Email is required";
    hasError = true;
  } else if (email.indexOf("@") === -1) {
    document.getElementById("emailErr").innerHTML = "Email must contain @";
    hasError = true;
  }

  if (password === "") {
    pwErr.innerHTML = "Password is required";
    hasError = true;
  } else {
    if (password.length < 6) {
      pwErr.innerHTML = "Password must be at least 6 characters";
      hasError = true;
    }
    if (password.indexOf("#") === -1) {
      if (pwErr.innerHTML !== "") {
        pwErr.innerHTML += "<br>";
      }
      pwErr.innerHTML += "Password must contain #";
      hasError = true;
    }
  }

  if (hasError) {
    wrongCount = wrongCount + 1;
    countDiv.innerHTML = "Wrong submissions: " + wrongCount;
    return false;
  }
}
