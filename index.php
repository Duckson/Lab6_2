<?php
session_start();

class Forest
{
    public $animals;
    public $plants;
    public $garbage;


    function addAnimals($class, $min = 5, $max = 10, $mult = 10)
    {
        for ($i = 0; $i < rand($min, $max); $i++) {
            $this->animals[] = new $class(1 * $mult, 2 * $mult);
        }
    }

    function addGarbage($class, $min = 5, $max = 10, $mult = 10)
    {
        for ($i = 0; $i < rand($min, $max); $i++) {
            $this->garbage[] = new $class(1 * $mult, 2 * $mult);
        }
    }

    function addPlants($class, $min = 5, $max = 10, $mult = 3)
    {
        for ($i = 0; $i < rand($min, $max); $i++) {
            $this->plants[] = new $class(1 * $mult, 2 * $mult);
        }
    }


    function feedWithAnimal($animal, $mood = 0)
    {
        foreach ($this->animals as $key => $food) {
            if ($animal->checkIfEdible($food, $mood)) {
                $animal->eaten_foods[] = $food;
                unset($this->animals[$key]);
                return $animal->sound;
            }
        }
        return "Нет еды";
    }

    function eatAnything($animal)
    {
        $types = ['Animal', 'Plant', 'Garbage'];
        shuffle($types);

        foreach ($types as $key => $type) {
            switch ($types[$key]) {
                case 'Animal':
                    $result = $this->feedWithAnimal($animal, $types[$key]);
                    if ($result != "Нет еды") return $result;
                    break;
                case 'Plant':
                    $result = $this->feedWithPlant($animal, $types[$key]);
                    if ($result != "Нет еды") return $result;
                    break;
                case "Garbage":
                    $result = $this->feedWithGarbage($animal, $types[$key]);
                    if ($result != "Нет еды") return $result;
                    break;
            }
            unset($types[$key]);
        }
        return "Нет еды";
    }

    function feedWithPlant($animal, $mood = 0)
    {
        foreach ($this->plants as $key => $food) {
            if ($animal->checkIfEdible($food, $mood)) {
                $animal->eaten_foods[] = $food;
                unset($this->plants[$key]);
                return $animal->sound;
            }
        }
        return "Нет еды";
    }

    function feedWithGarbage($animal, $mood = 0)
    {
        foreach ($this->garbage as $key => $food) {
            if ($animal->checkIfEdible($food, $mood)) {
                $animal->eaten_foods[] = $food;
                unset($this->garbage[$key]);
                return $animal->sound;
            }
        }
        return "Нет еды";
    }


}

abstract class LivingThing
{
    public $size;

}

abstract class Animal extends LivingThing
{
    public $eaten_foods;
    public $sound;

    abstract function checkIfEdible($food, $mood);
}

abstract class Herbivore extends Animal
{
    public $herb_foods;

    function checkIfEdible($food, $mood)
    {
        if (get_class($food) == $this->herb_foods || in_array(get_class($food), $this->herb_foods))
            return true;
        else return false;
    }
}

class Deer extends Herbivore
{

    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
        $this->herb_foods = ['Oat', 'Wheat'];
        $this->sound = "Хрум!";
    }
}

class Goat extends Herbivore
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
        $this->herb_foods = ['Barley', 'Wheat'];
        $this->sound = "Бее-е-е!";
    }
}


abstract class Carnivore extends Animal
{
    function checkIfEdible($food, $mood)
    {
        if (($food->size < $this->size) && $food != $this)
            return true;
        else return false;
    }
}

class Wolf extends Carnivore
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
        $this->sound = "Гав!";
    }
}

class Fox extends Carnivore
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
        $this->sound = "Ням!";
    }
}

abstract class Omnivore extends Animal
{
    public $herb_foods;

    function checkIfEdible($food, $mood)
    {
        switch ($mood) {
            case 'Animal':
                if (($food->size < $this->size) && $food != $this)
                    return true;
                else return false;
            case 'Plant':
                if (get_class($food) == $this->herb_foods || in_array(get_class($food), $this->herb_foods))
                    return true;
                else return false;
            case 'Garbage':
                return true;
            default:
                return false;
        }
    }
}

class Monkey extends Omnivore
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
        $this->sound = "У-у-а!";
        $this->herb_foods = ['Wheat', 'Oat', 'Barley'];
    }
}

