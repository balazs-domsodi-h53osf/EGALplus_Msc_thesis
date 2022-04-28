<?php
class Population {
	private $NumberOfTasks; //A feladatsorok generálásához felhasználható összes feladat mennyisége.
	private $TSize; //Az egyes feladatsorokban szereplő feladatok mennyisége.
	private $PopulationSize; //A generálandó populáció mérete, azaz a generálandó feladatsorok mennyisége.
	private $Difficulty; //Az egyes feladatsorok cél összesített nehézségi értéke.
	private $E; //$NumberOfTasks*$NumberOfTasks méretű 2D mátrix, mely 0-10 skálán az egyes feladatok feladatsorokban történő közös megjelenítésének kívánatosságát jelöli sorban, ahol 0 esetén tiltott a közös megjelenítés, 10 esetén pedig a leginkább kívánatos.
	private $A; //$NumberOfTasks méretű 1D mátrix, mely sorban az egyes feladatok szövegét tartalmazza.
	private $D; //$NumberOfTasks méretű 1D mátrix, mely sorban az egyes feladatok nehézségi értékét tartalmazza.
	private $Population; //A generált feladatsorok. $PopulationSize méretű 2D mátrix, mely soronként $TSize méretű 1D mátrixokat tartalmaz, melyek egyes elemei az adott feladatsorban szereplő feladatok indexei.
	private $FitnessValues; //A generált feladatsorok fitnesz értékei sorban. $PopulationSize méretű 1D mátrix.

	function __construct(int $NumberOfTasks, int $TSize, int $PopulationSize, int $Difficulty, array $E, array $A, array $D) {
		$this->NumberOfTasks = $NumberOfTasks;
		$this->TSize = $TSize;
		$this->PopulationSize = $PopulationSize;
		$this->Difficulty = $Difficulty;
		$this->E = $E;
		$this->A = $A;
		$this->D = $D;
		$this->DefinePopulation();
		$this->DefineFitnessValues();
	}

	function CreateSinglePopulationElement() { //Egy populációs elem generálása. A populációs elemet $TSize darab feladat indexszel töltjük fel, melyek közül semelyik kettő nem zárhatja ki egymást az E mátrix alapján és az összessített nehézségi értékük $Difficulty értékével megegyező kell, hogy legyen.
		while (true) { //Ciklusből kitörésig.
			$PopulationElement=array(); //Üres populációs elem létrehozása, mely később feltöltésre kerül.
			array_push($PopulationElement, rand(0,$this->NumberOfTasks-1)); //A populációs elembe egy véletlenszerű kezdő feladat index kerül. 
			$TryCounter=0; //Változó inicializálása, mely számlálja a populációs elembe új index illesztésére vonatkozó próbálkozásokat. 
			$DifficultySum=0; //Változó inicializálása a populációs elem összesített nehézségi értékének tárolására.
			while (count($PopulationElement)<$this->TSize) { //Amíg a populációs elem hossza el nem éri az egyes feladatsorokban szerepeltetendő feladatok számát. 
				while (in_array(($TaskIndex=rand(0,$this->NumberOfTasks-1)),$PopulationElement)); //Addig választunk a populációs elembe illesztendő újabb feladat indexet, amíg olyat nem találunk, ami még nem szerepel a feladatsorban.
				$ValidTaskIndex=true; //Változó annak vizsgálatára, hogy a populációs elembe illesztendő újabb feladat index szabályosan beilleszthető-e.
				$PopulationElementCurrentLength=count($PopulationElement); //A populációs elem jelenlegi hosszát tároló változó.
				for ($i=0; $i<=$PopulationElementCurrentLength-1; $i++) { //A populációs elem jelenlegi hosszáig.
					if ($this->E[$PopulationElement[$i]][$TaskIndex]==0) { //Amennyiben a populációs elem adott feladat indexe az E mátrix alapján nem szerepelhet együtt a populációs elembe illesztendő újabb feladat indexszel.
					$ValidTaskIndex=false; //A populációs elembe illesztendő újabb feladat index szabályosan nem illeszthető be.
					$TryCounter++; //Növeljük a populációs elem bővítésére vonatkozó próbálkozások számát számláló változót.
					if ($TryCounter>$this->NumberOfTasks*2) { //Legfeljebb $NumberOfTasks*2 alkalommal próbálkozunk új véletlenszerű feladat index beillesztésével. 
						continue 3; //Amennyiben ennyi próbálkozás után sem sikerül újabb feladat indexszel bővíteni a populációs elemet, úgy teljesen új populációs elemet generálunk és elölről kezdjük a folyamatot
					}
					break; //Ha érvénytelen feladat indexet választottunk és még nem értük el a maximális próbálkozások számát az előző feltételben, akkor a populációs elembe illesztendő újabb feladat indexszet választunk.
					}
				}
				if ($ValidTaskIndex) { //Amennyiben az előző ciklus végetértével az igazolódik be, hogy a populációs elembe szabályosan beilleszthető feladat indexet választottunk.
					$TryCounter=0; //Nullázuk a populációs elem bővítésére vonatkozó próbálkozások számát számláló változót, hogy a következő ciklusban elölről indulhasson.
					array_push($PopulationElement,$TaskIndex); //Beillesztjük a populációs elembe a választott feladat indexet.
				}
			}
			for ($i=0; $i<=$this->TSize-1; $i++) { //Amíg el nem érjük az egy populációs elemben szereplő feladat indexek számát.
				$DifficultySum+=$this->D[$PopulationElement[$i]]; //Összeadjuk az egyes feladat indexekhez tartozó nehézségi értékeket.
			}
			if ($DifficultySum!=$this->Difficulty) { //Ha az új populációs elem összesített nehézségi értéke nem egyezik a cél $Difficulty értékkel.
				continue; //Elölről kezdjük az egész folyamatot. 
			}
			break; //Amennyiben elértük a populációs elem kívánt hosszát és az összesített nehézségi értéke is megegyezik a cél $Difficulty értékkel, kitörünk a fő ciklusból.
		}
		sort($PopulationElement); //Feladat indexek szerint növekvő sorrendbe rendezzük a populációs elem tagjait.
		return $PopulationElement; //Visszaadjuk a kész populációs elemet.
	}

