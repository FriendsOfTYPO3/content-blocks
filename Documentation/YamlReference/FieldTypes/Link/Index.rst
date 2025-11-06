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
            - [ 'TYPO3 CMS', 'https://www.typo3.org' ]
            - [ 'TYPO3 GmbH', 'https://www.typo3.com' ]


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
