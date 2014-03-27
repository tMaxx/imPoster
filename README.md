rev engine // r3vCMS
====================

A work in progress project.

### Changelog
 - 0.6alpha3/4: Working static file handler (for now), fix Error a bit, routing, cleanups, optimalizations
 - 0.6aplha2: Moved various things into modules, revamp autoloader (_again..._) and modloader, got CLI working (_again..._), trying to launch route scopes, extend Conf (stub for now)
 - 0.6alpha1: Rewritten autoloader (basic compatibility with composer) and initialization, added CLI through [joddie/boris](https://github.com/joddie/boris/)
 - 0.5alpha2: Improved DB support, included basic authentication and session management (_no snapshot available_)
 - 0.4: Full MySQL database support, added session manager, lots of cleanups
 - 0.3: View render rewrite, tone down the code a bit
 - 0.2: Initial db support, initial View class & render
 - 0.1: Request path support, classes, errors, file management

### TODO
 - Throw old authentication to dumpster and write a new one
 - _Maybe_ extend templates
 - Invent a way to store db passwords in config so that it could be pushed into repo
 - Errors module: (trash,) rewrite
 - Explicit view generation: try to fix cross-referencing modules, then fail miserably
 - Finish static view generator
 - Some more types for static file server

### Licensing
Code in this repository is subject to Creative Commons licence (CC BY-NC-SA).
Any contributions (ideas, new code/fixes) are welcome :D
