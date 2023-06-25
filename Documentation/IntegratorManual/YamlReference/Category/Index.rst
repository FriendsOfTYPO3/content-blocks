.. include:: /Includes.rst.txt
.. _field_type_category:

========
Category
========

The `Category` type can handle relations to categories. The categories are taken
from the system table :sql:`sys_categories`.

It corresponds with the TCA :php:`type => 'category'`.

SQL overrides via `alternativeSql` allowed: no.

Properties
==========

.. rst-class:: dl-parameters

relationship
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|`

   Depending on the relationship, the category relations is stored (internally)
   in a different way. Possible keywords are `oneToOne`, `oneToMany` or
   `manyToMany` (default).

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

treeConfig.startingPoints
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Allows to set one or more roots (category uids), from which the categories
   should be taken from.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/category
    group: common
    fields:
      - identifier: categories
        type: Category

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/category
    group: common
    fields:
      - identifier: categories
        type: Category
        minitems: 1
        treeConfig:
          startingPoints: 7
        relationship: oneToOne
