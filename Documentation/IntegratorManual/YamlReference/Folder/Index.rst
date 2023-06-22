.. include:: /Includes.rst.txt
.. _field_type_folder:

======
Folder
======

The `Folder` type enables to select one or more folders. Files within these
folders will be resolved automatically and are available in Fluid.

It corresponds with the TCA :php:`type => 'folder'`.

SQL overrides via `alternativeSql` allowed: yes.

Top level settings
==================

.. rst-class:: dl-parameters

recursive
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|`

   Files in the selected folder will be resolved recursively.

Properties
==========

elementBrowserEntryPoints
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|`

   Enables to set an entrypoint, from which to select folders by default.

maxitems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   Maximum number of items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

minitems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   Minimum number of items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting `minitems` to at least 1.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-folder>`.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/folder
    group: common
    fields:
      - identifier: folder
        type: Folder

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/folder
    group: common
    fields:
      - identifier: folder
        type: Folder
        recursive: true
        elementBrowserEntryPoints:
          _default: '1:/styleguide/'
        minitems: 1
