.. include:: /Includes.rst.txt

.. _cb_parsefunc:

=================
lib.parseFunc_RTE
=================

In order to process links inside RTE fields, one needs to define a so called
:ref:`parseFunc <tsref:parsefunc>` TypoScript snippet. This snippet is shipped
in the Core, when you use the `fluid_styled_content` system extension. If you
only rely on Content Blocks, you need to define it on your own.

There are multiple options. You can just simply copy the following snippet from `fluid_styled_content`

.. code-block:: typoscript

    # Creates persistent ParseFunc setup for non-HTML content.
    lib.parseFunc {
        makelinks = 1
        makelinks {
            http {
                keep = {$styles.content.links.keep}
                extTarget = {$styles.content.links.extTarget}
            }
            mailto {
                keep = path
            }
        }
        tags {
            a = TEXT
            a {
                current = 1
                typolink {
                    parameter.data = parameters:href
                    title.data = parameters:title
                    ATagParams.data = parameters:allParams
                    # the target attribute takes precedence over config.intTarget
                    target.ifEmpty.data = parameters:target
                    # the target attribute takes precedence over the constant (styles.content.links.extTarget)
                    # which takes precedence over config.extTarget
                    # do not pass extTarget as reference, as it might not be set resulting in the string being
                    # written to the target attribute
                    extTarget.ifEmpty < config.extTarget
                    extTarget.ifEmpty.override = {$styles.content.links.extTarget}
                    extTarget.override.data = parameters:target
                }
            }
        }
        allowTags = {$styles.content.allowTags}
        denyTags = *
        # @deprecated since TYPO3 v12, remove with v13
        constants = 1
        nonTypoTagStdWrap {
            HTMLparser = 1
            HTMLparser {
                keepNonMatchedTags = 1
                htmlSpecialChars = 2
            }
        }
        htmlSanitize = 1
    }


    # Creates persistent ParseFunc setup for RTE content (which is mainly HTML) based on the "default" transformation.
    lib.parseFunc_RTE < lib.parseFunc
    lib.parseFunc_RTE {
        # Processing <ol>, <ul> and <table> blocks separately
        externalBlocks = article, aside, blockquote, div, dd, dl, footer, header, nav, ol, section, table, ul, pre, figure
        externalBlocks {
            ol {
                stripNL = 1
                stdWrap.parseFunc = < lib.parseFunc
            }
            ul {
                stripNL = 1
                stdWrap.parseFunc = < lib.parseFunc
            }
            pre {
                stdWrap.parseFunc < lib.parseFunc
            }
            table {
                stripNL = 1
                stdWrap {
                    HTMLparser = 1
                    HTMLparser {
                        tags.table.fixAttrib.class {
                            default = contenttable
                            always = 1
                            list = contenttable
                        }
                        keepNonMatchedTags = 1
                    }
                }
                HTMLtableCells = 1
                HTMLtableCells {
                    # Recursive call to self but without wrapping non-wrapped cell content
                    default.stdWrap {
                        parseFunc = < lib.parseFunc_RTE
                        parseFunc.nonTypoTagStdWrap.encapsLines {
                            nonWrappedTag =
                            innerStdWrap_all.ifBlank =
                        }
                    }
                    addChr10BetweenParagraphs = 1
                }
            }
            div {
                stripNL = 1
                callRecursive = 1
            }
            article < .div
            aside < .div
            figure < .div
            blockquote < .div
            footer < .div
            header < .div
            nav < .div
            section < .div
            dl < .div
            dd < .div
        }
        nonTypoTagStdWrap {
            HTMLparser = 1
            HTMLparser {
                keepNonMatchedTags = 1
                htmlSpecialChars = 2
            }
            encapsLines {
                encapsTagList = p,pre,h1,h2,h3,h4,h5,h6,hr,dt
                remapTag.DIV = P
                nonWrappedTag = P
                innerStdWrap_all.ifBlank = &nbsp;
            }
        }
    }

