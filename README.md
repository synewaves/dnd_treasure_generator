# D&D Treasure Generator

This is a simple treasure generator meant to choose an item from a predefined treasure list.  Each entry in the list is assigned odds based on a few different algorithms.  The odds create a weighted lottery from which an item is chosen randomly.  Higher level/value items are given a lower chance to be pulled from a common/cheaper item.

1. **Level + Rarity:** Each item is ranked based on its level compared to the party level, modified against it's rarity.  A highe
2. **Sell Value:** Each item is ranked based on its sell value.
3. **Even Stevens:** Each item is given the same rank evenly.

This project consists of a Generator class which can be used on its own, as well as a web frontend.

You can also [view the live web project](http://projects.tryingtothink.com/dnd/treasure/).