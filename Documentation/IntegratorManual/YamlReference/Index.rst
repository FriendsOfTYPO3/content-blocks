.. include:: /Includes.rst.txt
.. _yaml_reference:

==================================
Editing interface (YAML reference)
==================================

The editing interface configuration contains mostly view-related properties of
the fields (Exception is field :yaml:`alternativeSql`, which is database-related).
Therefore, a descriptive language (as YAML) is sufficient and does not open up a
possible security flaw.

A strict schema for field types is used to ease up the validation process for
field definitions. To keep it slim and easy to read, the mapping to TCA uses
strong defaults for field properties (e.g. default size for input is 30).

The field types for the EditorInterface.yaml are heavily inspired by the
`Symfony field types <https://symfony.com/doc/current/reference/forms/types.html>`__
and is mapped to TCA. Because Symfony is quite mainstream, well-established
and documented it makes it easier to understand those types for TYPO3 newcomers/
beginners/ frontend-only devs than TYPO3's exclusive TCA, thus providing a kind
of ubiquitous language.

General definitions
===================

name
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` string
   :sep:`|`

   Every editing interface configuration must contain exactly one name. The name is made up of vendor and package separated by a "/" like
   the `vendor/package` notation in a traditional composer.json file must be unique and must have at least 3 characters. Content Blocks which
   are placed in the `ContentBlocks` folder at any of your extensions will be determined and loaded automatically.

priority
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   The priority can be used to prioritize certain content blocks in the loading
   order. The default loading order is alphabetically. Higher priorities will be
   loaded before lower ones. This affects e.g. the order in the "New Content
   Element Wizard".

Field definitions
=================

Common field properties
-----------------------
.. rst-class:: dl-parameters

identifier
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` string
   :sep:`|`

   The field's identifier has to be unique within a Content Block. Exception is
   within a collections' field array, as this starts a new scope.

type
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` string
   :sep:`|`

   The field's type. See :ref:`field_types`.

properties
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|`

   Array of properties that are dependent on the :ref:`field_types`.

useExistingField
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` bool
   :sep:`|`

   If set to true, the identifier is treated as an existing field from the Core
   or your own defined field. **Important**: Make sure `type` matches the actual
   type in TCA.

alternativeSql
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|`

   It is possible to override the default SQL definition of a field with this
   option. Example :sql:`tinyint(2) DEFAULT '0' NOT NULL`. Not every field type
   can be overridden. Have a look at the standard SQL definition of the
   corresponding field.

.. _field_types:

Field types
-----------

.. toctree::
    :maxdepth: 1
    :titlesonly:

    Category/Index
    Checkbox/Index
    Collection/Index
    Color/Index
    DateTime/Index
    Email/Index
    File/Index
    Linebreak/Index
    Link/Index
    Number/Index
    Palette/Index
    Radio/Index
    Reference/Index
    Select/Index
    Tab/Index
    Text/Index
    Textarea/Index
