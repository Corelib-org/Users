<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
	<xsl:include href="../../../../../Base/share/xsl/base/layouts/default.xsl"/>
	<xsl:include href="../../base/user-setting.xsl"/>
	
	<xsl:template match="content" mode="xhtml-content">
		<xsl:call-template name="h1">
			<xsl:with-param name="headline">User settings</xsl:with-param>
			<xsl:with-param name="nav">
				<label for="view"><img src="corelib/resource/manager/images/icons/generic/create.png" alt="add" title="Add user setting"/>&#160;<a href="corelib/extensions/Users/{user/@id}/Settings/add/">Add user setting</a>&#160;&#160;&#160;</label>
				<xsl:apply-templates select="user-editmodes" mode="view-selector">
					<xsl:with-param name="id" select="user/@id"/>
					<xsl:with-param name="select">settings</xsl:with-param>
				</xsl:apply-templates>
			</xsl:with-param>
		</xsl:call-template>
		<!-- List --> 
		<xsl:apply-templates select="user-setting-list" mode="xhtml-list"/>
		<!-- List end -->
	</xsl:template>
</xsl:stylesheet>