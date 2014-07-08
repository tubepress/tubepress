### Organization of this directory is as follows:

* `core` Individual add-ons supplied by TubePress. Upon initial/uncached boot,
         the `platform` (see below) will search in this directory for add-on manifests
         i.e. files named (`manifest.json`). The bulk of the codebase lives here.

* `platform` Classes and scripts to discover and assemble add-ons into a single service container.
             See `platform/scripts/boot.php` for details on how to access this service container.

* `translations` [.po and .mo files](https://en.wikipedia.org/wiki/Gettext) for translating TubePress into
                 languages other than US English.