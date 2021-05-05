<?php
/*
	QUESTS
*/
//NOW SET IN THE QUESTS TABLE IN THE MYSQL DATABASE
/*
	REWARDS
*/
//SMALL CHEST
$chest1minOrbs = 200;
$chest1maxOrbs = 400;
$chest1minDiamonds = 2;
$chest1maxDiamonds = 10;
$chest1minShards = 1;
$chest1maxShards = 6;
$chest1minKeys = 1;
$chest1maxKeys = 6;
//BIG CHEST
$chest2minOrbs = 400;
$chest2maxOrbs = 800;
$chest2minDiamonds = 10;
$chest2maxDiamonds = 50;
$chest2minShards = 1;
$chest2maxShards = 6; // THIS VARIABLE IS NAMED IMPROPERLY, A MORE ACCURATE NAME WOULD BE $chest2minItemID AND $chest2maxItemID, BUT I DON'T WANT TO RENAME THIS FOR COMPATIBILITY REASONS... IF YOU'RE GETTING A BLANK CUBE IN YOUR DAILY CHESTS, YOU SET THIS TOO HIGH
$chest2minKeys = 1;
$chest2maxKeys = 6;
//REWARD TIMES (in seconds)
$chest1wait = 1800;
$chest2wait = 7200;
?>