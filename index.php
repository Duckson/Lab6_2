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

    function feed(Animal $animal)
    {
        return $animal->eat($this);
    }


    /*function feedWithAnimal($animal, $mood = 0)
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
    }*/


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

    abstract function eat($forest);

    function eatPlant(Forest $forest, $mood = 0)
    {
        foreach ($forest->plants as $key => $food) {
            if ($this->checkIfEdible($food, $mood)) {
                $this->eaten_foods[] = $food;
                unset($forest->plants[$key]);
                return $this->sound;
            }
        }
        return "Нет еды";
    }

    function eatAnimal(Forest $forest, $mood = 0)
    {
        foreach ($forest->animals as $key => $food) {
            if ($this->checkIfEdible($food, $mood)) {
                $this->eaten_foods[] = $food;
                unset($forest->animals[$key]);
                return $this->sound;
            }
        }
        return "Нет еды";
    }

    function eatGarbage(Forest $forest, $mood = 0)
    {
        foreach ($forest->garbage as $key => $food) {
            if ($this->checkIfEdible($food, $mood)) {
                $this->eaten_foods[] = $food;
                unset($forest->garbage[$key]);
                return $this->sound;
            }
        }
        return "Нет еды";
    }
}

abstract class Herbivore extends Animal
{
    public $herb_foods;

    function eat($forest)
    {
        return $this->eatPlant($forest);
    }

    function checkIfEdible($food, $mood = 0)
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
    function eat($forest)
    {
        return $this->eatAnimal($forest);
    }

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

    function eat($forest)
    {
        $types = get_object_vars($forest);
        shuffle($types);

        foreach ($types as $key => $type) {
            if ($this->isArrOfObjs($type)) {    //чтоб не жрал ненужные переменные леса
                $mood = get_parent_class(get_parent_class(array_values($type)[0]));
                switch ($mood) {
                    case 'Animal':
                        $result = $this->eatAnimal($forest, $mood);
                        if ($result != "Нет еды") return $result;
                        break;
                    case 'Plant':
                        $result = $this->eatPlant($forest, $mood);
                        if ($result != "Нет еды") return $result;
                        break;
                    case "Garbage":
                        $result = $this->eatGarbage($forest, $mood);
                        if ($result != "Нет еды") return $result;
                        break;
                }
            }
        }
        return "Нет еды";
    }

    function isArrOfObjs($arr)
    {
        if (!is_array($arr)) return false;
        foreach ($arr as $obj) {
            if (!is_object($obj)) return false;
        }
        return true;
    }

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

abstract class WasteOfLiving extends Garbage
{
    function __construct($min, $max)
    {
        $this->size = rand($min, $max);
    }
}

class LeafPile extends WasteOfLiving
{
}

class Poo extends WasteOfLiving
{
}

abstract class Plant extends LivingThing
{
}

abstract class Grass extends Plant
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


class Tree extends Plant
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
        $_SESSION['sound'] = $_SESSION['forest']->feed($animal);


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
