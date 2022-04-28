<!DOCTYPE html>
<html>
	<head>
		<style>
			#advanced {
				display: none;
			}
		</style>
		<script>
			function ShowAdvanced() {
				if (window.getComputedStyle(document.getElementById("advanced")).display === "none") {
					document.getElementById("advanced").style.display = "block";
				}
				else {
					document.getElementById("advanced").style.display = "none";
				}
			}
		</script>
	</head>
	<body>
		<button onclick="ShowAdvanced()">Show/hide additional information</button>
		<div id="advanced">
			<?php
			require('class.php');

			//Code for testing begin
			/*
			$StartTime=microtime(true);
			*/
			//Code for testing end

			ini_set('max_execution_time', '10800');
			$MaxGen=50;
			$Epsilon=0.0000000001;
			$FailedEpsilonCheckLimit=10;
			$PopulationSize=$_POST['PopSize'];
			$NumberOfTasks=$_POST['NumberOfTasks'];
			$TSize=$_POST['TSize'];
			$HMCR=0.5;
			$PAR=0.2;

			foreach ($_POST['e'] as $Key => $Value) {
				$Keys=explode('/', $Key);
				$E[$Keys[0]][$Keys[1]]=$Value;
				$E[$Keys[1]][$Keys[0]]=$Value;
			}
			foreach ($_POST['a'] as $Key => $Value) {
				$A[$Key]=$Value;
			}
			if (isset($_POST['d']) && isset($_POST['Difficulty'])) {
				foreach ($_POST['d'] as $Key => $Value) {
					$D[$Key]=$Value;
				}
				$Difficulty=$_POST['Difficulty'];
			}
			else {
				for ($i=0; $i<=$NumberOfTasks; $i++) {
					$D[$i]=1;
				}
				$Difficulty=$TSize;
			}

			//Code for testing begin
			/*
			$Population=500;
			$NumberOfTasks=2000;
			$TSize=50;
			$E=array();
			for ($i=0; $i<=$NumberOfTasks-1; $i++) {
				for ($j=0; $j<=$NumberOfTasks-1; $j++) {
				$E[$i][$j]=10;
				}
			}
			$A=array();
			for ($i=0; $i<=$NumberOfTasks-1; $i++) {
				$A[$i]="Task".$i;
			}
			$D=array();
			for ($i=0; $i<=$NumberOfTasks-1; $i++) {
				$D[$i]=1;
			}
			$Difficulty=$TSize;
			*/
			//Code for testing end

			$Population = new Population($NumberOfTasks, $TSize, $PopulationSize, $Difficulty, $E, $A, $D);

			//Code for showing in-progress values begin
				echo "PopSize: ".$Population->GetPopulationSize()."<br>";
				echo "NumberOfTasks: ".$Population->GetNumberOfTasks()."<br>";
				echo "TSize: ".$Population->GetTSize()."<br>";
				echo "Chosen Difficulty: ".$Population->GetDifficulty()."<br>";
				echo "<h3>E matrix:</h3>";
				for ($i=0; $i<=$Population->GetNumberOfTasks()-1; $i++) { 	
					for ($j=0; $j<=$Population->GetNumberOfTasks()-1; $j++) {
						if ($Population->GetE()[$i][$j]<10) echo $Population->GetE()[$i][$j]."&nbsp;&nbsp;&nbsp;&nbsp;";
						else echo $Population->GetE()[$i][$j]."&nbsp;&nbsp";
					}
					echo "<br>";
				}
				echo "<h3>Initial population: </h3>";
				for ($i=0; $i<=$Population->GetPopulationSize()-1; $i++) { 
					echo "The ".$i.".  element:  "; 
					for ($j=0; $j<=$Population->GetTSize()-1; $j++) {
						echo  $Population->GetPopulation()[$i][$j]," ";
					}
					echo "<br>";
				}
				echo "<h3>Initial fitness values: </h3>";
				for ($i=0; $i<=$Population->GetPopulationSize()-1; $i++) {
					echo "The ".$i.".  element's initial fitness value: ".$Population->GetFitnessValues()[$i]." <br> ";
				}
				echo "<br>";
				echo "Initial average fintess value: ".array_sum($Population->GetFitnessValues())/count($Population->GetFitnessValues());
			//Code for showing in-progress values end

			$Population->EnhancePopulation($MaxGen, $Epsilon, $FailedEpsilonCheckLimit, $HMCR, $PAR);

			//Code for showing in-progress values begin
				echo "<h3>Final fitness values : </h3>";
				for ($i=0; $i<=$Population->GetPopulationSize()-1; $i++) {
					echo "The ".$i.".  element's final fitness value: ".$Population->GetFitnessValues()[$i]."<br>";
				}
				echo "<br>";
				echo "Final average fintess value: ".array_sum($Population->GetFitnessValues())/count($Population->GetFitnessValues());
				echo "<br><br>";
			//Code for showing in-progress values end
			
			?>
		</div>
		<div>
			<?php

			//Code for testing begin
			/*
			$EndTime=microtime(true);
			$ExecutionTime=($EndTime-$StartTime);
			$MemoryUsage=memory_get_usage()/1204/1204;
			echo "Memory usage: ".$MemoryUsage." MB,\n";
			echo "Execution time: ".$ExecutionTime." sec";
			*/
			//Code for testing end

			echo "<h3>Results: </h3>";
			for ($i=0; $i<=$Population->GetPopulationSize()-1; $i++) {
				for ($j=0; $j<=$Population->GetTSize()-1; $j++) {
				echo $Population->GetA()[$Population->GetPopulation()[$i][$j]]." / difficulty: ".$Population->GetD()[$Population->GetPopulation()[$i][$j]] ."<br>";
				}
				echo "<hr><br>";
			}
			?>
		</div>
	</body>
</html>
