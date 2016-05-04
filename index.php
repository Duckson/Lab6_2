<?php
session_start();

class Forest
{

    function __construct()
    {
        $animal_types = ["Herbivore", "Carnivore"];
        $plant_types = ["Grass", "Tree"];

        if (!isset($_SESSION['animals']))
            foreach ($animal_types as $type) {
                for ($i = 0; $i < rand(2, 5); $i++) {
                    $_SESSION['animals'][] = new $type;
                }
            }

        if (!isset($_SESSION['plants']))
            foreach ($plant_types as $type) {
                for ($i = 0; $i < rand(8, 15); $i++) {
                    $_SESSION['plants'][] = new $type;
                }
            }

    }

    public function reset()
    {
        unset($_SESSION['animals']);
        unset($_SESSION['plants']);
        header("location:index.php");
        exit();
    }
}

abstract class LivingThing
{
    protected $info;

    public function getInfo()
    {
        return $this->info;
    }

}

abstract class Animal extends LivingThing
{
    function eat(&$foods)
    {
        $have_eaten = false;

        foreach ($foods as $key=>$food) {
            if ($have_eaten == false)
                if ($this->checkIfEdible($food)) {
                    $this->info['eaten_foods'][] = $food;
                    unset($foods[$key]);
                    $have_eaten = true;
                }
        }
    }

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
        if ($food->getInfo()['type'] == $this->info['herb_foods'] || in_array($food->getInfo()['type'], $this->info['herb_foods']))
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
        if ($food->getInfo()['size'] < $this->info['size'])
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

$forest = new Forest;
if ($_POST['reset']) $forest->reset();

foreach ($_SESSION['animals'] as $id => $animal)
    if (isset($_POST[$id]))
        if (get_class($animal) == "Carnivore")
            $animal->eat($_SESSION['animals']);
        else $animal->eat($_SESSION['plants']);
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
                <? foreach ($_SESSION['animals'] as $id => $animal): ?>
                    <tr>
                        <td><?= $animal->getInfo()['type'] ?></td>
                        <td><?= $animal->getInfo()['size'] ?></td>
                        <td><?= get_class($animal) ?></td>
                        <td>
                            <?php if (isset($animal->getInfo()['eaten_foods'])): ?>

                                <? foreach ($animal->getInfo()['eaten_foods'] as $food): ?>
                                    <?= $food->getInfo()['type'] . " of size " . $food->getInfo()['size'] . "<br>" ?>
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
                <? foreach ($_SESSION['plants'] as $plant): ?>
                    <tr>
                        <td><?= $plant->getInfo()['type'] ?></td>
                        <td><?= $plant->getInfo()['size'] ?></td>
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
