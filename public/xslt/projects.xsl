<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html"/>
    
    <xsl:template match="/">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Budget</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <xsl:for-each select="/projects/project">
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
                            <td>$<xsl:value-of select="budget"/></td>
                            <td>
                                <xsl:choose>
                                    <xsl:when test="status = 'completed'">
                                        <span class="badge badge-success">
                                            <xsl:value-of select="status"/>
                                        </span>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <span class="badge badge-warning">
                                            <xsl:value-of select="status"/>
                                        </span>
                                    </xsl:otherwise>
                                </xsl:choose>
                                        
                            </td>
                        </tr>
                    </xsl:for-each>
                </tbody>
            </table>
        </div>
    </xsl:template>
</xsl:stylesheet>