class Bear extends Omnivore
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
        $this->sound = "ГРРР!";
        $this->herb_foods = ['Wheat', 'Oat'];
    }
}

abstract class Garbage
{
    public $size;
}

class LeafPile extends Garbage
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
    }
}

class Poo extends Garbage
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
    }
}

abstract class Grass extends LivingThing
{
}

class Barley extends Grass
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
    }
}

class Oat extends Grass
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
    }
}

class Wheat extends Grass
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
    }
}


class Tree extends LivingThing
{
}

class Almond extends Tree
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
    }
}

class Oak extends Tree
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
    }
}

if (!isset($_SESSION['forest'])) {
    $_SESSION['forest'] = new Forest;
    $_SESSION['forest']->addAnimals('Deer', 2, 5, 20);
    $_SESSION['forest']->addAnimals('Goat', 2, 5, 15);
    $_SESSION['forest']->addAnimals('Fox', 2, 5, 12);
    $_SESSION['forest']->addAnimals('Wolf', 2, 5, 16);
    $_SESSION['forest']->addAnimals('Monkey', 2, 5, 16);
    $_SESSION['forest']->addAnimals('Bear', 2, 5, 30);
    $_SESSION['forest']->addPlants('Barley', 2, 5, 3);
    $_SESSION['forest']->addPlants('Oat', 2, 5, 4);
    $_SESSION['forest']->addPlants('Wheat', 2, 5, 5);
    $_SESSION['forest']->addPlants('Almond', 2, 5, 100);
    $_SESSION['forest']->addPlants('Oak', 2, 5, 120);
    $_SESSION['forest']->addGarbage('LeafPile', 2, 5, 5);
    $_SESSION['forest']->addGarbage('Poo', 2, 5, 5);
}

if ($_POST['reset']) {
    unset($_SESSION['forest']);
    header("location:index.php");
};

foreach ($_SESSION['forest']->animals as $id => $animal)
    if (isset($_POST[$id]))
        switch (get_parent_class($animal)) {
            case "Carnivore":
                $_SESSION['sound'] = $_SESSION['forest']->eatAnimal($animal);
                break;
            case "Omnivore":
                $_SESSION['sound'] = $_SESSION['forest']->eatAnything($animal);
                break;
            case "Herbivore":
                $_SESSION['sound'] = $_SESSION['forest']->eatPlant($animal);
                break;
            default:
                echo "wut!!?!?!!!?!?!?!";
        }

require('header.php');
?>

<div class="container-fluid">
    Sound: <?= $_SESSION['sound'] ?>
    <br>
    <div class="row">
        <div class="col-sm-7 text-center">
            <b>Animals</b>
            <table class="table table-hover table-condensed table-bordered">
                <thead>
                <tr class="text-center">
                    <th>Name</th>
                    <th>Size</th>
                    <th>Type</th>
                    <th>Eaten foods</th>
                    <th>Eat</th>
                </tr>
                </thead>
                <tbody>
                <? foreach ($_SESSION['forest']->animals as $id => $animal): ?>
                    <tr>
                        <td><?= get_class($animal) ?></td>
                        <td><?= $animal->size ?></td>
                        <td><?= get_parent_class($animal) ?></td>
                        <td>
                            <?php if (isset($animal->eaten_foods)): ?>

                                <? foreach ($animal->eaten_foods as $food): ?>
                                    <?= get_class($food) . " of size " . $food->size . "<br>" ?>
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
                </tbody>
            </table>
        </div>
        <div class="col-sm-4 text-center">
            <b>Plants</b>
            <table class="table table-hover table-condensed table-bordered">
                <tr>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Type</th>
                </tr>
                <? foreach ($_SESSION['forest']->plants as $plant): ?>
                    <tr>
                        <td><?= get_class($plant) ?></td>
                        <td><?= $plant->size ?></td>
                        <td><?= get_parent_class($plant) ?></td>
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
    <br>
    <div class="row">
        <div class="col-sm-7 text-center">
        </div>
        <div class="col-sm-4 text-center">
            <b>Garbage</b>
            <table class="table table-hover table-condensed table-bordered">
                <tr>
                    <th>Name</th>
                    <th>Size</th>
                </tr>
                <? foreach ($_SESSION['forest']->garbage as $garbage): ?>
                    <tr>
                        <td><?= get_class($garbage) ?></td>
                        <td><?= $garbage->size ?></td>
                    </tr>
                <? endforeach; ?>
            </table>
        </div>
    </div>
</div>