	function DefinePopulation() { //Populáció generálása.
		$this->Population = array(); //A populáció inicializálása.
		while (count($this->Population)<$this->PopulationSize) { //Amíg el nem érjük a cél populáció méretet.
			array_push($this->Population, $this->CreateSinglePopulationElement()); //Újabb populációs elemmel bővítjük a populációt.
		}
	}

	function CalculateSingleFitnessValue(array $PopulationElement) { //Egy populációs elem fitnesz értékének kiszámítása. A fitnesz érték a tartalmazott feladat indexek összes ismétlés nélküli kombinációja szerinti E mátrix értékek összegéből, illetve a többi populációs elemhez vett különbségből adódik. 
		$PopulationElementFitnessValue=0; //A populációs elem fitnesz értékének inicializálása.
		for ($i=0; $i<$this->TSize-1; $i++) { //A párok első tagja.
			for ($j=$i+1; $j<$this->TSize; $j++) { //A párok második tagja.
				$PopulationElementFitnessValue+=$this->E[$i][$j]; //A fitnesz érték növelése a párnak megfelelő E mátrix szerinti értékkel.
			}
		}
		for ($i=0; $i<=$this->PopulationSize-1; $i++) { //Az összes populációs elemhez képest
			$PopulationElementFitnessValue+=count(array_diff($PopulationElement,$this->Population[$i]))/($this->PopulationSize/20); //A fitnesz érték növelése a másik elemhez képesti különbséggel. A $PopulationSize/20-al történő osztás tetszőleges súlyozás miatt szerepel.
		}
		return $PopulationElementFitnessValue; //Visszaadjuk a populációs elem fitnesz értékét.
	}

	function DefineFitnessValues() { //A teljes populáció összes eleméhez tartozó fitnesz érték meghatározása.
		$this->FitnessValues=array(); //Fitnesz értékeket tároló tömb inicializálása.
		for ($i=0; $i<=$this->PopulationSize-1; $i++) { //A teljes populáción.
			array_push($this->FitnessValues,$this->CalculateSingleFitnessValue($this->Population[$i])); //Kiszámoljuk az egyes populációs elemek fitnesz értékét és a fitnesz értékeket tároló tömbhöz adjuk.
		}
	}

