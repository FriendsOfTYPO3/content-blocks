.. include:: /Includes.rst.txt
.. _changelog-1.5:

===
1.5
===

Content Blocks version 1.5 adds new features.

..  contents::

Feature
=======

Content Blocks as Site Set
--------------------------

`Site sets <https://docs.typo3.org/permalink/t3coreapi:site-sets>`__ are a
feature introduced with TYPO3 v13. They allow to manage dependencies on a per
site basis. These dependencies may contain site settings, TypoScript or page
TSconfig.

Content Blocks adds an integration into this dependency management system by
adding each loaded Content Block as a Site Set. By doing so, it is now possible
to require different Content Blocks for each site in your installation. On top
of this, auto-loading for TypoScript and page TSconfig is added, just like for
normal Site Set definitions.

..  figure:: /API/_Images/site-sets.jpg
    :alt: Content Blocks as Site Set

.. code-block:: yaml
   :caption: EXT:site_package/Configuration/Sets/Example/config.yaml

    name: my-vendor/my-set
    label: My Set
    dependencies:
      - example/card-group
      - example/cta

..  card::
    :class: mb-4

    ..  directory-tree::
        :level: 4

            *   :path:`my-content-block`

                *   :file:`config.yaml`
                *   :file:`setup.typoscript`
                *   :file:`page.tsconfig`

Read more in the dedicated :ref:`feature article <api_site_sets>`.