and substitute the constants with your own values. Just remember to look for
changes after major TYPO3 releases. There might be new or deprecated options.

Another option could be to use a snippet from a popular ready-to-go sitepackages
like `bootstrap_package`:

.. code-block:: typoscript

    ###################
    #### PARSEFUNC ####
    ###################
    lib.parseFunc {
        makelinks = 1
        makelinks {
            http {
                keep = path
                extTarget = _blank
            }
            mailto {
                keep = path
            }
        }
        tags {
            link = TEXT
            link {
                current = 1
                typolink {
                    parameter {
                        data = parameters : allParams
                    }
                    extTarget = _blank
                }
                parseFunc.constants = 1
            }
        }
        allowTags := addToList(a, abbr, acronym, address, article, aside, b, bdo)
        allowTags := addToList(big, blockquote, br, caption, center, cite, code, col)
        allowTags := addToList(colgroup, dd, del, dfn, dl, div, dt, em, font)
        allowTags := addToList(footer, header, h1, h2, h3, h4, h5, h6, hr, i, img)
        allowTags := addToList(ins, kbd, label, li, link, meta, nav, ol, p, pre, q)
        allowTags := addToList(samp, sdfield, section, small, span, strike, strong)
        allowTags := addToList(style, sub, sup, table, thead, tbody, tfoot, td, th)
        allowTags := addToList(tr, title, tt, u, ul, var)
        denyTags = *
        sword = <span class="text-highlight">|</span>
        constants = 1
        nonTypoTagStdWrap {
            HTMLparser = 1
            HTMLparser {
                keepNonMatchedTags = 1
                htmlSpecialChars = 2
            }
        }
    }


    #######################
    #### PARSEFUNC RTE ####
    #######################
    lib.parseFunc_RTE < lib.parseFunc
    lib.parseFunc_RTE {
        externalBlocks := addToList(article, aside, blockquote, div, dd, dl, footer)
        externalBlocks := addToList(header, nav, ol, section, table, ul)
        externalBlocks {
            blockquote {
                stripNL = 1
                callRecursive = 1
            }
            ol {
                stripNL = 1
                stdWrap {
                    parseFunc = < lib.parseFunc
                }
            }
            ul {
                stripNL = 1
                stdWrap {
                    parseFunc = < lib.parseFunc
                }
            }
            table {
                stripNL = 1
                stdWrap {
                    HTMLparser = 1
                    HTMLparser {
                        tags {
                            table {
                                fixAttrib {
                                    class {
                                        default = table
                                        always = 1
                                        list = table
                                    }
                                }
                            }
                        }
                        keepNonMatchedTags = 1
                    }
                     wrap = <div class="table-responsive">|</div>
                }
                HTMLtableCells = 1
                HTMLtableCells {
                    default.stdWrap {
                        parseFunc = < lib.parseFunc_RTE
                        parseFunc {
                            nonTypoTagStdWrap {
                                encapsLines {
                                    nonWrappedTag =
                                }
                            }
                        }
                    }
                    addChr10BetweenParagraphs = 1
                }
            }
            div {
                stripNL = 1
                callRecursive = 1
            }
            article < .div
            aside < .div
            footer < .div
            header < .div
            nav < .div
            section < .div
            dl < .div
            dd < .div
        }
        nonTypoTagStdWrap {
            encapsLines {
                encapsTagList = p, pre, h1, h2, h3, h4, h5, h6, hr, dt
                remapTag.DIV = P
                nonWrappedTag = P
                innerStdWrap_all.ifBlank = &nbsp;
            }
        }
        nonTypoTagStdWrap {
            HTMLparser = 1
            HTMLparser {
                keepNonMatchedTags = 1
                htmlSpecialChars = 2
            }
        }
    }

However, these tend to be out of date so you need to check yourself, if it does
fit your (security) needs.
