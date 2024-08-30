<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : project.xsl
    Created on : 23 August 2024, 12:04 am
    Author     : garys
    Description:
        Purpose of transformation follows.
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/project">
        <html>
            <body>
                <h2>Project: <xsl:value-of select="name"/></h2>
                <p>
                    <xsl:value-of select="description"/>
                </p>
                <h3>Tasks:</h3>
                <ul>
                    <xsl:for-each select="tasks/task">
                        <li>
                            <strong>
                                <xsl:value-of select="name"/>
                            </strong>: <xsl:value-of select="status"/>
                        </li>
                    </xsl:for-each>
                </ul>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
