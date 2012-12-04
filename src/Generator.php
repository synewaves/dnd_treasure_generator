<?php
/*
 * This file is part of the dnd_treasure_generator library.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

class Generator
{
  const CALC_MOD_LEVEL = 0;
  const CALC_MOD_MONEY = 1;
  const CALC_MOD_EVEN = 2;

  public $min_sell_value = 0;
  public $max_sell_value = PHP_INT_MAX;
  public $modifier = self::CALC_MOD_LEVEL;
  public $min_level = 0;
  public $max_level = PHP_INT_MAX;
  public $modifier_level = 0;

  protected $items_map = array();
  protected $odds_table = array();
  protected $last_seed = null;


  /**
   * Constructor
   *
   * @param array $items list of Item to generate from
   */
  public function __construct($items = array())
  {
    $this->setItems($items);
  }

  /**
   * Set the list of items
   *
   * @param array $items list of Item to generate from
   */
  public function setItems($items)
  {
    $this->items_map = array();

    foreach ($items as $id => $item) {
      $key = md5($id . ":" . $item->name);
      $this->items_map[$key] = $item;
    }
  }

  /**
   * Get the list of items
   *
   * @return array list of Item [item_id] => Item
   */
  public function getItems()
  {
    return $this->items_map;
  }

  /**
   * Get the last odds table
   *
   * @param array odds table [item_id] => odds
   */
  public function getLastOddsTable()
  {
    return $this->odds_table;
  }

  /**
   * Get the last random seed
   *
   * @param float last random seed
   */
  public function getLastSeed()
  {
    return $this->last_seed;
  }

  /**
   * Select an item from the list
   *
   * @param string selected item id
   */
  public function selectItem()
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
      $this->odds_table = $this->generateOddsForLevelMods($items, $this->modifier_level);
    } elseif ($this->modifier == self::CALC_MOD_MONEY) {
      $this->odds_table = $this->generateOddsForMoneyMods($items);
    } else {
      $this->odds_table = $this->generateOddsForEvenMods($items);
    }

    // adjust odds map to create relative percentages:
    $total = array_sum($this->odds_table);
    $this->odds_table = array_map(function($n) use($total) {
      return $n / $total;
    }, $this->odds_table);

    asort($this->odds_table);
    $this->odds_table = array_reverse($this->odds_table);

    if (count($this->odds_table) > 0) {
      return $this->choose($this->odds_table);
    }
  }

  /**
   * Generate odds table for level/rarity based odds
   *
   * @param array $items list of Item to generate from
   * @param int $level_mod party level modifier
   * @return array odds table
   */
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

  /**
   * Generate odds table for sell value based odds
   *
   * @param array $items list of Item to generate from
   * @return array odds table
   */
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

  /**
   * Generate odds table with all items at equal chance
   *
   * @param array $items list of Item to generate from
   * @return array odds table
   */
  protected function generateOddsForEvenMods($items)
  {
    $odds = array();
    $percent = 1 / count($items);

    foreach ($items as $key => $item) {
      $odds[$key] = $percent;
    }

    return $odds;
  }

  /**
   * Choose an item from the odds table
   *
   * @param array $items odds table
   * @return string selected item id
   */
  protected function choose($items)
  {
    // get random number between 0 and 1 to 8 decimal places:
    list($usec, $sec) = explode(' ', microtime());
    $this->last_seed = (float) $sec + ((float) $usec * 100000);
    mt_srand($this->last_seed);
    $num = mt_rand(0, 100000000) / 100000000;

    $acc = 0;
    $picked = null;
    foreach ($items as $key => $odds) {
      $acc += $odds;
      if ($num <= $acc) {
        $picked = $key;
        break;
      }
    }

    return $picked;
  }
}
