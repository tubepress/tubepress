This directory contains the add-ons supplied by TubePress, which comprise the bulk of TubePress's core functionality.
**The names of the subdirectories here do not matter** as they should never be accessed by the public. You should
also **not** rely on the directory names here if you are programming against TubePress. That said, in order to conceptually
group the add-ons together, we have created the following subdirectories:

* `base` Low-level classes, interfaces, and services that could be used outside of TubePress. These add-ons have
         they have nothing TubePress-specific in them, other than the fact that they are packaged inside
         a TubePress add-on.

* `core` Classes and services that provide TubePress-specific functionality.

* `deprecated` Provides bridges to deprecated classes and themes to prevent BC breaks.
               You should avoid using anything within.

* `integration` TubePress bindings to external software, services, and/or environments.