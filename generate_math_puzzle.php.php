<?php
include "headers.php";

// Generate two random numbers for the puzzle
$num1 = rand(1, 50);
$num2 = rand(1, 50);

// Generate a math question and the correct answer
$puzzle = "$num1 + $num2";
$answer = $num1 + $num2;

// Return the puzzle and the correct answer
echo json_encode([
    "puzzle" => $puzzle,
    "answer" => $answer
]);
