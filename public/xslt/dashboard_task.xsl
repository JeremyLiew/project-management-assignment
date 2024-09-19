<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <!-- Define the template for the root -->
    <xsl:template match="/tasks">
        <html>
            <head>
                <style>
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid black; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .badge-success { background-color: green; color: white; padding: 5px; border-radius: 3px; }
                    .badge-warning { background-color: yellow; color: black; padding: 5px; border-radius: 3px; }
                    .badge-info { background-color: blue; color: white; padding: 5px; border-radius: 3px; }
                </style>
            </head>
            <body>


                    <h1>Tasks</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>Task Name</th>
                                <th>Project</th>
                                <th>Task Cost</th>
                                <th>Created Date</th>
                                <th>Due Date</th>
                                <th>Completion Time (days)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:apply-templates select="task"/>
                        </tbody>
                    </table>

            </body>
        </html>
    </xsl:template>

    <!-- Define the template for tasks -->
    <xsl:template match="task">
        <tr>
            <td><xsl:value-of select="name" /></td>
            <td><xsl:value-of select="../name" /></td>
            <td><xsl:value-of select="cost" /></td>
            <td><xsl:value-of select="created_at" /></td>
            <td><xsl:value-of select="due_date" /></td>
            <td><xsl:value-of select="Completion_task_time" /></td>
            <td>
                <xsl:choose>
                    <xsl:when test="status = 'Completed'">
                        <span class="badge badge-success">
                            <xsl:value-of select="status"/>
                        </span>
                    </xsl:when>
                    <xsl:when test="status = 'Pending'">
                        <span class="badge badge-warning">
                            <xsl:value-of select="status"/>
                        </span>
                    </xsl:when>
                    <xsl:otherwise>
                        <span class="badge badge-info">
                            <xsl:value-of select="status"/>
                        </span>
                    </xsl:otherwise>
                </xsl:choose>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>
