.. include:: /Includes.rst.txt
.. _cb_skeleton:

=================
Kickstart command
=================

The command :bash:`make:content-block` creates a bare-minimum Content Block.
This is actually an alias for :bash:`content-blocks:create`, which is inspired
by the `EXT:make` extension.

Options
=======

..  confval-menu::
    :name: confval-make-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: content-type
   :name: make-content-type
   :required: true
   :default: content-element
   :type: string

   :bash:`content-element`, :bash:`page-type` or :bash:`record-type`

.. confval:: vendor
   :name: make-vendor
   :required: true
   :default: (vendor of root composer.json)
   :type: string

   Your vendor name. Lowercase, separated by dashes.

.. confval:: name
   :name: make-name
   :required: true
   :type: string

   Your Content Block name (this is not the title). Lowercase, separated by dashes.

.. confval:: extension
   :name: make-extension
   :required: true
   :type: string

   .. note::

      If you use the step-by-step process to navigate through the creation,
      only locally installed extensions will be displayed. In case you have
      a package installed via vcs, you have to provide it via CLI option.

   The host extension, where to store your new Content Block.

.. confval:: title
   :name: make-title
   :required: false
   :type: string

   The human-readable title for your Content Block.

.. confval:: type-name
   :name: make-type-name
   :required: false
   :type: string|int

   Custom type name. Required for content-type :bash:`page-type` (must be int).

   .. important::

       The :bash:`type-name` option is required and has to be an integer value, if you
       choose the :bash:`page-type` content type.

..  confval:: skeleton-path
    :name: make-skeleton-path
    :required: false
    :default: content-blocks-skeleton
    :type: string

    A path relative to the current working directory, which holds a skeleton of
    a Content Block. Only needed, if you want to use a different name or path
    other than `content-blocks-skeleton`.

    ..  card::
        :class: mb-4

        ..  directory-tree::
            :level: 4

            *   :path:`content-blocks-skeleton`

                *   :path:`content-element`

                    *   :path:`assets`

                        *   :file:`icon.svg`

                    *   :path:`templates`

                        *   :file:`backend-preview.fluid.html`
                        *   :file:`frontend.fluid.html`

                *   :path:`page-type`

                *  :path:`record-type`

    Learn more about :ref:`Content Blocks skeleton <cb_skeleton_path>`

..  confval:: config-path
    :name: make-config-path
    :required: false
    :default: content-blocks.yaml
    :type: string

    A path to a yaml config file path, which contains defaults for this command.

    Learn more about :ref:`Content Blocks defaults <cb_defaults>`

This will give you an overview of all available options:

.. code-block:: bash

   vendor/bin/typo3 make:content-block --help

Example creating a Content Block skeleton in one line:

.. code-block:: bash

   vendor/bin/typo3 make:content-block --content-type="content-element" --vendor="my-vendor" --name="my-name" --title="My shiny new Content Element" --extension="my_sitepackage"

Alternatively, the command can guide you through the creation by omitting the
required options:

.. code-block:: bash

   vendor/bin/typo3 make:content-block

On non-composer installations use:

.. code-block:: bash

   typo3/sysext/core/bin/typo3 make:content-block

Example interaction:

.. code-block:: bash

   Choose the content type of your content block [Content Element]:
   [content-element] Content Element
   [page-type      ] Page Type
   [record-type    ] Record Type
   > content-element

   Enter your vendor name:
   > my-vendor

   Enter your content block name:
   > my-content-block-name

   Choose an extension in which the content block should be stored:
   [sitepackage] Test Package for content blocks
   > sitepackage


After running the make command
==============================

In order to create newly added database tables or fields, you have to clear the
caches and then run the database compare. You can do the same in the TYPO3
Backend by using the Database Analyzer. Repeat this step every time you add new
fields to your Content Block definition.

.. code-block:: bash

   vendor/bin/typo3 cache:flush -g system
   vendor/bin/typo3 extension:setup --extension=my_sitepackage

.. _cb_skeleton_path:

Content Block skeleton
----------------------

.. versionadded:: 1.1

It is now possible to define a "skeleton" for your Content Blocks. To do this
create a folder called `content-blocks-skeleton` in your project root. This
folder may contain default templates or assets for one or more Content Types. It
is used as a base when creating new types with the :shell:`make:content-block`
command. In order to add a skeleton for Content Elements, create a folder called
`content-element` within that directory. Then, the structure is identical to
your concrete Content Block as you know it. You may place any files there. They
will simply be copied when a new Content Block is created. It is not possible to
define `language/labels.xlf` or `config.yaml` this way, as they are dynamically
generated based on your arguments.

..  card::
    :class: mb-4

    ..  directory-tree::
        :level: 4

        *   :path:`content-blocks-skeleton`

            *   :path:`content-element`

                *   :path:`assets`

                    *   :file:`icon.svg`

                *   :path:`templates`

                    *   :file:`backend-preview.fluid.html`
                    *   :file:`frontend.fluid.html`

            *   :path:`page-type`

            *  :path:`record-type`

In case you want to name the skeleton folder differently or place it somewhere
else, you can override the default folder by providing the option
:shell:`--skeleton-path` with a relative path to your current working directory.

..  code-block:: shell
    :caption: You can use an alternative skeleton path

    vendor/bin/typo3 make:content-block --skeleton-path="my-alternative-skeleton-path"

.. _cb_defaults:

Defaults
--------

.. versionadded:: 1.1

It is now possible to define default options for this command via a yaml config
file. By default, the command looks for a file called `content-blocks.yaml` in
the current working directory. The location and name can be overridden with the
:shell:`--config-path` option.

..  code-block:: shell

    vendor/bin/typo3 make:content-block --config-path="some-folder/my-config.yaml"

An example yaml config file contents may look like this:

..  code-block:: yaml
    :caption: content-blocks.yaml

    vendor: nh
    extension: content_blocks_examples
    content-type: record-type
    skeleton-path: folder1/content-block-skeletons

This config sets defaults for :yaml:`vendor`, :yaml:`skeleton-path`,
:yaml:`extension` and :yaml:`content-type`. These are all possible options right
now.

Now, whenever you run this command, these options will be set by default. This
does not mean, the questions for these options will be skipped, only that they
are the default value, if you just press `Enter` without any input. They will
be visible in brackets at the very right `[default value]`.

.. versionadded:: 1.3

The content-blocks.yaml file supports arbitrary default values for the generated
config.yaml file.

Add a :yaml:`config` key followed by a Content Type identifier. In this case,
we set default values for Content Elements, so we use :yaml:`content-element`.
You can also set default values for `page-type`, `record-type` or `file-type`.
Values defined in here override the generated configuration by the command.

.. code-block:: yaml

    config:
      content-element:
        basics:
          - TYPO3/Appearance
          - TYPO3/Links
        group: my_group
        prefixFields: true
        prefixType: vendor
