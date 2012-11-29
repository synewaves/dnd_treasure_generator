<?php

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

  //
  public function __construct($name = null, $gold_value = null, $level = null, $rarity = null)
  {
    $this->name = $name;
    $this->gold_value = $gold_value;
    $this->level = $level;
    $this->rarity = $rarity;
    $this->sell_value = $this->getSellValue();
  }

  //
  public function getSellValue()
  {
    switch ($this->rarity) {
      case self::RARITY_COMMON: return $this->gold_value * 0.2;
      case self::RARITY_UNCOMMON: return $this->gold_value * 0.5;
      default: return $this->gold_value;
    }
  }

  //
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
