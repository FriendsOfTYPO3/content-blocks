.. include:: /Includes.rst.txt

.. _yaml_reference_common:

===================
Common root options
===================

..  confval-menu::
    :name: confval-common-root-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: name
   :name: root-name
   :required: true
   :type: string

   Every editing interface configuration must contain exactly one name. The name
   is made up of vendor and content block name separated by a `/` just like the
   `vendor/package` notation in a traditional composer.json file. It must be
   unique and must have at least 3 characters. Both parts, the vendor and
   content block name, have to be lowercase and be separated by a dash `-`.

   .. code-block:: yaml

       name: my-vendor/my-content-block-name

.. confval:: title
   :name: root-title
   :required: false
   :type: string

   This is the title of the Content Block. If you have a labels.xlf file, you
   should define it there with the key :xml:`title`. If both are defined, the
   translation file has precedence. If nothing is defined, the title falls back
   to :yaml:`name`.

   .. code-block:: yaml

       title: "My super duper Content Block"

.. confval:: prefixFields
   :name: root-prefixFields
   :required: false
   :type: boolean
   :default: true

   The default behavior is to convert the both :yaml:`name` parts into a prefix.
   All dashes are removed in this process and the parts are combined with an
   underscore to prevent collisions. In order to better reuse fields between
   Content Blocks, it can be useful to deactivate this option.

   .. code-block:: yaml

       prefixFields: false

   Read more about :ref:`prefixing <api_prefixing>`.

.. confval:: prefixType
   :name: root-prefixType
   :required: false
   :type: string
   :default: full

   Determines how to prefix the field if :yaml:`prefixFields` is enabled. Can be
   either :yaml:`full` (default) or :yaml:`vendor`. The latter removes the
   second part of :yaml:`name` from the prefix.

   .. code-block:: yaml

       prefixFields: true
       prefixType: vendor

   Read more about :ref:`prefixing <api_prefixing>`.

.. confval:: vendorPrefix
   :name: root-vendorPrefix
   :required: false
   :type: string

   If set, this prefix will be used instead of the vendor part of :yaml:`name`.
   This is especially useful if you want to adhere to the best practice of
   prefixing fields with **tx_extension**.

   .. code-block:: yaml

       vendorPrefix: tx_sitepackage

   Read more about :ref:`prefixing <api_prefixing>`.

.. confval:: priority
   :name: root-priority
   :required: false
   :type: integer
   :default: "0"

   The priority can be used to prioritize certain Content Blocks in the loading
   order. Higher priorities will be loaded before lower ones. This affects e.g.
   the order in the "New Content Element Wizard".

   .. code-block:: yaml

       # This Content Block will be displayed before others without a priority set.
       priority: 10

   .. note::

      The **default** loading order is **undefined** and depends on the
      (file-)system and the order, in which extensions are loaded.

.. confval:: basics
   :name: root-basics
   :required: false
   :type: array

   Globally defined :yaml:`basics` are appended to the very end of your
   :yaml:`fields` array. Most commonly used to include shared
   :ref:`Tabs <field_type_tab>`.

   ..  code-block:: yaml

       basics:
           - TYPO3/Appearance
           - TYPO3/Links

   Can also be used as a Field Type :ref:`Basic <field_type_basic>`.

   Learn more about the concept of :ref:`Basics <basics>`.

.. confval:: fields
   :name: root-fields
   :required: false
   :type: array

   The main entry point for the field definitions. Fields defined in this array
   are displayed in the backend exactly in the same order. You can create new
   custom fields or reuse existing ones, which are defined via TCA. Learn
   :ref:`here <yaml_reference_field_properties>` what is needed to define a
   field.

   .. code-block:: yaml

       fields:
           - identifier: my_field
             type: Text
