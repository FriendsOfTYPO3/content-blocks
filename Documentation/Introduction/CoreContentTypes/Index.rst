.. include:: /Includes.rst.txt
.. _core-content-types:

==================
Core Content Types
==================

Here is an overview of the required and optional languages / places for defining
new Content Types:

**SQL**

When you need a custom field or a custom table, first of all you have to define
a SQL schema. This requires knowledge of MySQL syntax and which column types are
suitable for the field. Content Blocks already knows which types are the best
for the job and adds them via a PSR-14 Event. For experienced integrators, it is
still possible to override the default SQL definitions.

**TCA**

The "Table Configuration Array" is a TYPO3 specific PHP structure to define the
behavior of SQL columns inside the TYPO3 backend. It is quite mighty and has
plenty of options. In order to not limit the capabilities of Content Blocks all
options and field types are still available. You will recognize them in the YAML
definition. There are some improvements, which required slight naming changes.
Content Blocks greatly reduces the amount of code, which needs to be defined, by
automatically generating redundant information. E.g. by adding new types to the
type selector or placing custom fields to a designated destination. Also icons
are registered automatically, if they are placed in a fixed folder.

**XML / FlexForm**

If one wants to make use of TYPO3 FlexForms, it is required to define an XML
file and register it in TCA. Content Blocks streamlines this by using the same
syntax for both normal fields and FlexForm fields. There is no need to write
TCA inside of XML anymore. Everything is handled in the single YAML definition.

**XML / XLF**

Translatable labels are implemented by using XLF files in TYPO3. In Content
Blocks you still have to write those. However, all fields get a fixed key, which
consists of the Content Block name and the field identifier. You only need to
remember this standardized schema and they will automatically be used for the
backend labels. There is a fallback to the field identifier, if no entry for the
field is defined, so that you never encounter fields without a label.

**TSConfig**

TSConfig is special variant of TypoScript, which allows to modify the backend
behavior. It is required to define some TSConfig in order to register Content
Elements in the Content Element Wizard. Content Blocks already holds all
necessary information for this and adds default TSConfig. If not otherwise
defined, new Content Elements are placed in the "common" tab.

**TypoScript**

TypoScript is the glue between the backend and the frontend. For Content
Elements specifically it is required to define a rendering definition, if you
intend to display them in the frontend. Content Blocks automatically adds
default TypoScript, which registers a Fluid template for the Content Element. In
addition, all relations are resolved automatically, so you don't have to define
DataProcessors for common use cases like file references or collections.

**Fluid**

Fluid is the templating engine for TYPO3 and is both used in the backend and the
frontend. Content Blocks registers a `Frontend.html` and a `EditorPreview.html`
file, which can immediately be used for styling your Content Element. Layouts
and partials can be used as usual.
