<?php
/*
 * This file is part of the dnd_treasure_generator library.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

class Item
{
  const RARITY_COMMON = 1;
  const RARITY_UNCOMMON = 2;
  const RARITY_RARE = 4;

  public $name;
  public $gold_value;
  public $sell_value;
  public $level;
  public $rarity;
  public $display_value;

  /**
   * Constructor
   *
   * @param string $name item name
   * @param int $gold_value item actual value (not sell value)
   * @param int $level item level
   * @param int $rarity item rarity
   */
  public function __construct($name = null, $gold_value = null, $level = null, $rarity = null)
  {
    $this->name = $name;
    $this->gold_value = $gold_value;
    $this->level = $level;
    $this->rarity = $rarity;
    $this->sell_value = $this->getSellValue();
    $this->display_value = (string) $this;
  }

  /**
   * Get the sell value for the item
   *
   * @return int sell value
   */
  public function getSellValue()
  {
    switch ($this->rarity) {
      case self::RARITY_COMMON: return $this->gold_value * 0.2;
      case self::RARITY_UNCOMMON: return $this->gold_value * 0.5;
      default: return $this->gold_value;
    }
  }

  /**
   * Get string representation of item
   *
   * @return string item as string
   */
  public function __toString()
  {
    switch ($this->rarity) {
      case self::RARITY_COMMON: $rarity = "Common"; break;
      case self::RARITY_UNCOMMON: $rarity = "Uncommon"; break;
      case self::RARITY_RARE: $rarity = "Rare"; break;
      default: $rarity = "?";
    }

    return sprintf("%s (Level %d, %s) $%d", $this->name, $this->level, $rarity, $this->sell_value);
  }
}
