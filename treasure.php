<?php

class Player
{
  public $name;
  public $items;

  public function __construct($name = null, $items = array())
  {
    $this->name = $name;
    $this->items = $items;
  }
}

class Item
{
  public $name;
  public $value;
  public $level;
  public $rarity;
  public $want;
  public $odds = 1;
  public $relative_odds = 0;
  public $count = 0;

  public function __construct($name = null, $value = null, $level = null, $rarity = null, $want = null)
  {
    $this->name = $name;
    $this->value = $value;
    $this->level = $level;
    $this->rarity = $rarity;
    $this->want = $want;
  }

  public function __toString()
  {
    return sprintf("%d\t%1.5f\t%1.5f\t%s (Level %d, %s)", $this->count, $this->odds, $this->relative_odds, $this->name, $this->level, $this->rarity);
  }

  public function calculateOdds($encounter_level)
  {
    $level_mod = ($this->level - $encounter_level);

    $this->odds = 1;
    $this->odds /= $this->rarity->value;
    if ($level_mod > 0) {
      $this->odds /= pow($level_mod, $level_mod);
    }
    $this->odds = round($this->odds, 2);
  }

  public function id()
  {
    return md5($this->name);
  }
}

class Rarity
{
  const COMMON = 1;
  const UNCOMMON = 2;
  const RARE = 5;

  public $value;

  public function __construct($value = null)
  {
    $this->value = $value;
  }

  public function __toString()
  {
    switch ($this->value) {
      case self::COMMON: return "Common";
      case self::UNCOMMON: return "Uncommon";
      case self::RARE: return "Rare";
      default: return "?";
    }
  }
}

class Lottery
{
  public $players;

  public function __construct($players = array())
  {
    $this->players = $players;
  }

  public function run($args)
  {
    $commands = array_slice($args, 1);
    $command = $commands[0];

    switch (strtolower($command)) {
      case "list": $this->cmdList(); break;
      case "select": $this->cmdSelect(); break;
    }
  }

  protected function cmdList()
  {
    foreach ($this->players as $player) {
      echo $player->name . "\n";
      foreach ($player->items as $item) {
        echo sprintf("\t%s\n", $item);
      }
    }
  }

  protected function cmdSelect()
  {
    $items = array();
    $mult = 0;

    foreach ($this->players as $player) {
      foreach ($player->items as $item) {
        $id = $item->id();
        $items[$id] = $item;
        $items[$id]->calculateOdds(3);
        $mult += $items[$id]->odds;
      }
    }

    $mult = 1 / $mult;

    $pool = array();
    foreach ($items as $id => &$item) {
      $item->relative_odds = $item->odds * $mult;
      for ($i=0; $i<($item->relative_odds * 10000); $i++) {
        $pool[] = $id;
      }
    }
    unset($item);

    $iterations = 10000;

    for ($i=0; $i<$iterations; $i++) {
      $selected = $pool[array_rand($pool)];
      $items[$selected]->count++;




    //   $candidates = array();
    //   foreach ($items as $item) {
    //     if ($this->calcRand() <= $item->odds) {
    //       $candidates[] = $item->id();
    //     }
    //   }
    
    //   $selected = $candidates[array_rand($candidates)];
    //   $items[$selected]->count++;
    }

    foreach ($items as $item) {
      echo sprintf("%d\t%1.5f\t%1.5f\t%s", $item->count, $item->relative_odds * 100, ($item->count / $iterations) * 100, $item->name) . "\n";
      //echo $item . "\n";
    }
  }
}

$lottery = new Lottery(array(
  new Player("Joseph", array(
    new Item("+1 Lifestealer Shortsword", 840, 4, new Rarity(Rarity::UNCOMMON)),
    new Item("+1 Seeker Dagger", 680, 3, new Rarity(Rarity::UNCOMMON)),
    new Item("+1 Gloaming Armor", 1000, 5, new Rarity(Rarity::RARE)),
    new Item("+1 Weapon of Speed", 1000, 5, new Rarity(Rarity::RARE)),
    new Item("Helm of Seven Deaths", 1000, 5, new Rarity(Rarity::RARE)),
    new Item("+1 Safewing Amulet", 680, 3, new Rarity(Rarity::COMMON)),
    new Item("Restful Bedroll", 360, 1, new Rarity(Rarity::COMMON)),
    new Item("Bag of Holding", 1000, 5, new Rarity(Rarity::UNCOMMON)),
  )),

  new Player("Adrian", array(
    new Item("Helm of Seven Deaths", 1000, 5, new Rarity(Rarity::RARE)),
    new Item("+1 Gloaming Armor", 1000, 5, new Rarity(Rarity::RARE)),
    new Item("Guardian's Whistle", 840, 4, new Rarity(Rarity::UNCOMMON)),
    new Item("Gauntlets of Blood", 840, 4, new Rarity(Rarity::UNCOMMON)),
  )),

  new Player("Jason", array(
    new Item("+1 Warhammer of Defense", 840, 4, new Rarity(Rarity::UNCOMMON)),
    new Item("+1 Ebon Plate Armor", 680, 3, new Rarity(Rarity::UNCOMMON)),
    new Item("+1 Lifestealer Warhammer", 840, 4, new Rarity(Rarity::UNCOMMON)),
    new Item("Heavy Ranging Defender Shield", 840, 4, new Rarity(Rarity::UNCOMMON)),
    new Item("+2 Plate", 0, 6, new Rarity(Rarity::COMMON)),
    new Item("+2 Warhammer", 0, 6, new Rarity(Rarity::COMMON)),
    new Item("Belt of Vigor", 520, 2, new Rarity(Rarity::COMMON)),
    new Item("+1 Warhammer of Surrounding", 680, 3, new Rarity(Rarity::UNCOMMON)),
    new Item("+1 Doppelganger Plate Armor", 1000, 5, new Rarity(Rarity::UNCOMMON)),
    new Item("Shield of Deflection", 520, 2, new Rarity(Rarity::COMMON)),
    new Item("+1 Warning Warhammer", 840, 4, new Rarity(Rarity::UNCOMMON)),
    new Item("Reading Spectacles", 520, 2, new Rarity(Rarity::COMMON)),
    new Item("Floating Lantern", 680, 3, new Rarity(Rarity::COMMON)),
  )),

  new Player("Chris", array(
  )),
));

$lottery->run($argv);