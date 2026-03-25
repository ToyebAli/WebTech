function analyzeText() {
  var text = document.getElementById("textInput").value;

  if (text.trim() == "") {
    alert("Please enter some text first!");
    return;
  }

  var charCount = text.length;

  var trimmedText = text.trim();
  var words = trimmedText.split(" ");
  var wordCount = 0;
  for (var i = 0; i < words.length; i++) {
    if (words[i] != "") {
      wordCount++;
    }
  }

  var reversedText = "";
  for (var i = text.length - 1; i >= 0; i--) {
    reversedText += text[i];
  }

  document.getElementById("charCount").textContent = charCount;
  document.getElementById("wordCount").textContent = wordCount;
  document.getElementById("reversedText").textContent = reversedText;
  document.getElementById("results").classList.add("show");
}
