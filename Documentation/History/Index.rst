.. include:: /Includes.rst.txt

.. _cb_history:

=================
History of the Content Blocks
=================

The beginning
=============

The content blocks concept is a outcome of the structured content initiative.
Since there is a consensus in the community that we appreciate nearly endless possibilities
to create a new content element in TYPO3, but the creation of a simple content element is
still a very complex task.

There where different ideas and solutions for that issue in TER, which download count
proves that there is a need for a solution. So the community aimed for a solution shipped
by the core.

So in 2019 the structured content initiative reached out to the community to find a
solution for this aim. The initiative analyzed the existing solutions, extensions in
the TER like EXT:mask, EXT:dce, EXT:flux and summarized the pros and cons of each solution.
After that, there were different ideas and solutions developed and discussed in the community.

With the feedback of the community, the structured content initiative came up with
a concept for a new solution called the "content blocks", the smallest chunk of information
which is needed to create a new content element.


The content blocks concept
==========================

The content blocks concept is a new way to create content elements in TYPO3.

The outcome of the research and the discussions in the community is:

* A content block is a definition package in your {extDir}/ContentBlocks/ folder, which will be determined and loaded automatically
* A content block is not a TYPO3 Extension, so there is no PHP, TypoScript, SQL nor TSconfig inside
* The configuration for a new content element is stored in a YAML file, the EditorInterface.yaml
* The content blocks is made for frontend developers as main target group, so the folder structure is /src/ for private resources and /dist/ for public resources
* As a common best practice in TYPO3, labels and every other language related stuff is stored in a XLF file. This will be registered an processed by convention
* The entered name (vendor/package) in the EditorInterface.yaml file defines the identifier of a content block
* GUI to create / kickstart a new content block
* If there are breaking changes, support e.g via UpgradeWizards to migrate easily to the new version
* Not limited to fluid, twig or other technologies are also possible in the template
* Better UX for editors by shipping a template (EditorPreview.html) for the backend preview
* Usage of the AssetCollector: JavaScript and CSS in backend and frontend
* The content blocks are capsulated, in a own folder
* You can move a content block from one project to another project and it will work out of the box
* The content blocks are a standalone solution, so you can use it without Fluid Styled Content or other bootstrap extensions



What it does:
=============

Basically, the content blocks register the new content element by the entered vendor/package names in the corresponding EditorInterface.yaml
in TYPO3 and the newContentElementWizard, and translate the YAML-file into TCA and SQL.
It registers the Labels.xlf and sets the labels and descriptions by the field identifiers,
register the icon and adds the necessary TypoScript.

So it is a abstraction layer to ease up and speed up the work of integrators and frontend developers.
But in the end, it outputs the same definitions as a normal TYPO3 content element, which can be
overwritten by the integrator.


The first proof of concept
==========================

The first proof of concept was created by the structured content initiative and is called
the contentblocks_reg_api. This extension introduces the new API to TYPO3 v10 and v11.
In this first proof of concept, the content blocks are stored in a FlexForm structure.
But even if this was just a proof of concept, the community was not happy with store data as FlexForm.
The POC delivers beside the API also a simple GUI to kickstart a new content block.
The collections in this state are stored in one single table.
The field types at this point were oriented on the Symfony types.


Learnings from the contentblocks_reg_api
========================================

Learnings are:

* The community is not happy with the FlexForm pre solution
* The GUI is essential for the usage
* Overwrite the TCA is essential to add custom functions
* Copy and paste is useful and easy
* Writing the YAML file is not so hard as expected, but a GUI for editing would be beneficial
* The identifiers in the Labels.xlf are not so easy to work with, would be better to have an GUI for that
* The GUI / kickstarter in big projects should not be available in production environment


The data storage research
=========================

The first proof of concept was stored the structure and data in FlexForm. But despite the hint,
that this is just for getting a first impaction, this leads to massive contrary feedback from
the community. Meanwhile the structured content initiative did a research and discussed the
different possibilities to store the data.
The possible solutions were:

