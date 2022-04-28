<?php
ini_set("memory_limit","2048M");
ini_set('max_execution_time', '3600');

if (!isset($_POST['DifficultyValues']) || !isset($_POST['TSize']) || !isset($_POST['PopSize'])) {
	exit;
}

foreach($_POST['DifficultyValues'] as $key => $value) {
	$D[$key]=$value;
}

$TSize=$_POST['TSize'];
$PopSize=$_POST['PopSize'];

$Checked = array();
$SumArray = array();
$FinalDifficulties = array();
$OccurrencyTreshold = $PopSize*2;
$ValidDifficultiesTreshold = 3;

$SortedD = $D;
$DifficultyMinimum=0;
sort($SortedD);
for ($i=0; $i<$TSize; $i++) {
    $DifficultyMinimum += $SortedD[$i];
}

$DifficultyMaximum=0;
rsort($SortedD);
for ($i=0; $i<$TSize; $i++) {
    $DifficultyMaximum += $SortedD[$i];
}

$DifferenceGoal = round(($DifficultyMaximum-$DifficultyMinimum)/($ValidDifficultiesTreshold-1));

$Time = time();
while (true) {
 
    $RandomKeys = array_rand($D, $TSize);
    $CurrentTask = array();
    $CurrentDifficulty = array();
    foreach ($RandomKeys as $RandomKey) {
        $CurrentTask[] = $RandomKey;
        $CurrentDifficulty[] = $D[$RandomKey];
    }
        for ($i=0; $i<count($Checked); $i++) {  
            if ($Checked[$i] == $CurrentTask) {
                continue 2;
            }
        }

    $Checked[] = $CurrentTask;
    
    $CurrentSum = 0;
    foreach ($CurrentDifficulty as $Value) {
        $CurrentSum += $Value;
    }

    $SumFound = false;
    for ($i=0; $i<count($SumArray); $i++) {
        if ($SumArray[$i][0] == $CurrentSum) {
            $SumArray[$i][1]++;
            if ($SumArray[$i][1] == $OccurrencyTreshold) {
                $FinalDifficulties[] = $CurrentSum;
            }
            $SumFound = true;
            break;
        }
    }

    if ($SumFound == false) {
        $SumArray[] = array($CurrentSum, 1);
    }
    if (count($FinalDifficulties)>0) {
        $ValidDifficultyKeys = array();
        sort($FinalDifficulties);
        $ValidDifficultyKeys[] = 0;
        $FinalDifficultiesCurrent = $FinalDifficulties[0];
        for ($i=1; $i<count($FinalDifficulties); $i++) {
            if ($FinalDifficulties[$i] >= $FinalDifficultiesCurrent+$DifferenceGoal) {
                $ValidDifficultyKeys[] = $i;
                $FinalDifficultiesCurrent = $FinalDifficulties[$i];
            }
        }
        if (count($ValidDifficultyKeys) >= $ValidDifficultiesTreshold) {
            $ValidDifficulties = array();
            foreach ($ValidDifficultyKeys as $Key) {
                $ValidDifficulties[] = $FinalDifficulties[$Key];
            }
            break;
        }
    }
    if (time() > $Time + 1) {
        if ($DifferenceGoal>0) {
        $DifferenceGoal--;
        }
        $Time = time();
    }
}
echo json_encode($ValidDifficulties);
exit;
?>