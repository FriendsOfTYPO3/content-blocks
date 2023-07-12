# TYPO3 Content Blocks

This is the standalone repository for the Content Blocks project. Content Blocks
provide a new API to provide custom Content Types like Content Elements, Page
Types or generic Record Types.

The repository is kept in sync with the pending Core patch on
[Gerrit](https://review.typo3.org/c/Packages/TYPO3.CMS/+/77518).

## Installation

This package can be only installed with composer right now. It is compatible
with TYPO3 v12.4 or current main only.

```
composer req contentblocks/content-blocks:@dev
```

## Usage

Refer to the Documentation on how to use the Content Blocks API

## Developing

There is a ddev setup ready to use. Ensure [ddev](https://github.com/ddev/ddev)
is installed on your machine. The run:

```
ddev start
ddev composer install
touch .Build/public/FIRST_INSTALL
ddev launch
```

Continue with the TYPO3 installation process.

## Testing

First install the composer dependencies:

```
composer install
```

Then run unit or functional tests by executing:

```
Build/Scripts/runTests.sh -s unit
Build/Scripts/runTests.sh -s functional
```

## Feedback

You can reach us on the TYPO3 Slack channel `#cig-structuredcontent`. We
appreciate any constructive feedback.

## FAQ

Q: Will the [content_blocks_reg_api](https://github.com/TYPO3-Initiatives/content-block-registration-api)
be further developed for TYPO3 v12?

A: The predecessor `content_blocks_reg_api`, which was initially created in 2020,
will not be further developed for TYPO3 v12. This system extension will replace
it completely. Migration steps will be provided as soon as we will reach a stable
development state.

This extension has been developed entirely from scratch and has, besides the
initial concept, nothing to do with the content_blocks_reg_api. The goal was to
create a system extension, which combines the best concepts from Content Blocks
and the [Mask](https://github.com/Gernott/mask) extension.
