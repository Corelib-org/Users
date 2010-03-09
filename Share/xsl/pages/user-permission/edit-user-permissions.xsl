<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
	<xsl:include href="../../../../../Base/share/xsl/base/layouts/default.xsl"/>
	<xsl:include href="../../base/user-permission.xsl"/>


	<xsl:template match="content" mode="xhtml-content">
		<xsl:call-template name="h1">
			<xsl:with-param name="headline">Edit user permissions</xsl:with-param>
			<xsl:with-param name="nav">
				<xsl:apply-templates select="user-editmodes" mode="view-selector">
					<xsl:with-param name="id" select="user/@id"/>
					<xsl:with-param name="select">permissions</xsl:with-param>
				</xsl:apply-templates>
			</xsl:with-param>
		</xsl:call-template>	
		<form method="post">
			<div>
				<script type="text/javascript">
					<xsl:comment>
						function moveOption(option, from, to){
							from.remove(option);
							to.add(new Option(option.text, option.value), null);
						}
					// </xsl:comment>
				</script>
				<table>
					<tr>
						<td style="border: 0px;">
							<h3>Available permissions</h3>
							<select style="width: 372px;" id="available" name="revoke[]" class="select" size="10">
								<xsl:for-each select="user-permission-list/user-permission">
									<xsl:if test="@id != /page/settings/user/user-permission-manager/user-permission/@id">
										<option value="id"><xsl:value-of select="@title"/></option>
									</xsl:if>
								</xsl:for-each>
							</select>
						</td>
						<td style="border: 0px;">
							<h3>Granted permissions</h3>
							<select style="width: 372px;" id="granted" class="select" size="10" ondblclick="moveOption(this.options[this.selectedIndex], this, $('available'));">
								<xsl:for-each select="/page/settings/user/user-permission-manager/user-permission">
									<option value="id"><xsl:value-of select="@title"/></option>
								</xsl:for-each>
							</select>
						</td>
					</tr>
				</table>
				<!-- Edit end -->
			</div>
			<input type="submit" class="button submit right"/>
		</form>
	</xsl:template>
</xsl:stylesheet>