<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
	<xsl:include href="../../../../../Base/share/xsl/base/layouts/default.xsl"/>
	<xsl:include href="../../base/user-setting.xsl"/>

	<xsl:template match="content" mode="xhtml-content">
		<xsl:call-template name="h1">
			<xsl:with-param name="headline">Edit user setting</xsl:with-param>
		</xsl:call-template>
		<form method="post">
			<div>
				<!-- Edit --> 
				<xsl:call-template name="user-setting-edit-value">
					<xsl:with-param name="ident" select="user-setting/@ident" />
					<xsl:with-param name="value" select="/page/settings/get/value|user-setting/@value" />
				</xsl:call-template>
				<!-- Edit end -->
			</div>
			<input type="submit" class="button submit right" value="Save changes"/>
		</form>
	</xsl:template>
</xsl:stylesheet>