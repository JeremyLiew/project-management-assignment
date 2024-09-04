<?xml version="1.0" encoding="UTF-8"?>
<!-- Jeremy -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="actionToCount"/>

    <xsl:template match="/logs">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Action</th>
                    <th>Model Type</th>
                    <th>Model ID</th>
                    <th>User</th>
                    <th>Changes</th>
                    <th>Log Level</th>
                    <th>IP Address</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <xsl:for-each select="log">
                    <tr>
                        <xsl:if test="translate(action, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') = 'deleted'">
                            <xsl:attribute name="style">background-color: #ffdddd;</xsl:attribute>
                        </xsl:if>
                        <td><xsl:value-of select="id"/></td>
                        <td><xsl:value-of select="action"/></td>
                        <td><xsl:value-of select="model_type"/></td>
                        <td><xsl:value-of select="model_id"/></td>
                        <td><xsl:value-of select="user"/></td>
                        <td><pre><xsl:value-of select="changes"/></pre></td>
                        <td><xsl:value-of select="log_level"/></td>
                         <td><xsl:value-of select="ip_address"/></td>
                        <td><xsl:value-of select="created_at"/></td>
                    </tr>
                </xsl:for-each>
            </tbody>
        </table>

        <div>Total Logs: <xsl:value-of select="count(log)"/></div>

        <div>Number of '<xsl:value-of select="$actionToCount"/>' Actions:
            <xsl:value-of select="count(log[translate(action, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') = translate($actionToCount, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')])"/>
        </div>
    </xsl:template>
</xsl:stylesheet>
