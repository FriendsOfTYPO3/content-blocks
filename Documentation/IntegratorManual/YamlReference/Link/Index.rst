.. include:: /Includes.rst.txt
.. _field_type_link:

====
Link
====

The `Link` type creates a field with a link wizard. It is possible to link to
pages, files or even records (if configured).

It corresponds with the TCA :php:`type => 'link'`.

SQL overrides via `alternativeSql` allowed: yes.

Properties
==========

.. rst-class:: dl-parameters

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Default value set if a new record is created.

required
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the field will become mandatory.

allowedTypes
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` '[*]'
   :sep:`|`

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
    group: common
    fields:
      - identifier: url
        type: Link

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/link
    group: common
    fields:
      - identifier: url
        type: Link
        properties:
          autocomplete: true
          default: 'https://typo3.org'
          allowedTypes: ['page', 'url', 'file']
          required: false
          valuePicker:
            items:
              [
                ['https://www.typo3.org', TYPO3 CMS],
                ['https://www.typo3.com', TYPO3 GmbH],
              ]
