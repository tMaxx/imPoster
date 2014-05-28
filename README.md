rev engine
==========

PHP framework. CLI, MVC, PDO, CRUD and much more.

### Changelog
 - 0.6beta2: CRUD stub, Form refactor
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
 - Some more types for static file server (like images, plaintext, css w/o parser)
 - Cache module (_much, much later_)
 - highlight.js
 - SCSS: consider writing own server, current one is hellish slow (& add cross-referencing of styles w. updating etags)
 - DB: make it honor PDO's :variable binding in query, optimize
 - find some other Google Authenticator class
 - add breadcrumbs generator
 - static/JS: add cross-reference of scripts

### Licensing
Code in this repository is subject to Creative Commons licence (CC BY-NC-SA).
Any contributions (ideas, new code, fixes) are welcome :D