* FlexForm
* JSON / JSON Blob
* Database columns
* EAV (Entity Attribute Value)

The result of the research was, that the best solution is to store the data in the database.

Summary of the decision:
------------------------

**FlexForm:**

Store data in FlexForm is good for a quick structure, it delivers the possibilities of sections
which allows to store a kind of inline data or repeatable elements without the need of a custom
table. But the main problem is, that the data is stored in a XML structure, which is not easy to
work with. It doesn't provide all of the TCA features and there were some challenges with translations.
Furthermore, the community replayed that they are not happy with the FlexForm solution of the
proof of concept.

**JSON / JSON Blob:**

The JSON solution is a good solution for storing the data, but there is a big issue with the
that: There is no DataProcessor for JSON in TYPO3 at the moment. So this has to be done by hand
and would be a complete new technology for TYPO3. This technology afterwards would have to be
maintained and supported by the TYPO3 core team. This would cost a lot of more resources which
aren't available at the moment.

**Database columns and custom tables:**

The database columns will work out of the box, but if there are too many columns, this leads to
an error. But it is the most common way to store data in TYPO3 and it is the most stable solution
at the moment. To reduce the amount of columns, there is a possibility to reuse a column. So the
decision was to use the database columns technic.

**EAV (Entity Attribute Value):**

The EAV solution is like the JSON solution not implemented in TYPO3 at the moment. So this would
be a lot of work to implement this and due to lack of resources, this is not possible at the moment.


Why YAML?
=========

At the time this solution is met, the Form Framework and the routing was introduced a few years ago.
Despite the fact that Symfony was using away from YAML in this time, we stuck to this solution.

Reasons for YAML:

* YAML is a human readable format
* YAML is a very common format in the TYPO3 community
* The core provides a YAML parser
* YAML can import other YAML files, so complex structures can be split into multiple files
* YAML is a simple configuration format, without any logical stuff to learn

Why not PHP?

PHP would deliver a lot of possibilities, but it didn't fit to the vision of the content blocks:

* PHP is not a frontend technic
* In general, content blocks should be easy to understand and so there should not be any PHP code inside
* PHP is not a configuration format

Why not JSON?

JSON in fact is a good solution, but there are some reasons why we decided against JSON:

* JSON is a less used format in TYPO3
* JSON is not extendable or can import other files
* JSON does not support comments



Fusion with EXT:Mask - Nikita Hovratov joined the team
======================================================

In 2022, Nikita Hovratov from EXT:mask joined the team. He is the main developer of the mask extension
and has a lot of experience with the TYPO3 core and the issues which is addressed by the content blocks.
We decided to join forces and bring together the best of both worlds.


Developing for the core sysext
==============================

As we started to develop the system extension, we had to fit more how the core do things.
The content blocks extension is developed from scratch in a new way.

**Types are changed:**

We decided to adapt the types to the new TCA types in TYPO3 v12, so the amount of fields
to learn is reduced. This is because the TCA now supports much easier to use types.

**Separate the GUI from the core extension:**

While developing the system extension, there where several discussions to separate the GUI from
the core extensions. The reasons for this are:

* Mostly, you don't want to have the GUI in the production environment
* The GUI should not stuck to the release scheduling of the core, so we can add features faster
* There is the vision, that the GUI should be a website and/or a backend module
* Platform for sharing content blocks, where you can create and download content blocks

**Introduced an event after TCA is generated:**

To have a more modern way to extend the TCA, the content blocks extension listen to the
`TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent` event. This event is triggered when
TCA and TCA Overrides are compiled. So the content blocks TCA is rendered at the very end.
To extend or override the content blocks TCA, the content blocks extension provides an own
event to listen to:

`TYPO3\CMS\ContentBlocks\Event\AfterContentBlocksTcaCompilationEvent`