	function EnhancePopulation(int $MaxGen, int $Epsilon, int $FailedEpsilonCheckLimit, int $HMCR, int $PAR) { //A populáció minőségének javítása fitnesz érték szerint, harmóniakereső metaheurisztika alapján.
		$LastAverageFitness=array_sum($this->FitnessValues)/count($this->FitnessValues); //Kezdő fitnesz érték meghatározása.
		$FailedEpsilonCheckCount=0; //A kívánt minimális javulást el nem ért futásokat számláló változó inicializálása.
		for ($g=0; $g<=$MaxGen; $g++) { //Meghatározott maximális futásszámig.
			$RandomValue=mt_rand() / mt_getrandmax(); //Véletlenszám generálás.
			if ($RandomValue<=$HMCR) { //Amennyiben véletlenszám kisebb vagy egyenlő, mint HMCR.
				$NewPopulationElement=$this->Population[rand(0,$this->PopulationSize-1)]; //Véletlen populációs elem választás új változóba.
				if ($RandomValue<=$PAR) { //Amennyiben véletlenszám kisebb vagy egyenlő, mint PAR.
					$Time=time(); //Megkezdjük a blokkal eltöltött idő számítását másodpercben.
					while (true) { //Ciklusból kitörésig.
						$ValidChange=true; //Változó annak tárolására, hogy a bevezetni kívánt változás érvényes-e.
						$RandomTask=0; //Bevezetendő új feladat indexet tároló változó inicializálása.
						$RandomPopElementTaskIndex=rand(0,$this->TSize-1); //Cserélendő feladat index véletlenszerű kiválasztása.
						while ($RandomTask=rand(0,$this->NumberOfTasks-1)==$NewPopulationElement[$RandomPopElementTaskIndex] || $this->D[$NewPopulationElement[$RandomPopElementTaskIndex]]!=$this->D[$RandomTask] || time()<$Time+1); //Addig próbálunk véletlenszerűen új feladat indexet választani a cserélendő helyére ameddig nem találunk olyan másikat, ami ugyanazzal a nehézség értékkel rendelkezik, vagy ameddig el nem telik 1 másodperc.
						if (time()>$Time+1) { //Ha eltelt egy másodperc.
							goto CreateNewElement; //Új populációs elemet hozunk létre ehelyett.
						}
						for ($i=0; $i<=$this->TSize-1; $i++) { //A teljes populációs elemen, ami TSize hosszú.
							if ($this->E[$NewPopulationElement[$i]][$RandomTask]==0 & $i!=$RandomPopElementTaskIndex) {  //Ha a módosítandó populációs elem bármely tagja E mátrix szerint összeférhetetlen az újonnan beillesztendő elemmel, kivéve, ha ez éppen az, amelyiket kivennénk.
								$ValidChange=false; //Invalidnak minősítjük a cserét.
								break; //Kitürönk a ciklusból, hiszen fölösleges tovább vizsgálnunk ezt a kérdést.
							}
						}
						if ($ValidChange) { //Amennyiben nem minősítöttük invalidnak a cserét.
							$NewPopulationElement[$RandomPopElementTaskIndex]=$RandomTask; //Elvégezzük a cserét, azaz a populációs elem cserélendő feladat indexét a bevezetni kívánt feladat indexre cseréljük.
							break; //Kitörünk a while(true) ciklusból, hiszen sikerült evlégeznünk a módosítást.
						}
					}
				}
			}
			else { //Amennyiben véletlenszám nagyobb, mint HMCR.
				CreateNewElement:
				$NewPopulationElement=$this->CreateSinglePopulationElement(); //Új populációs elemet hozunk létre.
			}
			$NewPopulationElementFitnessValue=$this->CalculateSingleFitnessValue($NewPopulationElement); //Kiszámoljuk az előzőekben létrejött populációs elem fitnesz értékét.
			$Index=array_search(min($this->FitnessValues),$this->FitnessValues); //Megkeressük a jelenlegi legalacsonyabb fitnesz értéket.
			if ($this->FitnessValues[$Index]<$NewPopulationElementFitnessValue) { //Amennyiben az újonnan létrejött populációs elem fitnesz értéke jobb, az előzőleg megtalált legrosszabbnál.
				$this->SetFitnessValueElement($Index,$NewPopulationElementFitnessValue); //Cseréljük a legrosszabb fitnesz értéket az újra.
				$this->SetPopulationElement($Index,$NewPopulationElement); //Cseréljük magát a populációs elemet is az újra.
			}
			$CurrentAverageFitness=array_sum($this->FitnessValues)/count($this->FitnessValues); //Kiszámoljuk a jelenlegi átlagos fitnesz értéket.
			if ($CurrentAverageFitness-$LastAverageFitness<$Epsilon) { //Amennyiben a fitnesz érték javulása nem éri el Epsilon-t, azaz a minimálisan elvárt javulás mértékét.
				$FailedEpsilonCheckCount++; //Növeljük azt a változót, ami nyomon követi, hányszor nem értük el egymás után az elvárt minimális javulást. 
			}
			else { //Ha elértük a minimális elvárt javulást.
				$FailedEpsilonCheckCount=0; //Nullázuk azt a változót, ami nyomon követi, hányszor nem értük el egymás után az elvárt minimális javulást. 
			}
			if ($FailedEpsilonCheckCount==$FailedEpsilonCheckLimit) { //Ha egymás után annyiszor nem értük el a kívánt javulást, amennyiszer azt legfeljebb toleráljuk. 
				break; //Kitörünk a teljes minőségjavító ciklusból.
			}
			$LastAverageFitness=$CurrentAverageFitness; //A legutóbbi átlagos fitnesz értéknek a jelenlegit vesszük.
		}
	}

	function SetPopulationElement(int $Index, array $PopulationElement) { //Egy populációs elem felülírása adott indexen adott új elemmel.
		$this->Population[$Index] = $PopulationElement;
	}
  
	function SetFitnessValueElement(int $Index, float $FitnessValue) { //Egy fitnesz érték felülírása adott indexen adott új értékkel.
		$this->FitnessValues[$Index] = $FitnessValue;
	}

	function GetNumberOfTasks() {
		return $this->NumberOfTasks;
	}

	function GetTSize() {
		return $this->TSize;
	}

	function GetPopulationSize() {
		return $this->PopulationSize;
	}

	function GetDifficulty() {
		return $this->Difficulty;
	}

	function GetE() {
		return $this->E;
	}

	function GetA() {
		return $this->A;
	}

	function GetD() {
		return $this->D;
	}

	function GetPopulation() {
		return $this->Population;
	}

	function GetFitnessValues() {
		return $this->FitnessValues;
	}
  }
?>