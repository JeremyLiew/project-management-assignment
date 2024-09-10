<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <!-- Define the template for the root -->
    <xsl:template match="/projects">
        <html>
            <head>
                <style>
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid black; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                <h1>Projects</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Description</th>
                            <th>Budget Amount</th>
                            <th>Total Spending</th>
                            <th>Completion Time (days)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:for-each select="project">
                            <tr>
                                <td><xsl:value-of select="name" /></td>
                                <td><xsl:value-of select="description" /></td>
                                <td><xsl:value-of select="budgetAmount" /></td>
                                <td><xsl:value-of select="totalCost" /></td>
                                <td><xsl:value-of select="Completion_project_time" /></td>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>

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
                        <xsl:for-each select="project/task">
                            <tr>
                                <td><xsl:value-of select="name" /></td>
                                <td><xsl:value-of select="../name" /></td>
                                <td><xsl:value-of select="cost" /></td>
                                <td><xsl:value-of select="created_at" /></td>
                                <td><xsl:value-of select="due_date" /></td>
                                <td><xsl:value-of select="Completion_task_time" /></td>
                                <td><xsl:value-of select="status" /></td>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
            </body>
        </html>
    </xsl:template>

</xsl:stylesheet>
