<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
	<xsl:include href="../../../../../Base/share/xsl/base/layouts/default.xsl"/>
	<xsl:include href="../../base/user-setting.xsl"/>

	<xsl:template match="content" mode="xhtml-content">
		<h1>Create UserSetting</h1>
		<form method="post">
			<div>
				<!-- Create --> 
				<xsl:call-template name="user-setting-edit-ident">
					<xsl:with-param name="ident" select="/page/settings/get/ident" />
				</xsl:call-template>
				<xsl:call-template name="user-setting-edit-value">
					<xsl:with-param name="value" select="/page/settings/get/value" />
				</xsl:call-template>
				<!-- Create end -->
			</div>
			<input type="submit" class="button submit right"/>
		</form>
	</xsl:template>
</xsl:stylesheet>