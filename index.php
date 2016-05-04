<?php
session_start();

class Forest
{
    public $animals;
    public $plants;


    function addAnimals($class, $min, $max){
            for ($i = 0; $i < rand($min, $max); $i++) {
                $this->animals[] = new $class;
            }
    }

    function addPlants($class, $min, $max){
            for ($i = 0; $i < rand($min, $max); $i++) {
                $this->plants[] = new $class;
            }
    }


    function eatAnimal($animal)
    {
        foreach ($this->animals as $key => $food) {
                if ($animal->checkIfEdible($food)) {
                    $animal->info['eaten_foods'][] = $food;
                    unset($this->animals[$key]);
                    return true;
                }
        } return false;
    }

    function eatPlant($animal)
    {
        foreach ($this->plants as $key => $food) {
            if ($animal->checkIfEdible($food)) {
                $animal->info['eaten_foods'][] = $food;
                unset($this->plants[$key]);
                return true;
            }
        } return false;
    }

}

abstract class LivingThing
{
    public $info;

}

abstract class Animal extends LivingThing
{


    abstract function checkIfEdible($food);
}

class Herbivore extends Animal
{
    function __construct()
    {
        $types = [
            0 => [
                'type' => 'deer',
                'size' => rand(20, 40),
                'herb_foods' => ['oat', 'wheat']
            ],
            1 => [
                'type' => 'goat',
                'size' => rand(12, 24),
                'herb_foods' => ['barley', 'wheat']
            ]
        ];
        $this->info = $types[array_rand($types)];
    }

    function checkIfEdible($food)
    {
        if ($food->info['type'] == $this->info['herb_foods'] || in_array($food->info['type'], $this->info['herb_foods']))
            return true;
        else return false;
    }
}


class Carnivore extends Animal
{
    function __construct()
    {
        $types = [
            0 => [
                'type' => 'wolf',
                'size' => rand(15, 30)
            ],
            1 => [
                'type' => 'fox',
                'size' => rand(10, 20)
            ]
        ];
        $this->info = $types[array_rand($types)];
    }

    function checkIfEdible($food)
    {
        if (($food->info['size'] < $this->info['size']) && $food != $this)
            return true;
        else return false;
    }
}

/*
abstract class Plant
{
    protected $size;
    protected $type;
    protected $subtype;
}
*/

class Grass extends LivingThing
{
    function __construct()
    {
        $types = [
            0 => [
                'type' => 'barley',
                'size' => rand(5, 10)
            ],
            1 => [
                'type' => 'oat',
                'size' => rand(4, 8)
            ],
            2 => [
                'type' => 'wheat',
                'size' => rand(3, 12)
            ]
        ];
        $this->info = $types[array_rand($types)];
    }
}

class Tree extends LivingThing
{
    function __construct()
    {
        $types = [
            0 => [
                'type' => 'almond',
                'size' => rand(40, 160)
            ],
            1 => [
                'type' => 'oak',
                'size' => rand(30, 200)
            ]
        ];
        $this->info = $types[array_rand($types)];
    }
}

if (!isset($_SESSION['forest'])) {
    $_SESSION['forest'] = new Forest;
    $_SESSION['forest']->addAnimals('Carnivore', 5, 10);
    $_SESSION['forest']->addAnimals('Herbivore', 5, 10);
    $_SESSION['forest']->addPlants('Grass', 10, 20);
    $_SESSION['forest']->addPlants('Tree', 10, 20);
}

if ($_POST['reset']) {
    unset($_SESSION['forest']);
    header("location:index.php");
};

foreach ($_SESSION['forest']->animals as $id => $animal)
    if (isset($_POST[$id]))
        if (get_class($animal) == "Carnivore")
            $_SESSION['forest']->eatAnimal($animal);
        else $_SESSION['forest']->eatPlant($animal);
require('header.php');
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-7 text-center">
            <b>Animals</b>
            <table class="table table-hover">
                <tr class="text-center">
                    <th>Name</th>
                    <th>Size</th>
                    <th>Type</th>
                    <th>Eaten foods</th>
                    <th>Eat</th>
                </tr>
                <? foreach ($_SESSION['forest']->animals as $id => $animal): ?>
                    <tr>
                        <td><?= $animal->info['type'] ?></td>
                        <td><?= $animal->info['size'] ?></td>
                        <td><?= get_class($animal) ?></td>
                        <td>
                            <?php if (isset($animal->info['eaten_foods'])): ?>

                                <? foreach ($animal->info['eaten_foods'] as $food): ?>
                                    <?= $food->info['type'] . " of size " . $food->info['size'] . "<br>" ?>
                                <? endforeach; ?>

                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="index.php" method="post">
                                <input type="submit" name="<?= $id ?>" value="Eat">
                            </form>
                        </td>
                    </tr>
                <? endforeach; ?>
            </table>
        </div>
        <div class="col-sm-4 text-center">
            <b>Plants</b>
            <table class="table table-hover table-condensed">
                <tr>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Type</th>
                </tr>
                <? foreach ($_SESSION['forest']->plants as $plant): ?>
                    <tr>
                        <td><?= $plant->info['type'] ?></td>
                        <td><?= $plant->info['size'] ?></td>
                        <td><?= get_class($plant) ?></td>
                    </tr>
                <? endforeach; ?>
            </table>
        </div>
        <div class="col-sm-1">
            <form action="index.php" method="post">
                Сбросить? <input type="checkbox" name="reset">
                <input type="submit" value="Сброс">
            </form>
        </div>
    </div>
</div>
