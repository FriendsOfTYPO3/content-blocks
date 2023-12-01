.. include:: /Includes.rst.txt
.. _field_type_link:

====
Link
====

:php:`type => 'link' // TCA`

The :yaml:`Link` type creates a field with a link wizard. It is possible to link
to pages, files or even records (if configured).

Settings
========

.. confval:: default

   :Required: false
   :Type: string
   :Default: ''

   Default value set if a new record is created.

.. confval:: required

   :Required: false
   :Type: boolean
   :Default: false

   If set, the field becomes mandatory.

.. confval:: allowedTypes

   :Required: false
   :Type: array
   :Default: '[*]'

   Allow list of link types. Possible values are :yaml:`page`, :yaml:`url`,
   :yaml:`file`, :yaml:`folder`, :yaml:`email`, :yaml:`telephone` and
   :yaml:`record`.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-link>`.

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
            - [ 'https://www.typo3.org', 'TYPO3 CMS' ]
            - [ 'https://www.typo3.com', 'TYPO3 GmbH' ]
