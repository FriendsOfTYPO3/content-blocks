.. include:: /Includes.rst.txt
.. _field_type_folder:

======
Folder
======

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

   Minimum number of items. Default is no minimum. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting :yaml:`minitems` to at least 1.

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

.. confval:: autoSizeMax
   :name: folder-autoSizeMax
   :required: false
   :type: integer

   The field will never grow larger than this number of visible rows.

.. confval:: behaviour.allowLanguageSynchronization
   :name: folder-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: default
   :name: folder-default
   :required: false
   :type: string

   Default value set if a new record is created.

.. confval:: fieldControl
   :name: folder-fieldControl
   :required: false
   :type: object

   See :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`. The
   :yaml:`elementBrowser` control can be disabled via
   :yaml:`fieldControl.elementBrowser.disabled: true`.

.. confval:: fieldInformation
   :name: folder-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: folder-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: hideDeleteIcon
   :name: folder-hideDeleteIcon
   :required: false
   :type: boolean

   Removes the delete icon next to the selector box.

.. confval:: hideMoveIcons
   :name: folder-hideMoveIcons
   :required: false
   :type: boolean

   Removes the move icons next to the selector box.

.. confval:: multiple
   :name: folder-multiple
   :required: false
   :type: boolean
   :default: false

   Allows the same folder to be selected more than once in the list.

.. confval:: readOnly
   :name: folder-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

.. confval:: size
   :name: folder-size
   :required: false
   :type: integer
   :default: 1

   Number of visible rows in the selector box. A value of 1 displays a
   drop-down.

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
        <f:for each="{folder.files}" as="item">
            <f:image image="{item}" />
        </f:for>
    </f:for>
