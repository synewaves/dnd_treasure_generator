<?php
/*
 * This file is part of the dnd_treasure_generator library.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

include "src/Item.php";
include "src/Generator.php";

$generator = new Generator();

if (isset($_POST["items"]) && isset($_POST["options"])) {
  $items = array();
  foreach ($_POST["items"] as $item) {
    $name = isset($item["name"]) ? $item["name"] : null;
    $gold = isset($item["gold_value"]) ? (int) $item["gold_value"] : 0;
    $level = isset($item["level"]) ? (int) $item["level"] : 0;
    $rarity = isset($item["rarity"]) ? (int) $item["rarity"] : 0;
    $use = isset($item["use"]) ? (bool) $item["use"] : false;

    if ($use && $name != "" && $gold > 0 && $level > 0 && $rarity > 0) {
      $items[] = new Item($name, $gold, $level, $rarity);
    }
  }

  $generator->setItems($items);
  foreach (array("min_sell_value", "max_sell_value", "min_level", "max_level", "modifier_level") as $key) {
    if (isset($_POST["options"][$key]) && trim($_POST["options"][$key]) != "") {
      $generator->$key = (int) $_POST["options"][$key];
    }
  }
  $generator->modifier = isset($_POST["options"]["modifier"]) ? (int) $_POST["options"]["modifier"] : Generator::CALC_MOD_MONEY;

  $item_id = $generator->selectItem();
  $items = $generator->getItems();

  $return = new stdClass;
  $return->item = $item_id ? $items[$item_id] : null;

  if (isset($_POST["options"]["debug"]) && $_POST["options"]["debug"]) {
    $return->debug = new stdClass;
    $return->debug->item_id = $item_id;
    $return->debug->items = $items;
    $return->debug->odds_table = $generator->getLastOddsTable();
    $return->debug->random_seed = $generator->getLastSeed();
  }

  echo json_encode($return);
  exit;
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Treasure Generator</title>
  <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
  <link href="css/site.css" rel="stylesheet" media="screen">
</head>
<body>

<div class="container">
  <h1>Treasure Generator</h1>
  <div class="row">
    <div class="span12">
      <div id="selected" class="continue-controls"></div>
      <div id="debug" class="continue-controls"></div>

      <form method="POST" action="index.php">
        <fieldset class="form-inline">
          <legend>Items</legend>
          <div id="items" class="continue-controls"></div>
          <div id="add_list" style="display:none;">
            <textarea rows="10" class="input-xxlarge" id="list-import"></textarea>
            <span class="help-block">One item per line, format: Item name, level, Rarity, List value. Ex: Bag of Holding, 5, Uncommon, 1000</span>
            <button class="btn" id="update-list">Save Changes</button>
          </div>
          <span class="continue-controls"><a href="#" id="add-item">Add an item</a> | <a href="#" id="modify-list">Modify/import/export list</a></span>
        </fieldset>
        <fieldset class="row continue-controls">
          <legend>Options</legend>
          <div class="span4">
            <label for="options_modifier">Base odds on:</label>
            <select name="options[modifier]" id="options_modifier">
              <option value="<?php echo Generator::CALC_MOD_LEVEL; ?>">Level + Rarity</option>
              <option value="<?php echo Generator::CALC_MOD_MONEY; ?>">Sell Value</option>
              <option value="<?php echo Generator::CALC_MOD_EVEN; ?>">Even Stevens</option>
            </select>

            <label for="options_modifier_level">Party Level Modifier:</label>
            <input class="span2" type="text" name="options[modifier_level]" id="options_modifier_level" placeholder="Party Level Modifier">
            <span class="help-block">If blank, will calculate from average item level</span>
            <label class="checkbox">
              <input type="hidden" value="0" name="options[debug]">
              <input type="checkbox" value="1" name="options[debug]" id="options_debug">
              Show debug information?
            </label>
          </div>
          <div class="span4">
            <label for="options_min_level">Minimum item level:</label>
            <input class="span2" type="text" name="options[min_level]" id="options_min_level" placeholder="Min Level">
            <label for="options_max_level">Maximum item level:</label>
            <input class="span2" type="text" name="options[max_level]" id="options_max_level" placeholder="Max Level">
          </div>
          <div class="span4">
            <label for="options_min_sell_value">Minimum sell value:</label>
            <input class="span2" type="text" name="options[min_sell_value]" id="options_min_sell_value" placeholder="Min Sell Value">
            <label for="options_max_sell_value">Maximum sell value:</label>
            <input class="span2" type="text" name="options[max_sell_value]" id="options_max_sell_value" placeholder="Max Sell Value">
          </div>
        </fieldset>
        <div class="form-actions continue-controls">
          <button class="btn btn-primary" id="generate"><i class="icon-ok-circle icon-white"></i> Generate</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  var elms = <?php echo json_encode(array_values($generator->getItems())); ?>;
  var rarity_values = {
    <?php echo Item::RARITY_COMMON; ?>: "Common",
    <?php echo Item::RARITY_UNCOMMON; ?>: "Uncommon",
    <?php echo Item::RARITY_RARE; ?>: "Rare"
  };
</script>

<script id="item-template" type="text/x-handlebars-template">
  <div class="item">
    <label class="checkbox">
      <input type="hidden" value="0" name="items[{{ id  }}][use]">
      <input type="checkbox" value="1" name="items[{{ id  }}][use]" checked="checked">
    </label>
    <input data-type="name" type="text" class="input-xlarge" placeholder="Name" size="20" value="{{ name }}" name="items[{{ id  }}][name]">
    <div class="input-prepend">
      <span class="add-on">$</span>
      <input type="text" data-type="value" placeholder="Gold value" class="input-mini" size="3" value="{{ gold_value }}" name="items[{{ id  }}][gold_value]">
    </div>
    <input type="text" data-type="level" placeholder="Level" class="input-mini" size="3" value="{{ level }}" name="items[{{ id  }}][level]">
    <select name="items[{{ id  }}][rarity]" data-type="rarity">
      {{{select_rarity rarity}}}
    </select>

    <a class="btn btn-small btn-danger" href="#"><i class="icon-remove icon-white"></i></a>
  </div>
</script>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/handlebars.min.js"></script>
<script src="js/treasure.js"></script>

</body>
</html>