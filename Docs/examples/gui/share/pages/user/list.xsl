<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
	<xsl:include href="../../base/layouts/manager.xsl"/>
	<xsl:include href="../../base//user.xsl"/>

	<xsl:template match="content" mode="xhtml-content">
		<h1>User list</h1>
		<!-- List -->
		<xsl:apply-templates select="user-list" mode="xhtml-list"/>
		<!-- List end -->
	</xsl:template>
</xsl:stylesheet>