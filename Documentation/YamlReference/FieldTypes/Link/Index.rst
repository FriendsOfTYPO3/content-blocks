.. include:: /Includes.rst.txt
.. _field_type_link:

====
Link
====

The :yaml:`Link` type creates a field with a link wizard. It is possible to link
to pages, files or even records (if configured). This field is resolved to an
object of type :php:`\TYPO3\CMS\Core\LinkHandling\TypolinkParameter`.

Settings
========

..  confval-menu::
    :name: confval-link-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: default
   :name: link-default
   :required: false
   :type: string
   :default: ''

   Default value set if a new record is created.

.. confval:: required
   :name: link-required
   :required: false
   :type: boolean
   :default: false

   If set, the field becomes mandatory.

.. confval:: searchable
   :name: link-searchable
   :required: false
   :type: boolean
   :default: true

   If set to false, the field will not be considered in backend search.

.. confval:: nullable
   :name: link-nullable
   :required: false
   :type: boolean
   :default: false

   If set, the field value will resolve to `null` if no link is provided.
   Useful, if field is optional.

.. confval:: allowedTypes
   :name: link-allowedTypes
   :required: false
   :type: array
   :default: '[*]'

   Allow list of link types. Possible values are :yaml:`page`, :yaml:`url`,
   :yaml:`file`, :yaml:`folder`, :yaml:`email`, :yaml:`telephone` and
   :yaml:`record`.

.. confval:: appearance.allowedOptions
   :name: link-appearance.allowedOptions
   :required: false
   :type: array

   Controls which options are displayed in the link browser. Omit to allow all
   options. Set to an empty array :yaml:`[]` to deny all. Possible values:
   :yaml:`class`, :yaml:`params`, :yaml:`target`, :yaml:`title`, :yaml:`rel`,
   :yaml:`body`, :yaml:`cc`, :yaml:`bcc`, :yaml:`subject`.

.. confval:: appearance.allowedFileExtensions
   :name: link-appearance.allowedFileExtensions
   :required: false
   :type: array

   List of file extensions shown in the file browser. If empty, all extensions
   are allowed.

.. confval:: appearance.browserTitle
   :name: link-appearance.browserTitle
   :required: false
   :type: string

   Custom title attribute for the link browser icon. Defaults to
   :yaml:`Link`.

.. confval:: appearance.enableBrowser
   :name: link-appearance.enableBrowser
   :required: false
   :type: boolean
   :default: true

   Set to :yaml:`false` to disable the link browser icon.

.. confval:: autocomplete
   :name: link-autocomplete
   :required: false
   :type: boolean

   Enables or disables browser autocomplete for the field.

.. confval:: behaviour.allowLanguageSynchronization
   :name: link-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: fieldControl
   :name: link-fieldControl
   :required: false
   :type: object

   See :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`.

.. confval:: fieldInformation
   :name: link-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: link-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: mode
   :name: link-mode
   :required: false
   :type: string

   When set to :yaml:`useOrOverridePlaceholder`, a checkbox appears above the
   field allowing the user to override the placeholder value.

.. confval:: placeholder
   :name: link-placeholder
   :required: false
   :type: string

   Placeholder text displayed inside the field when it is empty.

.. confval:: readOnly
   :name: link-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

.. confval:: size
   :name: link-size
   :required: false
   :type: integer
   :default: 30

   Abstract width of the input field. Minimum :yaml:`10`, maximum :yaml:`50`.

.. confval:: valuePicker
   :name: link-valuePicker
   :required: false
   :type: object

   Renders a select box next to the field from which predefined values can be
   inserted. Requires an :yaml:`items` array of objects with :yaml:`label` and
   :yaml:`value` keys.

   Example:

   .. code-block:: yaml

      valuePicker:
        items:
          - label: 'TYPO3 CMS'
            value: 'https://www.typo3.org'
          - label: 'TYPO3 GmbH'
            value: 'https://www.typo3.com'

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/link
    fields:
      - identifier: url
        type: Link

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/link
    fields:
      - identifier: url
        type: Link
        autocomplete: true
        default: 'https://typo3.org'
        allowedTypes:
          - page
          - url
          - file
        required: true
        valuePicker:
          items:
            - label: 'TYPO3 CMS'
              value: 'https://www.typo3.org'
            - label: 'TYPO3 GmbH'
              value: 'https://www.typo3.com'


Usage in Fluid
==============

As this field is an object of type :php:`\TYPO3\CMS\Core\LinkHandling\TypolinkParameter`
you have to check for the property :html:`url` to determine whether the field is
set or not.

.. note::

    Alternatively, you can set the field :yaml:`nullable: true`. In this case
    the value will resolve to `null` if not set.


.. code-block:: html

    <f:if condition="{data.link_field.url}">
        <f:link.typolink parameter="{data.link_field}">Link</f:link.typolink>
    </f:if>
