rev engine
==========

A work in progress project.

### Changelog
 - 0.6beta1: Release. Finally.
 - 0.6alpha9-10: Static files' time of life, shorten changelog, app-side work, DB to PDO, git submodules
 - 0.6alpha5-8: Cleanups, add MVC to View, User/Google auth, reorganize modules, optimalizations & fixes
 - 0.6alpha1-4: Rewritten autoloader, modloader and initialization, add CLI, config handler, modularize things a bit, static file handler, fix Error a bit, routing, cleanups, optimalizations
 - 0.5alpha2: Improved DB support, included basic authentication and session management
 - 0.4: Full MySQL database support, added session manager, lots of cleanups
 - 0.3: View render rewrite, tone down the code a bit
 - 0.2: Initial db support, initial View class & render
 - 0.1: Request path support, classes, errors, file management

### TODO
 - Explicit view generation: try to fix cross-referencing modules, then fail miserably
 - Some more types for static file server (like images, plaintext)
 - Cache module (_much, much later_)
 - Fix 'safe exit' functionality in Mod
 - highlight.js
 - URI Vars: allow only int or letters (a-zA-Z0-9)
 - SCSS: consider writing own server, current one is hellish slow
 - DB: try to speed up a bit, simplify, interface names (IEnableable)
 - statics: allow also plain old css file serving (no scss parsing)
 - extend Form's field renderer

### Licensing
Code in this repository is subject to Creative Commons licence (CC BY-NC-SA).
Any contributions (ideas, new code/fixes) are welcome :D
