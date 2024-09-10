<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : project_transform.xsl
    Created on : 11 September 2024, 2:26 am
    Author     : garys
    Description:
        Purpose of transformation follows.
-->

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template match="/">
        <html>
            <head>
                <title>Projects List</title>
            </head>
            <body>
                <h1>Projects List</h1>
                <table border="1">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Budget</th>
                        <th>Status</th>
                        <th>Users</th>
                    </tr>
                    <xsl:for-each select="projects">
                        <tr>
                            <td>
                                <xsl:value-of select="id"/>
                            </td>
                            <td>
                                <xsl:value-of select="name"/>
                            </td>
                            <td>
                                <xsl:value-of select="description"/>
                            </td>
                            <td>
                                <xsl:value-of select="budget"/>
                            </td>
                            <td>
                                <xsl:value-of select="status"/>
                            </td>
                            <td>
                                <xsl:for-each select="users/user">
                                    <span>
                                        <xsl:value-of select="name"/> (<xsl:value-of select="role"/>)</span>
                                    <br/>
                                </xsl:for-each>
                            </td>
                        </tr>
                    </xsl:for-each>
                </table>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
