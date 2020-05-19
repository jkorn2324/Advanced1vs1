# Advanced1vs1

- This is a Pocketmine-MP plugin used to add support for advanced 1vs1 systems. 
- This takes advantage of other, more notable kit plugins, such as AdvancedKits and my KitKB plugin.
- Contains support for Minecraft Pocket Edition version **0.15.10** so far, but support for versions **1.1** and the **latest** will eventually be added.
- Differentiates itself from the **Practice Core** plugin as this plugin only contains a 1vs1 system, while the other plugin contains an arena system, etc...

## Table of Contents
[TOC]

------------

## Commands

`/duel <optional - kit>` - Places the player within a queue that is used for actually dueling, the kit parameter is optional and refers to the given kit to duel with or not.
- If the kit parameter is empty, then the players would duel with the default kit.

`/arenacreate <name>` - Creates a new duel arena based on the given name **AND** the level the player is in when the command is executed.

`/arenadelete <name>` - Deletes an existing duel arena based on the given name.
- If the arena already contains a currently running duel, the duel will continue running until it is finished.

`/lsdarenas` - Lists all of the existing duel arenas and showing whether or not they are being used or not.

`/p1Spawn <optional - name>`  - Sets the first player's spawn at the given position of the player who executes the command. 
- The name parameter refers to an arena name. 
- **If no name parameter is provided**, then the first player's spawn is saved to the command sender (player) and used when that player creates a new arena.
- **If a name parameter is provided**, then the player would edit the position of the first player's spawn at an existing arena.

`/p2Spawn <optional - name>` - Sets the second player's spawn at the given position of the player who executes the command. 
- The name parameter refers to an arena name. 
- **If no name parameter is provided**, then the second player's spawn is saved to the command sender (player) and used when that player creates a new arena.
- **If a name parameter is provided**, then the player would edit the position of the second player's spawn at an existing arena.

`/edge1 <optional - name>` - Sets the first edge of the arena at the given position of the player who executes the command.
- The name parameter refers to an arena name. 
- **If no name parameter is provided**, then the first edge of the arena is saved to the command sender (player) and used when that player creates a new arena.
- **If a name parameter is provided**, then the player would edit the position of the first edge of an existing arena.

`/edge2 <optional - name>` - Sets the second edge of the arena at the given position of the player who executes the command.
- The name parameter refers to an arena name. 
- **If no name parameter is provided**, then the second edge of the arena is saved to the command sender (player) and used when that player creates a new arena.
- **If a name parameter is provided**, then the player would edit the position of the second edge of an existing arena.

## Features

Contains support for various kit plugins (each listed with the links for the repositories in the descriptions). **If you want me to add support for another kit plugin, create a new issue on this repository and provide the link to the kit plugin you want support for.**

#### Main Features 
- [x] Contains a default duel kit used in case no kits exist. 
- [x] Contains default duel arena generation in case no duel arenas exist.
- [ ] Ability to determine which kits within a specific plugin can be used for duels (SOON).
- [x] Ability to add custom duel arenas, etc...

#### Versions
- [x] 0.15.10 Support
	- [x] [AdvancedKits Support](http://github.com/luca28pet/AdvancedKits/tree/46df69c8386ea47ad4137901ea41976701625984 "AdvancedKits Support")
	- [x] [KitKb Support](http://github.com/jkorn2324/KitKnockback-0.15 "KitKb Support")
- [ ] 1.1.x Support
- [ ] Latest Support