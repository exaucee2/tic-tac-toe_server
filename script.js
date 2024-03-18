
document.addEventListener("DOMContentLoaded", function() {
    playGame();

    var boxes = document.querySelectorAll(".box");
    boxes.forEach(function(box) {
        box.addEventListener("click", function() {
            makeMove(box.id);
        });
    });

    var playAgainButton = document.getElementById("play-again");
    playAgainButton.addEventListener("click", newGame);
});

function playGame() {
    sendRequest("GET", "api.php?type=playGame", null)
    .then(function(response) {
        // Traitement de la réponse ici
        console.log("Réponse reçue :", response);
      })
      .catch(function(error) {
        // Gestion des erreurs ici
        console.error("Erreur lors de la requête :", error);
      });
}

function makeMove(spot) {
    var params = spot;
    sendRequest("POST", "api.php?action=makeMove","move="+ params)
    .then(function(response) {
        // Traitement de la réponse ici
        console.log("Réponse reçue :", response);
      })
      .catch(function(error) {
        // Gestion des erreurs ici
        console.error("Erreur lors de la requête :", error);
      });
}

function newGame() {
    sendRequest("POST", "api.php", "action=newGame", updateGameState);
}

function updateGameState(data) {
    if (!data) {
        console.error("Invalid game state data.");
        return;
    }

    document.getElementById("main-grid").innerHTML = data.gameBoard;
    document.getElementById("results").innerHTML = data.result;

    document.querySelectorAll(".turn-box").forEach(turnBox => turnBox.classList.remove("active"));
    var activeTurnBox = document.querySelector(".turn-box:contains('" + data.currentPlayer + "')");
    if (activeTurnBox) {
        activeTurnBox.classList.add("active");
    }

    document.getElementById("leaderboard").innerHTML = data.leaderboard;
}

function sendRequest(method, url, params) {
    return new Promise(function(resolve, reject) {
      var xhttp = new XMLHttpRequest();
      if (params !== null) {
        url = url + "&" + params;
      }
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
          if (this.status == 200) {
            resolve(JSON.parse(this.responseText));
          } else {
            reject("Une erreur s'est produite lors de la requête : " + this.status);
          }
        }
      };
      var data = new FormData();
        data.append('move', "0,2");

      xhttp.open(method.toUpperCase(), url, true);
      xhttp.send(data);
    });
  }
