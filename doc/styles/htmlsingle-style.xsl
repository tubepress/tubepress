<?xml version="1.0" encoding="UTF-8"?>

<!-- 
    Most of this stuff was yanked from the springframework build
 -->

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version='1.0'
 xmlns:xslthl="http://xslthl.sf.net"
                exclude-result-prefixes="xslthl"

>

	<xsl:import href="file:///opt/local/share/xsl/docbook-xsl/html/docbook.xsl" />

    <xsl:param name="base.dir">/Users/ehough/Documents/workspace/tubepress/doc/doc/html/</xsl:param>

    <!--  Extensions -->
	<xsl:param name="use.extensions">1</xsl:param>
    <xsl:param name="tablecolumns.extension">0</xsl:param>
    <xsl:param name="callout.extensions">1</xsl:param>
    
    <!-- Graphics -->
    <xsl:param name="callout.graphics" select="1" />
    <xsl:param name="callout.defaultcolumn">100</xsl:param>
    <xsl:param name="callout.graphics.path">images/callouts/</xsl:param>
    <xsl:param name="callout.graphics.extension">.gif</xsl:param>
    
    <xsl:param name="table.borders.with.css" select="1"/>
    <xsl:param name="html.stylesheet">css/stylesheet.css</xsl:param>
    <xsl:param name="html.stylesheet.type">text/css</xsl:param>
    <xsl:param name="generate.toc">book toc,title</xsl:param>

    <!-- Label Chapters and Sections (numbering) -->
    <xsl:param name="chapter.autolabel" select="1"/>
    <xsl:param name="section.autolabel" select="1"/>
    <xsl:param name="section.autolabel.max.depth" select="3"/>

    <xsl:param name="section.label.includes.component.label" select="1"/>
    <xsl:param name="table.footnote.number.format" select="'1'"/>
    
    <!-- Show only Sections up to level 1 in the TOCs -->
    <xsl:param name="toc.section.depth">1</xsl:param>
    
    <!-- Remove "Chapter" from the Chapter titles... -->
    <xsl:param name="local.l10n.xml" select="document('')"/>
    <l:i18n xmlns:l="http://docbook.sourceforge.net/xmlns/l10n/1.0">
        <l:l10n language="en">
            <l:context name="title-numbered">
                <l:template name="chapter" text="%n.&#160;%t"/>
                <l:template name="section" text="%n&#160;%t"/>
            </l:context>
        </l:l10n>
    </l:i18n>

<!-- Use code syntax highlighting -->
	<xsl:param name="highlight.source" select="1"/>
    
    <xsl:template match='xslthl:keyword'>
        <span class="hl-keyword"><xsl:value-of select='.'/></span>
    </xsl:template>

    <xsl:template match='xslthl:comment'>
        <span class="hl-comment"><xsl:value-of select='.'/></span>
    </xsl:template>

    <xsl:template match='xslthl:oneline-comment'>
        <span class="hl-comment"><xsl:value-of select='.'/></span>
    </xsl:template>

    <xsl:template match='xslthl:multiline-comment'>
        <span class="hl-multiline-comment"><xsl:value-of select='.'/></span>
    </xsl:template>

    <xsl:template match='xslthl:tag'>
        <span class="hl-tag"><xsl:value-of select='.'/></span>
    </xsl:template>

    <xsl:template match='xslthl:attribute'>
        <span class="hl-attribute"><xsl:value-of select='.'/></span>
    </xsl:template>

    <xsl:template match='xslthl:value'>
        <span class="hl-value"><xsl:value-of select='.'/></span>
    </xsl:template>
    
    <xsl:template match='xslthl:string'>
        <span class="hl-string"><xsl:value-of select='.'/></span>
    </xsl:template>
    

</xsl:stylesheet>
