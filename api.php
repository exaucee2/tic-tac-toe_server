<?php
header("Access-Control-Allow-Origin: http://localhost/dev/index.html");
session_start();


if (!isset($_SESSION['leaderboard'])) {
    $_SESSION['leaderboard'] = array();
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type']) && $_GET['type'] === 'playGame') {
    echo json_encode(getGameState());
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'makeMove':
            if (isset($_POST['move'])) {
                $move = $_POST['move'];
                echo json_encode(updateGameState($move));
            }
            break;
        case 'newGame':
            echo json_encode(resetGameState());
            break;
        default:
            echo json_encode(array('error' => 'Unknown action'));
            break;
    }
}

function getGameState() {
    if (!isset($_SESSION['gameState'])) {
        $_SESSION['gameState'] = array_fill(0, 9, "");
        $_SESSION['gameState']['isGameOver'] = false;
    }
    return $_SESSION['gameState'];
}


function updateGameState($move) {
    $gameState = getGameState();
    if ($gameState['isGameOver']) {
        return $gameState; 
    }
  
    $currentPlayer = getCurrentPlayer();
    if ($gameState[$move] === "") {
        $gameState[$move] = $currentPlayer;
        if (checkGameOver($gameState)) {
            $gameState['isGameOver'] = true;
            $gameState['result'] = determineGameResult($gameState);
            updateLeaderboard($gameState); // Met à jour le classement à la fin de la partie
        }
    }
    return $gameState;
}

function resetGameState() {
    $_SESSION['gameState'] = array_fill(0, 9, "");
    $_SESSION['gameState']['isGameOver'] = false;
    return $_SESSION['gameState'];
}

function getCurrentPlayer() {
    $movesCount = 0;
    echo $movesCount;
    foreach ($_SESSION['gameState'] as $cell) {
        if ($cell !== "") {
            $movesCount++;
        }
    }
    return ($movesCount % 2 === 0) ? 'O' : 'X';
}

function checkGameOver() {
    $winningCombinations = array(
        array(0, 1, 2), array(3, 4, 5), array(6, 7, 8), 
        array(0, 3, 6), array(1, 4, 7), array(2, 5, 8), 
        array(0, 4, 8), array(2, 4, 6) 
    );
    foreach ($winningCombinations as $combination) {
        $firstCell = $_SESSION['gameState'][$combination[0]];
        if ($firstCell !== "" && $_SESSION['gameState'][$combination[1]] === $firstCell && $_SESSION['gameState'][$combination[2]] === $firstCell) {
            return true;
        }
    }
    foreach ($_SESSION['gameState'] as $cell) {
        if ($cell === "") {
            return false;
        }
    }
    return true;
}

function determineGameResult() {
    if ($_SESSION['gameState']['isGameOver']) {
        $winningCombinations = array(
            array(0, 1, 2), array(3, 4, 5), array(6, 7, 8), 
            array(0, 3, 6), array(1, 4, 7), array(2, 5, 8),
            array(0, 4, 8), array(2, 4, 6) 
        );
        foreach ($winningCombinations as $combination) {
            $firstCell = $_SESSION['gameState'][$combination[0]];
            if ($firstCell !== "" && $_SESSION['gameState'][$combination[1]] === $firstCell && $_SESSION['gameState'][$combination[2]] === $firstCell) {
                return "Player " . $firstCell . " wins!";
            }
        }
        return "Draw.";
    }
    return "Game in progress.";



    function leaderboard($gameState){
        $leaderboard=isset($_SESSION['leaderboard']) ? $_SESSION['leaderboard'] : array();
        $leaderboard[] = $gameState['result'];
    
    }
}
