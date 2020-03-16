<p align="center">
	<a href="https://skriptbe.ga"><img src="https://skriptbe.ga/img/banner.png"></img></a><br>
	<b>Port of Bukkit Skript for PocketMine-MP</b>
</p>

Skript is a plugin for PocketMine-MP, which allows server owners and other people to modify their servers without learning PHP. You can load scripts from Bukkit plugin version.

# What is Skript?
Skript is a plugin that allows you to customize Minecraft's mechanics with simple scripts written in plain English sentences. 

# Requirements
SkriptBE requires latest PocketMine-MP.

# Download
You can download latest version plugin form <a href="https://skriptbe.ga">here</a>

# Install
Download plugin and put to plugins folder.

# How to use
Install plugin and drop scripts to folder **plugin_data/SkriptBE/scripts** (script file name must be end in .sk). Run server. You can see loaded scripts by command **/skript list**. In folder **plugin_data/SkriptBE/scripts** is scripts you can load it when you remove -- from file name.

# TO-DO
 - [x] configs and files support,
 - [ ] control structures eg. if, else (50 %),
 - [ ] variables (50 %),
 - [ ] entity support,
 - [ ] implement api,
 - [ ] add support for iProtector,
 - [x] boss bars,
 - [ ] mysql, sqlite3,
 - [ ] curl support,
 - [ ] more functions,
 - [ ] new events,
 - [ ] implement regions,

# Examples
Basic commands:
```
command /hello:
  permission: hello.permission
  permission message: You can not use this.
  description: "description for /hello command"
  trigger:
    send "<red>Hello"
```

Events:
```
on join:
    broadcast "&a%player% join to server!"
```

```
on quit:
    broadcast "&e%player% left from server!"
```

Timers:
```
every 5 minutes:
    broadcast "This plugin is great"
```

# Documentation
The documentation for this plugin is the same as for its Java versions. You can see it under the <a href="https://skriptlang.github.io/Skript/index.html">link</a>

# License
This project is licensed under LGPL-3.0.
