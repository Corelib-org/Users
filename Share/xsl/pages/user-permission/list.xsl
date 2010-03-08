<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
	<xsl:include href="../../../../../Base/share/xsl/base/layouts/default.xsl"/>
	<xsl:include href="../../base/user-permission.xsl"/>

	<xsl:template match="content" mode="xhtml-content">
		<xsl:call-template name="h1">
			<xsl:with-param name="headline">User permissions</xsl:with-param>
			<xsl:with-param name="nav">
				<label for="view"><a href="corelib/extensions/Users/Permissions/create/">Add permission</a></label>
			</xsl:with-param>
		</xsl:call-template>	
		<!-- List --> 
		<xsl:apply-templates select="user-permission-list" mode="xhtml-list"/>
		<!-- List end -->
	</xsl:template>
</xsl:stylesheet>