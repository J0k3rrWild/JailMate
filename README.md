# JailMate - for PMMP API 4.0.3

You need this plugin for API 3.26.2 instead? [CLICK HERE](https://github.com/J0k3rrWild/JailMate/tree/api4)

[![](https://poggit.pmmp.io/shield.state/JailMate)](https://poggit.pmmp.io/p/JailMate)
[![](https://poggit.pmmp.io/shield.dl.total/JailMate)](https://poggit.pmmp.io/p/JailMate)

## General info

Smart plugin that will allow you to safely check suspicious players

## Table of contents
* [Commands](#Commands)
* [Permissions](#Permissions)
* [Detailed information](#Detailed-information)
* [Social Media](##Social-media)


## Commands

```/jail <nick>``` - Arrests a suspicious player <br><br>
```/setjail confirm``` - Set jail position <br><br>
```/unjail <player>``` - Unjail player

## Permissions

```jail``` - Gives authority to all arrest commands & bypass<br><br>
```jail.setjail``` - Gives the powers of the place to establish an arrest<br><br>
```jail.jail``` - Gives you the power to release from arrest and arrest others<br><br>
```jail.bypass``` - Gives power to bypass aresst (anyone with this permission cannot be arrested)

## Detailed information

### Blocked events while arrested:

```PlayerMoveEvent``` - Players can't move while arrested<br><br>
```PlayerCommandPreprocessEvent``` - Players can't use all commands while arrested<br><br>
```PlayerDropItemEvent``` - Players can't drop items while arrested<br><br>
```BlockPlaceEvent``` - Players can't place blocks while arrested<br><br>
```BlockBreakEvent``` - Players can't destroy blocks while arrested<br><br>

### Additional security:

* When a player quits the game while being checked, they will be automatically banned (ban name)<br>
* If the server is shut down (for example by a crash or restart) during the interrogation, the player will be arrested again when he next time enter the game<br><br><br>




## Social media

[![](https://img.shields.io/badge/Discord-7289DA?style=for-the-badge&logo=discord&logoColor=white)](https://discord.gg/8b3rKZPYM8)

