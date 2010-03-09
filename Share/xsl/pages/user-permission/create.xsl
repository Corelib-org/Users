<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
	<xsl:include href="../../../../../Base/share/xsl/base/layouts/default.xsl"/>
	<xsl:include href="../../base/user-permission.xsl"/>

	<xsl:template match="content" mode="xhtml-content">
		<xsl:call-template name="h1">
			<xsl:with-param name="headline">Add permission</xsl:with-param>
		</xsl:call-template>	
		<form method="post">
			<div>
				<!-- Create --> 
				<xsl:call-template name="user-permission-edit-ident">
					<xsl:with-param name="ident" select="/page/settings/get/ident" />
				</xsl:call-template>
				<xsl:call-template name="user-permission-edit-title">
					<xsl:with-param name="title" select="/page/settings/get/title" />
				</xsl:call-template>
				<xsl:call-template name="user-permission-edit-description">
					<xsl:with-param name="description" select="/page/settings/get/description" />
				</xsl:call-template>
				<!-- Create end -->
			</div>
			
			<input type="submit" class="button submit right" value="Add permission"/>
		</form>
	</xsl:template>
</xsl:stylesheet>