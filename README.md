rev engine
==========

A work in progress project.

### Changelog
 - 0.6alpha9: Static files' time of life, shorten changelog
 - 0.6alpha5-8: Cleanups, add MVC to View, User/Google auth, reorganize modules, optimalizations & fixes
 - 0.6alpha1-4: Rewritten autoloader, modloader and initialization, add CLI, config handler, modularize things a bit, static file handler, fix Error a bit, routing, cleanups, optimalizations
 - 0.5alpha2: Improved DB support, included basic authentication and session management
 - 0.4: Full MySQL database support, added session manager, lots of cleanups
 - 0.3: View render rewrite, tone down the code a bit
 - 0.2: Initial db support, initial View class & render
 - 0.1: Request path support, classes, errors, file management

### TODO
 - Errors module: ~~(trash,) rewrite~~ fix it, optimize, tone down
 - Explicit view generation: try to fix cross-referencing modules, then fail miserably
 - Some more types for static file server (like images, plaintext)
 - Cache module (_much, much later_)
 - Simplify DB - maybe move to PDO?
 - Fix 'safe exit' functionality in Mod
 - Cosmetic change: move mods from 'ext' to 'lib'
 - highlight.js


### Licensing
Code in this repository is subject to Creative Commons licence (CC BY-NC-SA).
Any contributions (ideas, new code/fixes) are welcome :D
