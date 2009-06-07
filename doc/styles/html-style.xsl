<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version='1.0'>

	<xsl:import href="file:///opt/local/share/xsl/docbook-xsl/html/docbook.xsl" />

	<xsl:param name="use.extensions">1</xsl:param>
	<xsl:param name="tablecolumns.extension">0</xsl:param>
	<xsl:param name="callout.extensions">1</xsl:param>

	<xsl:param name="callout.graphics" select="1" />
	<xsl:param name="callout.defaultcolumn">100</xsl:param>
	<xsl:param name="callout.graphics.path">images/callouts/</xsl:param>
	<xsl:param name="callout.graphics.extension">.gif</xsl:param>

</xsl:stylesheet>
