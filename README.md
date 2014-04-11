rev engine
==========

A work in progress project.

### Changelog
 - 0.6alpha7/8: Config fixes, micro-refactor of View, Google auth, move modules a bit, User auth, Console class, various optimizations
 - 0.6alpha5/6: Introduced MVC, some performance optimalizations, class organization, various other fixes
 - 0.6alpha3/4: Working static file handler (for now), fix Error a bit, routing, cleanups, optimalizations
 - 0.6aplha2: Moved various things into modules, revamp autoloader (_again..._) and modloader, got CLI working (_again..._), trying to launch route scopes, extend Conf (stub for now)
 - 0.6alpha1: Rewritten autoloader (basic compatibility with composer) and initialization, added CLI through [joddie/boris](https://github.com/joddie/boris/)
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
 - Static files: time of life
 - Simplify DB - maybe move to PDO?
 - Fix 'safe exit' functionality in Mod


### Licensing
Code in this repository is subject to Creative Commons licence (CC BY-NC-SA).
Any contributions (ideas, new code/fixes) are welcome :D
