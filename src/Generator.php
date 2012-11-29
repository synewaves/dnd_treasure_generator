<?php

class Generator
{
  const CALC_MOD_LEVEL = 0;
  const CALC_MOD_MONEY = 1;

  public $min_sell_value = 0;
  public $max_sell_value = PHP_INT_MAX;
  public $modifier = self::CALC_MOD_LEVEL;
  public $min_level = 0;
  public $max_level = 100;
  public $modifier_level = 3;
  protected $items_map = array();

  //
  public function __construct($items = array())
  {
    $this->setItems($items);
  }

  //
  public function setItems($items)
  {
    $this->items_map = array();

    foreach ($items as $id => $item) {
      $key = md5($id . ":" . $item->name);
      $this->items_map[$key] = $item;
    }
  }

  //
  public function run()
  {
    $items = array();

    // trim items from options:
    foreach ($this->items_map as $key => $item) {
      if ($this->min_sell_value > $item->sell_value || $this->max_sell_value < $item->sell_value) {
        continue;
      }

      if ($this->min_level > $item->level || $this->max_level < $item->level) {
        continue;
      }

      $items[$key] = $item;
    }

    if ($this->modifier == self::CALC_MOD_LEVEL) {
      $odds_map = $this->generateOddsForLevelMods($items, $this->modifier_level);
    } else {
      $odds_map = $this->generateOddsForMoneyMods($items);
    }

    // adjust odds map to create relative percentages:
    $total = array_sum($odds_map);
    $odds_map = array_map(function($n) use($total) {
      return $n / $total;
    }, $odds_map);

    // generate pulls from odds
    $pulls = array();
    foreach ($odds_map as $key => $odds) {
      $pulls = array_merge($pulls, array_fill(0, $odds * 1000, $key));
    }

    // select one
    if (count($pulls) > 0) {
      // print tables:
      echo "Options:\n";
      foreach ($odds_map as $key => $odds) {
        echo sprintf("%3.5f\t%s\n", $odds, $this->items_map[$key]);
      }
      echo "\n";

      $selected = $this->items_map[$pulls[array_rand($pulls)]];
      echo sprintf("Selected:\n%s\n", $selected);
    } else {
      echo "Nothing to choose from!\n";
    }
  }

  //
  protected function generateOddsForLevelMods($items, $level_mod = null)
  {
    $odds = array();

    // get average level from list
    if (is_null($level_mod)) {
      $total = 0;
      foreach ($items as $item) {
        $total += $item->level;
      }

      $level_mod = $total / count($items);
    }

    foreach ($items as $key => $item) {
      $value = 1;
      $mod = $item->level - $level_mod;
      if ($mod < 0) {
        $value *= abs($mod);
      } elseif ($mod > 0) {
        $mod = pow($mod, 2);
        if ($mod == 1) {
          $mod = 2;
        }

        $value /= abs($mod);
      }

      $value /= $item->rarity;
      $odds[$key] = $value;
    }

    return $odds;
  }

  // 
  protected function generateOddsForMoneyMods($items)
  {
    $odds = array();
    $multiplier = 0;

    $total_sell_value = 0;
    foreach ($items as $item) {
      $total_sell_value += $item->sell_value;
    }

    foreach ($items as $key => $item) {
      $odds[$key] = round($total_sell_value / $item->sell_value);
    }
    
    return $odds;
  }
}
