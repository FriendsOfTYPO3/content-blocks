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

table
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` tt_content
   :sep:`|`

   It is possible to create a new content types with another table. This is
   especially useful if you want e.g. to store contacts or similar in a storage
   folder and use them in different places. These won't create a new content
   element as with tt_content types.

typeField
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` CType
   :sep:`|`

   The field to use as the type switch. Should be a type Select field. Useful in
   combination with another :yaml:`table`.

typeName
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` 1 or automatically generated
   :sep:`|`

   The identifier of the new content type. It is automatically generated from
   the name, if not defined manually.

prefixFields
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` true
   :sep:`|`

   By default, all fields are prefixed with the name of the content block to
   prevent collisions. In order to better reuse fields between content blocks,
   it can be useful to deactivate this option. Read more about
   :ref:`reusing fields here <cb_reuse_existing_fields>`.

aggregateRoot
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` true
   :sep:`|`

   By default, all tables are treated as `aggregateRoot`. This means, this table
   is not a child-table of another root. By assigning this option the `false`
   value, additional fields are created to enable a reference to a parent table:
   :sql:`foreign_table_parent_uid`, :sql:`tablenames` and :sql:`fieldname`. Now,
   a type Collection field can define :yaml:`foreign_table` with this table.
   When referencing an existing table, you need to take care yourself that these
   fields exist. Also, non-aggregate tables are hidden in the List module.

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

label
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|`

By default labels should be defined inside the :file:`Labels.xml` file. But in
case there is only one language for the backend you may define labels directly
in the YAML configuration. This has precedence over translation files.

description
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|`

See `label` above.

useExistingField
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` bool
   :sep:`|`

   If set to true, the identifier is treated as an existing field from the Core
   or your own defined field.

alternativeSql
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|`

   It is possible to override the default SQL definition of a field with this
   option. Example :sql:`tinyint(2) DEFAULT '0' NOT NULL`. Not every field type
   can be overridden. Have a look at the standard SQL definition of the
   corresponding field.

prefixField
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` true
   :sep:`|`

   If set to false, the prefixing is disabled for this field. This overrules the
   global option :yaml:`prefixFields`.

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
    FlexForm/Index
    Folder/Index
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
