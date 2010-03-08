<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
	<xsl:include href="../../../../../Base/share/xsl/base/layouts/default.xsl"/>
	<xsl:include href="../../base/user.xsl"/>


	<xsl:template match="content" mode="xhtml-content">
		<xsl:call-template name="h1">
			<xsl:with-param name="headline">Edit User</xsl:with-param>
		</xsl:call-template>
		
		<form method="post">
			<div>
				<!-- Edit --> 
				<xsl:call-template name="user-edit-username">
					<xsl:with-param name="username" select="/page/settings/get/username|user/@username" />
				</xsl:call-template>
				<xsl:call-template name="user-edit-password">
					<xsl:with-param name="password" select="/page/settings/get/password|user/@password" />
				</xsl:call-template>
				<xsl:call-template name="user-edit-email">
					<xsl:with-param name="email" select="/page/settings/get/email|user/@email" />
				</xsl:call-template>
				<xsl:call-template name="user-edit-activated">
					<xsl:with-param name="activated" select="/page/settings/get/activated|user/@activated" />
				</xsl:call-template>
				<!-- Edit end -->
			</div>
			<input type="submit" class="button submit right" value="Save changes"/>
		</form>
	</xsl:template>
</xsl:stylesheet>