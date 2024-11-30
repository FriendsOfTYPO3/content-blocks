.. include:: /Includes.rst.txt
.. _field_type_folder:

======
Folder
======

:php:`type => 'folder' // TCA`

The :yaml:`Folder` type enables to select one or more folders. This field type
is resolved to an array of :php:`\TYPO3\CMS\Core\Resource\Folder` objects.

Settings
========

..  confval-menu::
    :name: confval-folder-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: elementBrowserEntryPoints
   :name: folder-elementBrowserEntryPoints
   :required: false
   :type: array

   Enables to set an entrypoint, from which to select folders by default.

.. confval:: maxitems
   :name: folder-maxitems
   :required: false
   :type: integer

   Maximum number of items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

.. confval:: minitems
   :name: folder-minitems
   :required: false
   :type: integer

   Minimum number of items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting `minitems` to at least 1.

.. confval:: relationship
   :name: folder-relationship
   :required: false
   :type: string
   :default: oneToMany

   The relationship defines the cardinality between the relations. Possible
   values are :yaml:`oneToMany` (default), :yaml:`manyToOne` and
   :yaml:`oneToOne`. In case of a [x]toOne relation, the processed field will
   be filled directly with the folder instead of a collection of folders. In
   addition, :yaml:`maxitems` will be automatically set to :yaml:`1`.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-folder>`.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/folder
    fields:
      - identifier: folder
        type: Folder

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/folder
    fields:
      - identifier: folder
        type: Folder
        elementBrowserEntryPoints:
          _default: '1:/styleguide/'
        minitems: 1


Usage in Fluid
==============

In most cases you want to retrieve the files within the folders. To achieve
this, you have to access the files via the :php:`getFiles()` method. In Fluid
this looks like this for a field with :yaml:`identifier: folder`.

.. hint::

    If you've defined :yaml:`relationship: manyToOne`, then you can omit the
    outer :html:`f:for` loop.

.. code-block:: html

    <f:for each="{data.folder}" as="folder">
        <f:for each="{folder.files}" as="image">
            <f:image image="{item}" />
        </f:for>
    </f:for>
