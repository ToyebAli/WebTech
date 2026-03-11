window.addEventListener("DOMContentLoaded", function () {
  var display = document.getElementById("display");
  var buttons = document.getElementsByClassName("btn");

  for (var i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener("click", function () {
      var buttonValue = this.getAttribute("data-value");

      if (buttonValue === "C") {
        display.value = "";
      } 
      else if (buttonValue === "=") {
        try {
          var answer = eval(display.value);
          display.value = answer;
        } 
        catch (error) {
          display.value = "Error";
        }
      } 
      else {
        display.value = display.value + buttonValue;
      }
    });
  }
});
