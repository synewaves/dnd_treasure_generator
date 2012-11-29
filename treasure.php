<?php
include "src/Item.php";
include "src/Generator.php";

$generator = new Generator();
//$generator->min_sell_value = 350;
//$generator->max_sell_value = 800;
//$generator->min_level = 4;
//$generator->max_level = 4;
//$generator->modifier_level = 8;
//$generator->modifier = Generator::CALC_MOD_MONEY;
$generator->setItems(array(
  // Joseph
  new Item("+1 Lifestealer Shortsword", 840, 4, Item::RARITY_UNCOMMON),
  new Item("+1 Seeker Dagger", 680, 3, Item::RARITY_UNCOMMON),
  new Item("+1 Gloaming Armor", 1000, 5, Item::RARITY_RARE),
  new Item("+1 Weapon of Speed", 1000, 5, Item::RARITY_RARE),
  new Item("Helm of Seven Deaths", 1000, 5, Item::RARITY_RARE),
  //new Item("+1 Safewing Amulet", 680, 3, Item::RARITY_COMMON),
  //new Item("Restful Bedroll", 360, 1, Item::RARITY_COMMON),
  new Item("Bag of Holding", 1000, 5, Item::RARITY_UNCOMMON),

  // // Adrian
  // new Item("Helm of Seven Deaths", 1000, 5, Item::RARITY_RARE),
  // new Item("+1 Gloaming Armor", 1000, 5, Item::RARITY_RARE),
  // new Item("Guardian's Whistle", 840, 4, Item::RARITY_UNCOMMON),
  // new Item("Gauntlets of Blood", 840, 4, Item::RARITY_UNCOMMON),
  // new Item("+2 Leather Armor", 1800, 4, Item::RARITY_COMMON),
  // new Item("+1 Longsword of Defense", 840, 4, Item::RARITY_UNCOMMON),

  // // Jason
  // new Item("+1 Warhammer of Defense", 840, 4, Item::RARITY_UNCOMMON),
  // new Item("+1 Ebon Plate Armor", 680, 3, Item::RARITY_UNCOMMON),
  // new Item("+1 Lifestealer Warhammer", 840, 4, Item::RARITY_UNCOMMON),
  // new Item("Heavy Ranging Defender Shield", 840, 4, Item::RARITY_UNCOMMON),
  // new Item("+2 Plate", 1800, 6, Item::RARITY_COMMON),
  // new Item("+2 Warhammer", 1800, 6, Item::RARITY_COMMON),
  // new Item("Belt of Vigor", 520, 2, Item::RARITY_COMMON),
  // new Item("+1 Warhammer of Surrounding", 680, 3, Item::RARITY_UNCOMMON),
  // new Item("+1 Doppelganger Plate Armor", 1000, 5, Item::RARITY_UNCOMMON),
  // new Item("Shield of Deflection", 520, 2, Item::RARITY_COMMON),
  // new Item("+1 Warning Warhammer", 840, 4, Item::RARITY_UNCOMMON),
  // new Item("Reading Spectacles", 520, 2, Item::RARITY_COMMON),
  // new Item("Floating Lantern", 680, 3, Item::RARITY_COMMON),

  // // Chris
  // new Item("+1 Ranging Defender Shield", 840, 4, Item::RARITY_UNCOMMON),
  // new Item("+1 Warhammer of Defense", 840, 4, Item::RARITY_UNCOMMON),
  // new Item("+1 Warning Warhammer", 840, 4, Item::RARITY_UNCOMMON),
  // new Item("+1 Ebon Chain Armor", 680, 3, Item::RARITY_UNCOMMON),
));

$generator->run();